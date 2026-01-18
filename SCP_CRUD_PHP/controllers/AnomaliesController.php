<?php

require_once 'models/Anomalies.php';
require_once 'interfaces/IAnomaliesRepository.php';

class AnomaliesController
{
    private $repository;

    public function __construct(IAnomaliesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        $anomaliesList = $this->repository->getAll();
        require_once 'views/CRUD/anomalies/anomalies.php';
    }

    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        require_once 'views/CRUD/anomalies/anomaliesCreate.php';
    }

    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Sanitize input data from the form
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
            $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS);
            $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_SPECIAL_CHARS);
            $contencion = filter_input(INPUT_POST, 'contencion', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $doc_extensa = filter_input(INPUT_POST, 'doc_extensa', FILTER_SANITIZE_URL);
            $doc_extensa = !empty($doc_extensa) ? $doc_extensa : null;
            $id_sitio = filter_input(INPUT_POST, 'id_sitio', FILTER_VALIDATE_INT);
            $id_sitio = $id_sitio ? $id_sitio : null;

            // Validate input data and handle errors using session storage

            // Check for empty mandatory fields
            if (empty($id) || empty($nickname)) {
                $_SESSION['error'] = "MANDATORY FIELDS: ID and Nickname are required.";
                header("Location: index.php?action=anomalies_create");
                exit;
            }

            // Validate that the ID starts with 'SCP-' prefix
            $id = strtoupper($id);
            if (strpos($id, 'SCP-') !== 0) {
                $_SESSION['error'] = "PROTOCOL ERROR: The ID must strictly start with 'SCP-' (e.g., SCP-096).";
                header("Location: index.php?action=anomalies_create");
                exit;
            }

            // Validate site assignment protocol based on anomaly class
            if (empty($id_sitio) && !in_array($class, ['KETER', 'THAUMIEL'])) {
                $_SESSION['error'] = "PROTOCOL VIOLATION: A Containment Site is MANDATORY for $class class. Only KETER and THAUMIEL entities may have unknown locations.";
                header("Location: index.php?action=anomalies_create");
                exit;
            }

            // Handle image file upload
            $img_url = null;

            if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'views/CRUD/anomalies/assets/img/';

                // Create the upload directory if it does not exist
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0777, true)) {
                        $_SESSION['error'] = "SYSTEM ERROR: Cannot create upload directory. Check permissions.";
                        header("Location: index.php?action=anomalies_create");
                        exit;
                    }
                }

                $fileExtension = pathinfo($_FILES['img_file']['name'], PATHINFO_EXTENSION);
                $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '', $id);
                $fileName = $safeId . '_image.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['img_file']['tmp_name'], $targetPath)) {
                    $img_url = $targetPath;
                } else {
                    $_SESSION['error'] = "UPLOAD FAILED: Check folder permissions in Linux.";
                    header("Location: index.php?action=anomalies_create");
                    exit;
                }
            } else {
                $img_url_input = filter_input(INPUT_POST, 'img_url', FILTER_SANITIZE_URL);
                $img_url = !empty($img_url_input) ? $img_url_input : null;
            }

            // Create a new anomaly object with the collected data
            $anomaly = new Anomalies($id, $nickname, $class, $contencion, $description, $doc_extensa, $img_url, $id_sitio);

            try {
                $this->repository->create($anomaly);

                // Success: display alert and close the popup window
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

    public function edit($id)
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        $id = htmlspecialchars($id);

        $anomaly = $this->repository->getById($id);
        if ($anomaly) {
            require_once 'views/CRUD/anomalies/anomaliesEdit.php';
        } else {
            // If anomaly not found, close window with alert
            echo "<script>alert('Anomaly not found.'); window.close();</script>";
        }
    }

    public function update()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Retrieve the new and original IDs
            $newId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
            $originalId = $_POST['original_id'] ?? $newId; // Use original ID for redirection in case of errors

            // Validate input data using session for error messages

            // Validate ID format
            $newId = strtoupper($newId);
            if (strpos($newId, 'SCP-') !== 0) {
                $_SESSION['error'] = "PROTOCOL ERROR: The ID must start strictly with 'SCP-'.";
                header("Location: index.php?action=anomalies_edit&id=" . urlencode($originalId));
                exit;
            }

            // Sanitize other input fields
            $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS);
            $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_SPECIAL_CHARS);
            $contencion = filter_input(INPUT_POST, 'contencion', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $doc_extensa = filter_input(INPUT_POST, 'doc_extensa', FILTER_SANITIZE_URL);
            $doc_extensa = !empty($doc_extensa) ? $doc_extensa : null;
            $id_sitio = filter_input(INPUT_POST, 'id_sitio', FILTER_VALIDATE_INT);
            $id_sitio = $id_sitio ? $id_sitio : null;

            // Validate site assignment protocol based on anomaly class
            if (empty($id_sitio) && !in_array($class, ['KETER', 'THAUMIEL'])) {
                $_SESSION['error'] = "PROTOCOL VIOLATION: A Site is mandatory for $class class. Only KETER/THAUMIEL can be unassigned.";
                header("Location: index.php?action=anomalies_edit&id=" . urlencode($originalId));
                exit;
            }

            // Handle image update
            $img_url = filter_input(INPUT_POST, 'current_img', FILTER_SANITIZE_URL);

            if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'views/CRUD/anomalies/assets/img/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExtension = pathinfo($_FILES['img_file']['name'], PATHINFO_EXTENSION);
                $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '', $newId);
                $fileName = $safeId . '_image_' . time() . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['img_file']['tmp_name'], $targetPath)) {
                    if ($img_url && file_exists($img_url) && $img_url !== $targetPath) {
                        unlink($img_url);
                    }
                    $img_url = $targetPath;
                }
            }

            // Update the anomaly record
            $anomaly = new Anomalies($newId, $nickname, $class, $contencion, $description, $doc_extensa, $img_url, $id_sitio);

            try {
                $this->repository->update($anomaly, $originalId);

                // Success: close popup window
                echo "<script>
                        alert('Anomaly file updated successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (PDOException $e) {
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

    public function delete()
    {
        $this->verifyAuth();
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($id) {
            // Attempt to delete the associated image file from the filesystem
            $anomaly = $this->repository->getById($id);
            if ($anomaly && $anomaly->getImgUrl()) {
                $realPath = realpath($anomaly->getImgUrl());
                // Security check to prevent path traversal, ensuring deletion is limited to our image folder
                if ($realPath && file_exists($realPath) && strpos($realPath, 'views/CRUD/anomalies/assets/img/') !== false) {
                    unlink($realPath);
                }
            }

            try {
                $this->repository->delete($id);
            } catch (Exception $e) {
                // If deletion fails, store error message in session
                $_SESSION['error'] = "DELETION FAILED: " . $e->getMessage();
            }
        }

        header('Location: index.php?action=anomalies_index');
        exit;
    }

    private function verifyAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }
}
