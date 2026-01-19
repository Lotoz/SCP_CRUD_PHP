<?php

require_once 'models/AssignedPersonnel.php';
require_once 'interfaces/IAssignedPersonnelRepository.php';

/**
 * Class AssignedPersonnelController
 * * Manages the Many-to-Many relationship between Personnel (Users) and SCPs.
 * This controller handles assigning staff to specific anomalies and defining their roles (e.g., 'Lead Researcher', 'Security').
 * * SECURITY: Requires Level 4 Clearance.
 */
class AssignedPersonnelController
{
    private $repository;

    public function __construct(IAssignedPersonnelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lists all current assignments.
     * Accessible only by authorized personnel.
     */
    public function index()
    {
        // Generate CSRF token for forms in the view
        $csrf_token = SessionManager::generateCSRFToken();

        // Security Check: Enforce Level 4 clearance
        $this->verifyAuth();

        // Retrieve all records and load the view
        $assignments = $this->repository->getAll();
        require_once 'views/CRUD/assigned/assigned.php';
    }

    /**
     * Shows the form to create a new assignment.
     * Intended to be opened in a pop-up window.
     */
    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        require_once 'views/CRUD/assigned/assignedCreate.php';
    }

    /**
     * Processes the creation of a new assignment (POST request).
     */
    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Sanitize input to prevent XSS and injection
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $scpId = filter_input(INPUT_POST, 'scp_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

            // 2. Validation: Ensure both IDs are present
            if (empty($userId) || empty($scpId)) {
                // Set error message in Session (Flash Message)
                $_SESSION['error'] = "Error: User ID and SCP ID are required.";
                // Redirect back to the form to show the alert
                header("Location: index.php?action=assigned_create");
                exit;
            }

            // 3. Create the Model object
            $assignment = new AssignedPersonnel($userId, $scpId, $role);

            try {
                // Attempt to save to the database
                $this->repository->create($assignment);

                // SUCCESS HANDLER:
                // Since this view is often opened in a popup/modal, we use JS to:
                // 1. Show a success alert.
                // 2. Refresh the parent window (the list).
                // 3. Close this popup.
                echo "<script>
                        alert('Personnel assigned successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (PDOException $e) {
                // ERROR HANDLER:
                // If it fails (e.g., User is already assigned to this SCP), set a generic error.
                // We hide the specific SQL error for security reasons.
                $_SESSION['error'] = "Not valid: Duplicate entry or Invalid IDs provided.";
                header("Location: index.php?action=assigned_create");
            }
            exit;
        }
    }

    /**
     * Shows the edit form.
     * NOTE: Since this is a Many-to-Many table, the Primary Key is composite (User ID + SCP ID).
     * We need both 'uid' and 'sid' from the URL to identify the record.
     */
    public function edit()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        // Retrieve composite keys from URL parameters
        $uid = isset($_GET['uid']) ? htmlspecialchars($_GET['uid']) : null;
        $sid = isset($_GET['sid']) ? htmlspecialchars($_GET['sid']) : null;

        $assignment = $this->repository->getByIds($uid, $sid);

        if ($assignment) {
            require_once 'views/CRUD/assigned/assignedEdit.php';
        } else {
            // If the record doesn't exist, redirect with error
            $_SESSION['error'] = "Assignment record not found.";
            header("Location: index.php?action=assigned_index");
            exit;
        }
    }

    /**
     * Updates an existing assignment (POST request).
     * Usually used to change the 'Role' of a user for a specific SCP.
     */
    public function update()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve data
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $scpId = filter_input(INPUT_POST, 'scp_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

            $assignment = new AssignedPersonnel($userId, $scpId, $role);

            try {
                $this->repository->update($assignment);

                // Success: Alert and close popup
                echo "<script>
                        alert('Assignment role updated.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                // Error: Redirect back to edit form with the specific IDs
                $_SESSION['error'] = "Action failed: Unable to update assignment role.";
                header("Location: index.php?action=assigned_edit&uid=$userId&sid=$scpId");
            }
            exit;
        }
    }

    /**
     * Deletes an assignment.
     * Uses POST to prevent accidental deletion via URL traversal.
     */
    public function delete()
    {
        $this->verifyAuth();

        $userId = filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_SPECIAL_CHARS);
        $scpId = filter_input(INPUT_POST, 'sid', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($userId && $scpId) {
            $this->repository->delete($userId, $scpId);
        }

        // Redirect back to the list
        header('Location: index.php?action=assigned_index');
        exit;
    }

    /**
     * Internal helper to verify Security Clearance.
     * Redirects to Dashboard if the user is below Level 4.
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
