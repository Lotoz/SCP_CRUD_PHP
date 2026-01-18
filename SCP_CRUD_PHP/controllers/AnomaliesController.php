<?php

require_once 'models/Anomalies.php';
require_once 'interfaces/IAnomaliesRepository.php';

/**
 * Controller class for managing SCP Anomalies.
 * Handles CRUD operations, input validation, file uploads, and authentication enforcement.
 */
class AnomaliesController
{
    private $repository;

    /**
     * Constructor using Dependency Injection for the repository.
     * @param IAnomaliesRepository $repository
     */
    public function __construct(IAnomaliesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Displays the main list of anomalies.
     */
    public function index()
    {
        // Generate CSRF token for form security in the view
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        // Retrieve all records to populate the table
        $anomaliesList = $this->repository->getAll();
        require_once 'views/CRUD/anomalies/anomalies.php';
    }

    /**
     * Displays the form to create a new anomaly.
     */
    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        require_once 'views/CRUD/anomalies/anomaliesCreate.php';
    }

    /**
     * Processes the creation form submission (POST).
     * Validates SCP protocols and handles file uploads.
     */
    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Sanitize all incoming POST data to prevent XSS/Injection
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
            $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS);
            $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_SPECIAL_CHARS);
            $contencion = filter_input(INPUT_POST, 'contencion', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);

            // Validate URL format for external docs (allow null if empty)
            $doc_extensa = filter_input(INPUT_POST, 'doc_extensa', FILTER_SANITIZE_URL);
            $doc_extensa = !empty($doc_extensa) ? $doc_extensa : null;

            // Validate integer for site ID
            $id_sitio = filter_input(INPUT_POST, 'id_sitio', FILTER_VALIDATE_INT);
            $id_sitio = $id_sitio ? $id_sitio : null;

            // 2. Business Logic & Validation

            // Ensure mandatory fields are present
            if (empty($id) || empty($nickname)) {
                $_SESSION['error'] = "MANDATORY FIELDS: ID and Nickname are required.";
                header("Location: index.php?action=anomalies_create");
                exit;
            }

            // Enforce SCP naming convention (Must start with "SCP-")
            $id = strtoupper($id);
            if (strpos($id, 'SCP-') !== 0) {
                $_SESSION['error'] = "PROTOCOL ERROR: The ID must strictly start with 'SCP-' (e.g., SCP-096).";
                header("Location: index.php?action=anomalies_create");
                exit;
            }

            // Enforce Containment Logic: Only specific classes can exist without a site
            if (empty($id_sitio) && !in_array($class, ['KETER', 'THAUMIEL'])) {
                $_SESSION['error'] = "PROTOCOL VIOLATION: A Containment Site is MANDATORY for $class class. Only KETER and THAUMIEL entities may have unknown locations.";
                header("Location: index.php?action=anomalies_create");
                exit;
            }

            // 3. Image File Handling
            $img_url = null;

            if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'views/CRUD/anomalies/assets/img/';

                // Auto-create directory if missing (Recursive permission 0777)
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true)) {
                        $_SESSION['error'] = "SYSTEM ERROR: Cannot create upload directory. Check permissions.";
                        header("Location: index.php?action=anomalies_create");
                        exit;
                    }
                }

                // Sanitize filename: use the SCP ID as the filename to avoid conflicts/weird chars
                $fileExtension = pathinfo($_FILES['img_file']['name'], PATHINFO_EXTENSION);
                $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '', $id);
                $fileName = $safeId . '_image.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;

                // Move file from temp to target
                if (move_uploaded_file($_FILES['img_file']['tmp_name'], $targetPath)) {
                    $img_url = $targetPath;
                } else {
                    $_SESSION['error'] = "UPLOAD FAILED: Check folder permissions in Linux.";
                    header("Location: index.php?action=anomalies_create");
                    exit;
                }
            } else {
                // Fallback: Check if a URL was provided instead of a file
                $img_url_input = filter_input(INPUT_POST, 'img_url', FILTER_SANITIZE_URL);
                $img_url = !empty($img_url_input) ? $img_url_input : null;
            }

            // 4. Persistence
            $anomaly = new Anomalies($id, $nickname, $class, $contencion, $description, $doc_extensa, $img_url, $id_sitio);

            try {
                $this->repository->create($anomaly);

                // UX: Close the popup window and refresh the parent window on success
                echo "<script>
                        alert('Anomaly registered successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                $_SESSION['error'] = "DATABASE ERROR: " . $e->getMessage();
                header("Location: index.php?action=anomalies_create");
                exit;
            }
            exit;
        }
    }

    /**
     * Displays the edit form for a specific anomaly.
     * @param string $id The SCP-ID to edit.
     */
    public function edit($id)
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        $id = htmlspecialchars($id); // Basic XSS protection for the output

        $anomaly = $this->repository->getById($id);
        if ($anomaly) {
            require_once 'views/CRUD/anomalies/anomaliesEdit.php';
        } else {
            // Handle case where ID doesn't exist (e.g., manual URL manipulation)
            echo "<script>alert('Anomaly not found.'); window.close();</script>";
        }
    }

    /**
     * Processes the update form submission (POST).
     * Handles ID changes, file replacements, and updates database.
     */
    public function update()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Capture new ID and the original ID (needed if we are renaming the SCP)
            $newId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
            $originalId = $_POST['original_id'] ?? $newId;

            // --- Validation Block (Similar to Create) ---

            $newId = strtoupper($newId);
            if (strpos($newId, 'SCP-') !== 0) {
                $_SESSION['error'] = "PROTOCOL ERROR: The ID must start strictly with 'SCP-'.";
                header("Location: index.php?action=anomalies_edit&id=" . urlencode($originalId));
                exit;
            }

            $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS);
            $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_SPECIAL_CHARS);
            $contencion = filter_input(INPUT_POST, 'contencion', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $doc_extensa = filter_input(INPUT_POST, 'doc_extensa', FILTER_SANITIZE_URL);
            $doc_extensa = !empty($doc_extensa) ? $doc_extensa : null;
            $id_sitio = filter_input(INPUT_POST, 'id_sitio', FILTER_VALIDATE_INT);
            $id_sitio = $id_sitio ? $id_sitio : null;

            // Enforce Site assignment logic
            if (empty($id_sitio) && !in_array($class, ['KETER', 'THAUMIEL'])) {
                $_SESSION['error'] = "PROTOCOL VIOLATION: A Site is mandatory for $class class. Only KETER/THAUMIEL can be unassigned.";
                header("Location: index.php?action=anomalies_edit&id=" . urlencode($originalId));
                exit;
            }

            // --- Image Update Logic ---
            $img_url = filter_input(INPUT_POST, 'current_img', FILTER_SANITIZE_URL);

            if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'views/CRUD/anomalies/assets/img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate new filename with timestamp to prevent browser caching issues
                $fileExtension = pathinfo($_FILES['img_file']['name'], PATHINFO_EXTENSION);
                $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '', $newId);
                $fileName = $safeId . '_image_' . time() . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['img_file']['tmp_name'], $targetPath)) {
                    // Cleanup: Delete the old image if it exists and is different from the new one
                    if ($img_url && file_exists($img_url) && $img_url !== $targetPath) {
                        unlink($img_url);
                    }
                    $img_url = $targetPath;
                }
            }

            // Update Object
            $anomaly = new Anomalies($newId, $nickname, $class, $contencion, $description, $doc_extensa, $img_url, $id_sitio);

            try {
                // Perform update (Repository handles the primary key change if needed)
                $this->repository->update($anomaly, $originalId);

                echo "<script>
                        alert('Anomaly file updated successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (PDOException $e) {
                // Handle duplicate ID error specifically (Code 23000)
                if ($e->getCode() == '23000') {
                    $_SESSION['error'] = "PROTOCOL ERROR: The ID '{$newId}' is already assigned to another anomaly.";
                } else {
                    $_SESSION['error'] = "DATABASE ERROR: " . $e->getMessage();
                }
                header("Location: index.php?action=anomalies_edit&id=" . urlencode($originalId));
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = "SYSTEM ERROR: " . $e->getMessage();
                header("Location: index.php?action=anomalies_edit&id=" . urlencode($originalId));
                exit;
            }
            exit;
        }
    }

    /**
     * Deletes an anomaly.
     * Expects ID via POST method for security (avoids accidental deletions via GET).
     */
    public function delete()
    {
        $this->verifyAuth();
        // Retrieve ID from POST body
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($id) {
            // Clean up: Delete associated physical image file before removing DB record
            $anomaly = $this->repository->getById($id);
            if ($anomaly && $anomaly->getImgUrl()) {
                $realPath = realpath($anomaly->getImgUrl());

                // Security: Verify the path is actually within our intended directory to prevent traversal attacks
                if ($realPath && file_exists($realPath) && strpos($realPath, 'views/CRUD/anomalies/assets/img/') !== false) {
                    unlink($realPath);
                }
            }

            try {
                $this->repository->delete($id);
            } catch (Exception $e) {
                $_SESSION['error'] = "DELETION FAILED: " . $e->getMessage();
            }
        }

        header('Location: index.php?action=anomalies_index');
        exit;
    }

    /**
     * Helper method to enforce user authentication.
     * Redirects to login if session is invalid.
     */
    private function verifyAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }
}
