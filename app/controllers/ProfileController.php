<?php
// app/controllers/ProfileController.php
// Controller handling viewing and editing of the logged-in user profile

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PostModel.php';

class ProfileController {
    private $userModel;
    private $postModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
    }

    /**
     * Enforce authentication for profile actions.
     * Redirects unauthenticated visitors to login.
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'You must be logged in to access that page.';
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit;
        }
    }

    /**
     * View a user's profile and their posts.
     * Defaults to the logged-in user or accepts user_id / id query parameter.
     */
    public function view() {
        $this->requireAuth();

        $loggedInUserId = (int)$_SESSION['user_id'];
        $requestedUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : $loggedInUserId);
        $userId = ($requestedUserId > 0) ? $requestedUserId : $loggedInUserId;

        $user = $this->userModel->getProfileById($userId);

        if (!$user) {
            if ($userId === $loggedInUserId) {
                // If session exists but user row was deleted or not found
                unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['full_name']);
                $_SESSION['error_message'] = 'User profile not found. Please log in again.';
                header('Location: ' . BASE_URL . '/index.php?action=login');
                exit;
            } else {
                $_SESSION['error_message'] = 'User profile not found.';
                header('Location: ' . BASE_URL . '/index.php?action=profile');
                exit;
            }
        }

        // Retrieve only posts belonging to the profile user, ordered newest first
        $posts = $this->postModel->getPostsByUserId($userId, $loggedInUserId);

        $isOwnProfile = ($userId === $loggedInUserId);
        $pageTitle = $isOwnProfile ? 'My Profile' : $user['full_name'] . "'s Profile";
        require __DIR__ . '/../views/profile/profile.php';
    }

    /**
     * Show edit form or process edit submission for the logged-in user's profile.
     */
    public function edit() {
        $this->requireAuth();

        $userId = (int)$_SESSION['user_id'];
        $user = $this->userModel->getProfileById($userId);

        if (!$user) {
            unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['full_name']);
            $_SESSION['error_message'] = 'User profile not found. Please log in again.';
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit;
        }

        $errors = [];
        $formData = [
            'full_name' => $user['full_name'],
            'bio'       => $user['bio'] ?? ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $bio      = trim($_POST['bio'] ?? '');

            $formData['full_name'] = $fullName;
            $formData['bio']       = $bio;

            // 1. Validate Full Name
            if ($fullName === '') {
                $errors[] = 'Full Name is required.';
            } elseif (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 100) {
                $errors[] = 'Full Name must be between 2 and 100 characters.';
            }

            // 2. Validate Bio
            if (mb_strlen($bio) > 500) {
                $errors[] = 'Bio must not exceed 500 characters.';
            }

            // Normalize empty bio to null or empty string
            $cleanBio = ($bio === '') ? null : $bio;

            // 3. Process Profile Picture Upload (if uploaded)
            $newProfileImage = null;
            $removePicture = !empty($_POST['remove_picture']);

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $fileError = $_FILES['profile_image']['error'];
                if ($fileError !== UPLOAD_ERR_OK) {
                    $errors[] = 'Failed to upload profile picture. (Error code: ' . $fileError . ')';
                } else {
                    $tmpPath  = $_FILES['profile_image']['tmp_name'];
                    $fileName = $_FILES['profile_image']['name'];
                    $fileSize = $_FILES['profile_image']['size'];

                    // Max 2MB (2 * 1024 * 1024)
                    $maxSize = 2 * 1024 * 1024;
                    if ($fileSize > $maxSize) {
                        $errors[] = 'Profile picture must not exceed 2MB.';
                    }

                    // Validate extension against safe list
                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (!in_array($extension, $allowedExtensions, true)) {
                        $errors[] = 'Invalid image extension. Only JPG, PNG, GIF, and WEBP formats are allowed.';
                    }

                    // Validate MIME type via finfo and getimagesize
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $tmpPath);
                    finfo_close($finfo);

                    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($mimeType, $allowedMimes, true)) {
                        $errors[] = 'Invalid image file type. Please upload a real image.';
                    }

                    $imageInfo = @getimagesize($tmpPath);
                    if ($imageInfo === false) {
                        $errors[] = 'Uploaded file is not a valid image.';
                    }

                    // If file is valid, generate safe unique filename
                    if (empty($errors)) {
                        $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $safeFileName = 'avatar_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
                        $destination  = $uploadDir . $safeFileName;

                        if (move_uploaded_file($tmpPath, $destination)) {
                            $newProfileImage = $safeFileName;

                            // Clean up previous image file if it exists in uploads/avatars/
                            if (!empty($user['profile_image'])) {
                                $oldFilePath = $uploadDir . basename($user['profile_image']);
                                if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                                    @unlink($oldFilePath);
                                }
                            }
                        } else {
                            $errors[] = 'Could not save the uploaded image file. Please check folder permissions.';
                        }
                    }
                }
            } elseif ($removePicture) {
                // User opted to remove existing picture
                if (!empty($user['profile_image'])) {
                    $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
                    $oldFilePath = $uploadDir . basename($user['profile_image']);
                    if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }
                $newProfileImage = ''; // flag to set null in db
            }

            // 4. If all validations pass, save changes to DB
            if (empty($errors)) {
                $updated = $this->userModel->updateProfile($userId, $fullName, $cleanBio);

                if ($newProfileImage !== null) {
                    $imageToSave = ($newProfileImage === '') ? null : $newProfileImage;
                    $this->userModel->updateProfileImage($userId, $imageToSave);
                }

                if ($updated !== false) {
                    // Update session display name if it changed
                    $_SESSION['full_name'] = $fullName;
                    $_SESSION['success_message'] = 'Your profile has been updated successfully!';
                    header('Location: ' . BASE_URL . '/index.php?action=profile');
                    exit;
                } else {
                    $errors[] = 'Failed to update profile. Please try again.';
                }
            }
        }

        $pageTitle = 'Edit Profile';
        require __DIR__ . '/../views/profile/edit.php';
    }
}
