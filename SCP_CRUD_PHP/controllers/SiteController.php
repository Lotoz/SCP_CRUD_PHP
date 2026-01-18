<?php

require_once 'models/Site.php';
require_once 'interfaces/ISiteRepository.php';

/**
 * SiteController - Containment Site Management
 *
 * Handles the creation and management of SCP Containment Sites.
 * STRICT SECURITY: Only Level 5 personnel (O5 Council/Site Directors) can access these functions.
 * Little note for curiosity: In this controller, the errores are shown using JavaScript alerts to ensure immediate visibility. Not use the $_SESSION method.
 * Because i wanna try something different and see how it works in practice.
 */
class SiteController
{
    private $repository;

    public function __construct(ISiteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lists all registered Containment Sites.
     */
    public function index()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth(); // Level 5 check
        $sitesList = $this->repository->getAll();
        require_once 'views/CRUD/sites/sites.php';
    }

    /**
     * Displays the form to establish a new Site.
     */
    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        require_once 'views/CRUD/sites/sitesCreate.php';
    }

    /**
     * Stores a new Site record (POST).
     * Validates that Name and Location are provided. Admin ID is optional.
     */
    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Sanitize Input
            $name = filter_input(INPUT_POST, 'name_sitio', FILTER_SANITIZE_SPECIAL_CHARS);
            $ubicacion = filter_input(INPUT_POST, 'ubicacion', FILTER_SANITIZE_SPECIAL_CHARS);

            // Admin ID is optional (some sites might be automated or pending assignment)
            $adminId = filter_input(INPUT_POST, 'id_administrador', FILTER_SANITIZE_SPECIAL_CHARS);
            $adminId = !empty($adminId) ? $adminId : null;

            if (empty($name) || empty($ubicacion)) {
                echo "<script>alert('Error: Name and Location are required.'); window.history.back();</script>";
                exit;
            }

            // Create Site Object (ID is null for new entries)
            $site = new Site(null, $name, $ubicacion, $adminId);

            try {
                $this->repository->create($site);
                echo "<script>
                        alert('Containment Site established successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Database Error: " . addslashes(htmlspecialchars($e->getMessage())) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    /**
     * Displays the edit form for a specific Site.
     * Validates that the Site ID is a valid integer before querying.
     */
    public function edit($id)
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        // Site IDs are Auto-Increment Integers, so we strictly validate the type
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            echo "<script>alert('Invalid Site ID.'); window.close();</script>";
            exit;
        }

        $site = $this->repository->getById($id);

        if (!$site) {
            echo "<script>alert('Site not found.'); window.close();</script>";
            exit;
        }

        require_once 'views/CRUD/sites/sitesEdit.php';
    }

    /**
     * Updates an existing Site (POST).
     */
    public function update()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $name = filter_input(INPUT_POST, 'name_sitio', FILTER_SANITIZE_SPECIAL_CHARS);
            $ubicacion = filter_input(INPUT_POST, 'ubicacion', FILTER_SANITIZE_SPECIAL_CHARS);
            $adminId = filter_input(INPUT_POST, 'id_administrador', FILTER_SANITIZE_SPECIAL_CHARS);
            $adminId = !empty($adminId) ? $adminId : null;

            if (!$id || empty($name) || empty($ubicacion)) {
                echo "<script>alert('Error: Invalid Data.'); window.history.back();</script>";
                exit;
            }

            $site = new Site($id, $name, $ubicacion, $adminId);

            try {
                $this->repository->update($site);
                echo "<script>
                        alert('Site protocols updated.');
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
     * Decomissions (Deletes) a Site.
     * Requires POST method to prevent accidental deletion via URL.
     */
    public function delete()
    {
        $this->verifyAuth();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            $this->repository->delete($id);
        }

        header('Location: index.php?action=sites_index');
        exit;
    }

    /**
     * Enforces Authorization.
     * STRICT: Level 5 Clearance is mandatory for any Site operation.
     */
    private function verifyAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 5) {
            $_SESSION['error'] = "Access Denied: Level 5 Clearance required for Site Management.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
