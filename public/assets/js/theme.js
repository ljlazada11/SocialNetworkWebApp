/**
 * theme.js - Dark Mode / Light Mode toggle and persistence for SMCC Connect
 */
(function() {
    'use strict';

    var THEME_STORAGE_KEY = 'smcc_theme_preference';
    var THEME_DARK = 'dark';
    var THEME_LIGHT = 'light';

    /**
     * Get preferred theme from localStorage or system preference
     */
    function getStoredTheme() {
        var stored = localStorage.getItem(THEME_STORAGE_KEY);
        if (stored === THEME_DARK || stored === THEME_LIGHT) {
            return stored;
        }
        // Fallback to system preference if nothing stored
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return THEME_DARK;
        }
        return THEME_LIGHT;
    }

    /**
     * Apply theme to <html> element and update toggle button state
     */
    function setTheme(theme) {
        var root = document.documentElement;
        if (theme === THEME_DARK) {
            root.setAttribute('data-bs-theme', 'dark');
            root.classList.add('dark-theme');
        } else {
            root.setAttribute('data-bs-theme', 'light');
            root.classList.remove('dark-theme');
        }

        updateToggleButton(theme);
    }

    /**
     * Update the icon and tooltip / aria labels of the toggle button(s)
     */
    function updateToggleButton(theme) {
        var toggles = document.querySelectorAll('.theme-toggle-btn');
        toggles.forEach(function(btn) {
            var icon = btn.querySelector('.theme-toggle-icon');
            if (theme === THEME_DARK) {
                if (icon) {
                    icon.textContent = '☀️';
                    icon.classList.remove('bi-moon-stars-fill');
                    icon.classList.add('theme-icon-sun');
                }
                btn.setAttribute('title', 'Switch to Light Mode');
                btn.setAttribute('aria-label', 'Switch to Light Mode');
            } else {
                if (icon) {
                    icon.textContent = '🌙';
                    icon.classList.remove('theme-icon-sun');
                }
                btn.setAttribute('title', 'Switch to Dark Mode');
                btn.setAttribute('aria-label', 'Switch to Dark Mode');
            }
        });
    }

    /**
     * Toggle between dark and light themes
     */
    window.toggleTheme = function() {
        var currentTheme = document.documentElement.getAttribute('data-bs-theme') === THEME_DARK ? THEME_DARK : THEME_LIGHT;
        var newTheme = currentTheme === THEME_DARK ? THEME_LIGHT : THEME_DARK;
        try {
            localStorage.setItem(THEME_STORAGE_KEY, newTheme);
        } catch (e) {
            console.warn('localStorage not accessible for theme persistence', e);
        }
        setTheme(newTheme);
    };

    // Apply immediately to prevent flicker
    var initialTheme = getStoredTheme();
    setTheme(initialTheme);

    // Ensure buttons are updated once DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            var current = document.documentElement.getAttribute('data-bs-theme') === THEME_DARK ? THEME_DARK : THEME_LIGHT;
            updateToggleButton(current);
        });
    } else {
        updateToggleButton(initialTheme);
    }
})();
