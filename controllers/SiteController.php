<?php

require_once 'models/Site.php';
require_once 'interfaces/ISiteRepository.php';

class SiteController
{
    private $repository;

    public function __construct(ISiteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Muestra la tabla principal de sitios.
     */
    public function index()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        $sitesList = $this->repository->getAll();
        // RUTA ACTUALIZADA A LA CARPETA
        require_once 'views/CRUD/sites/sites.php';
    }

    /**
     * Muestra el popup de creación.
     */
    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        // RUTA ACTUALIZADA
        require_once 'views/CRUD/sites/sitesCreate.php';
    }

    /**
     * Procesa la creación.
     */
    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // El Middleware en index.php ya valida CSRF, pero por si acaso.

            $name = trim($_POST['name_sitio']);
            $ubicacion = trim($_POST['ubicacion']);
            $adminId = !empty($_POST['id_administrador']) ? trim($_POST['id_administrador']) : null;

            if (empty($name) || empty($ubicacion)) {
                echo "<script>alert('Error: Name and Location are required.'); window.history.back();</script>";
                exit;
            }

            $site = new Site(null, $name, $ubicacion, $adminId);

            try {
                $this->repository->create($site);
                echo "<script>
                        // Feedback visual y cierre
                        alert('Containment Site established successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    /**
     * Muestra el popup de edición con datos cargados.
     */
    public function edit($id)
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        $site = $this->repository->getById($id);

        if (!$site) {
            echo "<script>alert('Site not found.'); window.close();</script>";
            exit;
        }

        // RUTA ACTUALIZADA
        require_once 'views/CRUD/sites/sitesEdit.php';
    }

    /**
     * Procesa la actualización.
     */
    public function update()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = trim($_POST['name_sitio']);
            $ubicacion = trim($_POST['ubicacion']);
            $adminId = !empty($_POST['id_administrador']) ? trim($_POST['id_administrador']) : null;

            // Validación básica
            if (empty($name) || empty($ubicacion)) {
                echo "<script>alert('Name and Location are required.'); window.history.back();</script>";
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
                echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    /**
     * Elimina el sitio de forma segura (POST).
     */
    public function delete()
    {
        $this->verifyAuth();

        // Leemos del POST, no del argumento de función
        $id = $_POST['id'] ?? null;

        if ($id) {
            $this->repository->delete($id);
        }

        header('Location: index.php?action=sites_index');
        exit;
    }

    private function verifyAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 5) {
            $_SESSION['error'] = "Access Denied: Level 5 Clearance required for Site Management.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
