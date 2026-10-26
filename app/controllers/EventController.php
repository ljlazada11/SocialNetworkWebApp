<?php
// app/controllers/EventController.php
// Controller managing campus events calendar and schedules

require_once __DIR__ . '/../models/EventModel.php';

class EventController {
    private $eventModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'You must be logged in to view campus events.';
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit;
        }
        $this->eventModel = new EventModel();
    }

    /**
     * Display the full campus events calendar page.
     */
    public function index() {
        $pageTitle = 'Campus Events Calendar';
        $upcomingEvents = $this->eventModel->getUpcomingEvents(50);
        $allEvents = $this->eventModel->getAllEvents();

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/events/index.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
