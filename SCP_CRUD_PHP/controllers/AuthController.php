<?php

require_once 'models/User.php';
require_once 'interfaces/ILoginUserRepository.php';
require_once 'config/Database.php';

/**
 * AuthController - Authentication Controller
 *
 * Handles user authentication, session management, and registration.
 * Implements security measures against Brute Force (locking), Session Fixation, and Path Traversal.
 */
class AuthController
{
    private $userRepository;
    private $taskRepository;

    public function __construct(ILoginUserRepository $userRepository, ITaskRepository $taskRepository)
    {
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Displays the login form.
     * Redirects to dashboard if the user is already logged in.
     */
    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?action=dashboard');
            exit();
        }
        $csrf_token = SessionManager::generateCSRFToken();
        include 'views/login.php';
    }

    /**
     * Processes the login submission (POST).
     * Validates credentials, manages account locking, and sets up the session.
     */
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit();
        }

        // 1. Validate Input
        // Note: We do NOT sanitize passwords, as special chars are valid in passwords.
        $id = filter_input(INPUT_POST, 'user', FILTER_DEFAULT);
        $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);

        if (!$id || !$password) {
            $_SESSION['error'] = 'Username and password are required.';
            header('Location: index.php?action=login');
            exit();
        }

        $id = trim($id);

        try {
            $user = $this->userRepository->getById($id);

            // Security: Use generic error message to prevent User Enumeration
            if (!$user) {
                $_SESSION['error'] = 'Invalid credentials.';
                header('Location: index.php?action=login');
                exit();
            }

            // Check if account is locked
            if (!$user->isstate()) {
                $_SESSION['error'] = 'Account locked. Contact Administration.';
                header('Location: index.php?action=login');
                exit();
            }

            // Verify Password
            if (!$user->verificarPassword($password)) {
                // Increment failed attempts
                $attempts = $user->getTryAttempts() + 1;
                $this->userRepository->updateAttempts($id, $attempts);

                // Lock account if threshold reached (5 attempts)
                if ($attempts >= 5) {
                    $this->userRepository->updateState($id); // Sets state to false (locked)
                    $_SESSION['error'] = 'Account locked due to excessive login attempts.';
                } else {
                    $_SESSION['error'] = 'Invalid credentials.';
                }
                header('Location: index.php?action=login');
                exit();
            }

            // Success: Reset attempts and regenerate Session ID (prevents Session Fixation)
            $this->userRepository->resetAttempts($id);
            session_regenerate_id(true);

            // Initialize Session Variables
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['name']    = $user->getFullName();
            $_SESSION['level']   = $user->getLevel();
            $_SESSION['rol']     = $user->getRol();

            // Security: Whitelist themes to prevent Path Traversal via the theme cookie/session
            $allowedThemes = ['gears', 'unicorn', 'ice', 'admin', 'clef', 'sophie'];
            $theme = $user->getTheme();
            $_SESSION['theme'] = in_array($theme, $allowedThemes) ? $theme . '.css' : 'gears.css';

            header('Location: index.php?action=dashboard');
            exit();
        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            $_SESSION['error'] = 'System Error.';
            header('Location: index.php?action=login');
            exit();
        }
    }

    /**
     * Displays the dashboard (Main Page).
     * Protected: Requires active session.
     */
    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Fetch pending tasks for the logged-in user
        $tasks = $this->taskRepository->getNotCompletedTasks($userId);

        require_once 'views/dashboard.php';
    }

    /**
     * Logs the user out.
     * Destroys session data and redirects to login.
     */
    public function logout()
    {
        SessionManager::logout();
        header('Location: index.php?action=login');
        exit();
    }

    /**
     * Displays the registration form.
     */
    public function register()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        include 'views/register.php';
    }

    /**
     * Processes the registration submission (POST).
     * Validates input rules and creates a new user with default 'Cleaner' role.
     */
    public function registerProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=register');
            exit();
        }

        // Sanitize Input
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation Rules
        if (!$id || !$name || !$lastname || !$email || !$password) {
            $_SESSION['error'] = 'All fields are required and must be valid.';
            header('Location: index.php?action=register');
            exit();
        }

        // Regex: Allow only alphanumeric, underscores, and hyphens for ID
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
            $_SESSION['error'] = 'ID contains invalid characters.';
            header('Location: index.php?action=register');
            exit();
        }

        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Passwords do not match.';
            header('Location: index.php?action=register');
            exit();
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password too short (min 8 chars).';
            header('Location: index.php?action=register');
            exit();
        }

        try {
            // Check for duplicates
            if ($this->userRepository->getById($id)) {
                $_SESSION['error'] = 'ID already taken.';
                header('Location: index.php?action=register');
                exit();
            }

            if ($this->userRepository->getByEmail($email)) {
                $_SESSION['error'] = 'Email already exists.';
                header('Location: index.php?action=register');
                exit();
            }

            // Create User (Default Role: Cleaner, Level: 1, Theme: gears)
            $user = new User($id, $name, $lastname, $email, $password, 1, 'cleaner', 'gears');

            if ($this->userRepository->save($user)) {
                $_SESSION['success'] = 'Registered successfully.';
                header('Location: index.php?action=login');
                exit();
            } else {
                $_SESSION['error'] = 'Database error.';
                header('Location: index.php?action=register');
                exit();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'System Error.';
            header('Location: index.php?action=register');
            exit();
        }
    }
}
