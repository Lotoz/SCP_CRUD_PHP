<?php

require_once 'models/AssignedPersonnel.php';
require_once 'interfaces/IAssignedPersonnelRepository.php';

/**
 * Controller for managing assignments between Personnel (Users) and SCPs.
 * Handles the Many-to-Many relationship logic and Role updates.
 */
class AssignedPersonnelController
{
    private $repository;

    public function __construct(IAssignedPersonnelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lists all active personnel assignments.
     */
    public function index()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth(); // Enforces Level 4 check
        $assignments = $this->repository->getAll();
        require_once 'views/CRUD/assigned/assigned.php';
    }

    /**
     * Opens the assignment creation form.
     */
    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        require_once 'views/CRUD/assigned/assignedCreate.php';
    }

    /**
     * Stores a new assignment (POST).
     * Links a User to an SCP with a specific role.
     */
    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Sanitize inputs
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $scpId = filter_input(INPUT_POST, 'scp_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

            // Validate mandatory relationship fields
            if (empty($userId) || empty($scpId)) {
                echo "<script>alert('Error: User ID and SCP ID are required.'); window.history.back();</script>";
                exit;
            }

            $assignment = new AssignedPersonnel($userId, $scpId, $role);

            try {
                $this->repository->create($assignment);
                // UX: Refresh parent window to show new data, then close popup
                echo "<script>
                        alert('Personnel assigned successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (PDOException $e) {
                // Handle duplicate assignments (Composite Primary Key violation)
                if (strpos($e->getMessage(), '23000') !== false) {
                    echo "<script>alert('Error: Duplicate entry or Invalid IDs.'); window.history.back();</script>";
                } else {
                    echo "<script>alert('Database Error: " . addslashes(htmlspecialchars($e->getMessage())) . "'); window.history.back();</script>";
                }
            }
            exit;
        }
    }

    /**
     * Opens the edit form.
     * Requires BOTH User ID and SCP ID to identify the record (Composite Key).
     */
    public function edit()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        // Sanitize GET parameters used for lookups
        $uid = isset($_GET['uid']) ? htmlspecialchars($_GET['uid']) : null;
        $sid = isset($_GET['sid']) ? htmlspecialchars($_GET['sid']) : null;

        $assignment = $this->repository->getByIds($uid, $sid);

        if ($assignment) {
            require_once 'views/CRUD/assigned/assignedEdit.php';
        } else {
            echo "<script>alert('Assignment record not found.'); window.close();</script>";
        }
    }

    /**
     * Updates an existing assignment (POST).
     * Typically updates the 'Role' while keeping IDs constant.
     */
    public function update()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // IDs are retrieved to identify the row, but usually not changed here
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $scpId = filter_input(INPUT_POST, 'scp_id', FILTER_SANITIZE_SPECIAL_CHARS);
            // The Role is the main editable field in this context
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

            $assignment = new AssignedPersonnel($userId, $scpId, $role);

            try {
                $this->repository->update($assignment);
                echo "<script>
                        alert('Assignment role updated.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes(htmlspecialchars($e->getMessage())) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    /**
     * Deletes an assignment.
     * Requires both User ID (uid) and SCP ID (sid) via POST.
     */
    public function delete()
    {
        $this->verifyAuth();

        $userId = filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_SPECIAL_CHARS);
        $scpId = filter_input(INPUT_POST, 'sid', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($userId && $scpId) {
            $this->repository->delete($userId, $scpId);
        }

        header('Location: index.php?action=assigned_index');
        exit;
    }

    /**
     * Enforces Authorization.
     * STRICT: Only users with Clearance Level 4 or higher can manage assignments.
     */
    private function verifyAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 4) {
            $_SESSION['error'] = "Access Denied: Level 4 Clearance required.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
