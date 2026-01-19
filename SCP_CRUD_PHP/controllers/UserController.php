<?php

require_once 'models/User.php';
require_once 'interfaces/IUserRepository.php';

/**
 * UserController - Personnel Management
 *
 * Handles the administration of system users (personnel).
 * STRICT SECURITY: Only Level 5 (O5 Council/Site Directors) can access these functions.
 */
class UserController
{
    private $repository;

    public function __construct(IUserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lists all registered personnel.
     */
    public function index()
    {
        // Generate CSRF token for the view to use in forms
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAdminAuth(); // Enforce Level 5 check
        $usersList = $this->repository->getAll();
        require_once 'views/CRUD/users/users.php';
    }

    /**
     * Displays the user creation form.
     */
    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAdminAuth();
        require_once 'views/CRUD/users/usersCreate.php';
    }

    /**
     * Stores a new user (POST).
     * Includes strict validation for ID format, Email, and Clearance Levels.
     */
    public function store()
    {
        $this->verifyAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Sanitize Inputs to prevent XSS and injection
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'] ?? ''; // Password handled raw here, hashed in Repository
            $rol = filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_SPECIAL_CHARS);
            $level = filter_input(INPUT_POST, 'level', FILTER_VALIDATE_INT);
            $theme = filter_input(INPUT_POST, 'theme', FILTER_SANITIZE_SPECIAL_CHARS);
            $state = isset($_POST['state']) ? 1 : 0; // Checkbox logic

            // --- Validation Block (Errors stored in Session) ---

            // Validate Mandatory Fields
            if (empty($id) || empty($password)) {
                $_SESSION['error'] = "MANDATORY FIELDS: Operative ID and Password are required.";
                header("Location: index.php?action=users_create");
                exit;
            }

            // Validate ID Format (Alphanumeric + Underscore/Hyphen only)
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
                $_SESSION['error'] = 'INVALID FORMAT: ID contains invalid characters.';
                header('Location: index.php?action=users_create');
                exit();
            }

            // Validate Clearance Level Range
            if ($level === false || $level < 0 || $level > 10) {
                $_SESSION['error'] = "PROTOCOL ERROR: The clearance level must be an integer between 0 and 5.(or less than 10)";
                header("Location: index.php?action=users_create");
                exit;
            }

            // Validate Email Format
            if (!$email) {
                $_SESSION['error'] = "INVALID FORMAT: The provided email address is not valid.";
                header("Location: index.php?action=users_create");
                exit;
            }

            // Create Object
            $user = new User($id, $name, $lastname, $email, $password, $level, $rol, $theme);
            $user->setstate($state);

            try {
                $this->repository->create($user);

                // SUCCESS: Use JS to close the popup and refresh parent window
                echo "<script>
                        alert('Personnel registered successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (PDOException $e) {
                // Handle Duplicate Key Error (SQLState 23000)
                if ($e->getCode() == '23000') {
                    $_SESSION['error'] = "DUPLICATE ENTRY: The ID '$id' is already in use.";
                } else {
                    $_SESSION['error'] = "DATABASE ERROR. Please try again later. ";
                }
                header("Location: index.php?action=users_create");
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = "SYSTEM ERROR. Could not register personnel. ";
                header("Location: index.php?action=users_create");
                exit;
            }
            exit;
        }
    }

    /**
     * Displays the edit form for a specific user.
     */
    public function edit($id)
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAdminAuth();

        $id = htmlspecialchars($id);

        $user = $this->repository->getById($id);
        if ($user) {
            require_once 'views/CRUD/users/usersEdit.php';
        } else {
            echo "<script>alert('User not found.'); window.close();</script>";
        }
    }

    /**
     * Updates an existing user (POST).
     * Allows empty password field (implies no change).
     */
    public function update()
    {
        $this->verifyAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Sanitize Inputs
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            // Password logic: If empty, the repository will know not to overwrite the old hash
            $password = $_POST['password'] ?? '';

            $rol = filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_SPECIAL_CHARS);
            $level = filter_input(INPUT_POST, 'level', FILTER_VALIDATE_INT);
            $theme = filter_input(INPUT_POST, 'theme', FILTER_SANITIZE_SPECIAL_CHARS);
            $state = isset($_POST['state']) ? 1 : 0;

            // 2. Validation
            if (!$email) {
                $_SESSION['error'] = "INVALID FORMAT: The provided email address is not valid.";
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit;
            }

            if ($level === false || $level < 0 || $level > 5) {
                $_SESSION['error'] = "PROTOCOL ERROR: The clearance level must be an integer between 0 and 5.";
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit;
            }

            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
                $_SESSION['error'] = 'INVALID FORMAT: ID contains invalid characters.';
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit();
            }

            // Create Object for update
            $user = new User($id, $name, $lastname, $email, $password, $level, $rol, $theme);
            $user->setstate($state);

            try {
                $this->repository->update($user);

                // SUCCESS: Close popup
                echo "<script>
                        alert('Personnel file updated successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (PDOException $e) {
                $_SESSION['error'] = "DATABASE ERROR: " . $e->getMessage();
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = "UPDATE FAILED: " . $e->getMessage();
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit;
            }
            exit;
        }
    }

    /**
     * Deletes a user record.
     * Prevents admins from deleting their own account to avoid lockouts.
     */
    public function delete()
    {
        $this->verifyAdminAuth();

        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        // Security: Prevent self-deletion
        if ($id === $_SESSION['user_id']) {
            $_SESSION['error'] = "PROTOCOL VIOLATION: You cannot expunge your own record.";
        } else if ($id) {
            try {
                $this->repository->delete($id);
            } catch (Exception $e) {
                $_SESSION['error'] = "DELETE FAILED: " . $e->getMessage();
            }
        }

        header('Location: index.php?action=users_index');
        exit;
    }

    /**
     * Enforces Authorization.
     * STRICT: Level 5 Clearance is mandatory for any User Management operation.
     */
    private function verifyAdminAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 5) {
            $_SESSION['error'] = "ACCESS DENIED: Clearance Level 5 required.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
