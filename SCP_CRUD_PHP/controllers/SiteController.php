<?php

require_once 'models/Site.php';
require_once 'interfaces/ISiteRepository.php';

/**
 * Class SiteController
 * * Handles the CRUD operations for SCP Containment Sites.
 * Sites are critical infrastructure, so this controller enforces strict security.
 * * SECURITY: Requires Level 5 Clearance (O5 Council / Site Directors).
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
        $this->verifyAuth(); // Strict check
        $sitesList = $this->repository->getAll();
        require_once 'views/CRUD/sites/sites.php';
    }

    /**
     * Opens the form to commission a new Site.
     */
    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        require_once 'views/CRUD/sites/sitesCreate.php';
    }

    /**
     * Stores a new Site in the database.
     */
    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Sanitize Inputs
            $name = filter_input(INPUT_POST, 'name_sitio', FILTER_SANITIZE_SPECIAL_CHARS);
            $ubicacion = filter_input(INPUT_POST, 'ubicacion', FILTER_SANITIZE_SPECIAL_CHARS);

            // Administrator ID is optional (Site might not have a Director yet)
            $adminId = filter_input(INPUT_POST, 'id_administrador', FILTER_SANITIZE_SPECIAL_CHARS);
            $adminId = !empty($adminId) ? $adminId : null;

            // 2. Validation
            if (empty($name) || empty($ubicacion)) {
                $_SESSION['error'] = "Error: Name and Location are required.";
                header("Location: index.php?action=sites_create");
                exit;
            }

            // Create Site Object (ID is null because it's auto-increment)
            $site = new Site(null, $name, $ubicacion, $adminId);

            try {
                $this->repository->create($site);

                // Success: Close the popup
                echo "<script>
                        alert('Containment Site established successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                // Error: Redirect to create form
                $_SESSION['error'] = "Not valid, try again.";
                header("Location: index.php?action=sites_create");
            }
            exit;
        }
    }

    /**
     * Opens the edit form.
     * Validates that the ID passed in the URL is a valid Integer.
     */
    public function edit($id)
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        // Security: Ensure ID is an integer to prevent SQL injection attempts via URL
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $_SESSION['error'] = "Invalid Site ID.";
            header("Location: index.php?action=sites_index");
            exit;
        }

        $site = $this->repository->getById($id);

        if (!$site) {
            $_SESSION['error'] = "Site not found.";
            header("Location: index.php?action=sites_index");
            exit;
        }

        require_once 'views/CRUD/sites/sitesEdit.php';
    }

    /**
     * Updates site protocols/information.
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

            // Validate mandatory fields
            if (!$id || empty($name) || empty($ubicacion)) {
                $_SESSION['error'] = "Error: Invalid Data provided.";

                // Logic to determine where to redirect (List vs Edit Form)
                $redirect = $id ? "index.php?action=sites_edit&id=$id" : "index.php?action=sites_index";
                header("Location: $redirect");
                exit;
            }

            $site = new Site($id, $name, $ubicacion, $adminId);

            try {
                $this->repository->update($site);

                // Success
                echo "<script>
                        alert('Site protocols updated.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                // Error
                $_SESSION['error'] = "Not valid, try again.";
                header("Location: index.php?action=sites_edit&id=$id");
            }
            exit;
        }
    }

    /**
     * Deletes a site.
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
     * Internal helper for authorization.
     * Level 5 is required for Site management.
     */
    private function verifyAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 5) {
            $_SESSION['error'] = "Access Denied: Level 5 Clearance required.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
