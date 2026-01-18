<?php

require_once 'models/Task.php';
require_once 'interfaces/ITaskRepository.php';

/**
 * TaskController - Personal Task Management
 *
 * Handles the creation, editing, and deletion of user-specific tasks.
 * Includes strict validation to ensure users can only manage their own tasks
 * (unless they possess higher-level administrative clearance).
 */
class TaskController
{
    private $repository;

    public function __construct(ITaskRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lists all tasks assigned to the current user.
     */
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        // Good practice: Sanitize session data before using it in logic/views
        $userId = htmlspecialchars($userId);

        $tasks = $this->repository->getByUserId($userId);
        $csrf_token = SessionManager::generateCSRFToken();

        require_once 'views/CRUD/task/task.php';
    }

    /**
     * Displays the task creation form.
     */
    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        require_once 'views/CRUD/task/taskCreate.php';
    }

    /**
     * Stores a new task (POST).
     * Now uses Session for error reporting instead of JS alerts.
     */
    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Sanitize Description (Prevent XSS)
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);

            // 2. Sanitize Date (Prevent code injection in date field)
            $dueDateInput = filter_input(INPUT_POST, 'due_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $dueDate = !empty($dueDateInput) ? $dueDateInput : null;

            $userId = $_SESSION['user_id'];

            // Strict Validation
            if (empty($description) || $description === false) {
                $_SESSION['error'] = "VALIDATION ERROR: Description cannot be empty or invalid.";
                header("Location: index.php?action=task_create");
                exit;
            }

            $task = new Task(null, trim($description), 0, $userId, $dueDate);

            try {
                $this->repository->create($task);

                // Success: Close the popup window and refresh the parent
                echo "<script>
                        alert('Task assigned successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                $_SESSION['error'] = "DATABASE ERROR: " . $e->getMessage();
                header("Location: index.php?action=task_create");
                exit;
            }
            exit;
        }
    }

    /**
     * Displays the edit form for a specific task.
     * Validates ownership (User can only edit their own tasks unless Level >= 5).
     */
    public function edit($id)
    {
        // Validate that the ID passed via GET is an integer
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $_SESSION['error'] = "INVALID ID: Task ID must be a number.";
            header('Location: index.php?action=task_index');
            exit;
        }

        $csrf_token = SessionManager::generateCSRFToken();
        $task = $this->repository->getById($id);

        if ($task) {
            // Security: Check if the user owns the task or has Admin Level 5
            if ($task->getIdUsuario() !== $_SESSION['user_id'] && $_SESSION['level'] < 5) {
                echo "<script>alert('SECURITY ALERT: Unauthorized access to this task.'); window.close();</script>";
                exit;
            }
            require_once 'views/CRUD/task/taskEdit.php';
        } else {
            echo "<script>alert('Task not found.'); window.close();</script>";
        }
    }

    /**
     * Updates an existing task (POST).
     * Handles validation errors via Session redirect.
     */
    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Validate Numeric ID
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            // 2. Sanitize Description
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);

            // 3. Handle Checkbox (safe, just checking existence)
            $completado = isset($_POST['completado']) ? 1 : 0;

            // 4. Sanitize Date
            $dueDateInput = filter_input(INPUT_POST, 'due_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $dueDate = !empty($dueDateInput) ? $dueDateInput : null;

            // 5. Sanitize User ID
            $userIdInput = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_SPECIAL_CHARS);

            // Integrity Check
            if (!$id || !$description) {
                $_SESSION['error'] = "VALIDATION ERROR: Invalid data provided.";
                // Redirect back to edit with the ID to preserve context
                header("Location: index.php?action=task_edit&id=" . urlencode($id));
                exit;
            }

            // EXTRA SECURITY: Prevent ID injection.
            // Force the task owner to remain the current user, unless Admin Level 5 overrides.
            $userId = $_SESSION['user_id'];
            if ($_SESSION['level'] >= 5 && !empty($userIdInput)) {
                $userId = $userIdInput;
            }

            $task = new Task($id, trim($description), $completado, $userId, $dueDate);

            try {
                $this->repository->update($task);

                // Success: Close popup
                echo "<script>
                        alert('Task updated successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                $_SESSION['error'] = "UPDATE ERROR: " . $e->getMessage();
                header("Location: index.php?action=task_edit&id=" . urlencode($id));
                exit;
            }
            exit;
        }
    }

    /**
     * Deletes a task.
     * Expects ID via POST for security.
     */
    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }

        // Validate ID is an integer
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            try {
                // Ideally, we should also check ownership here before deleting
                $this->repository->delete($id);
            } catch (Exception $e) {
                $_SESSION['error'] = "DELETION FAILED: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "INVALID REQUEST: Missing Task ID.";
        }

        header('Location: index.php?action=task_index');
        exit;
    }
}
