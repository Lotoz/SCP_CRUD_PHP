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
        //Debido a que el formulario se abre en una ventana nueva, genero el token aquí
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        require_once 'views/CRUD/anomalies/anomaliesCreate.php';
    }

    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // CSRF Check (Global Middleware should catch this, adding for clarity)

            $id = trim($_POST['id']);
            $nickname = trim($_POST['nickname']);
            $class = trim($_POST['class']);
            $contencion = $_POST['contencion'];
            $description = $_POST['description'];
            $id_sitio = !empty($_POST['id_sitio']) ? $_POST['id_sitio'] : null;
            $doc_extensa = $_POST['doc_extensa'] ?? null;

            // --- Lógica de Subida de Imagen ---
            $img_url = null;

            if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
                // Directorio actualizado
                $uploadDir = 'views/CRUD/anomalies/assets/img/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExtension = pathinfo($_FILES['img_file']['name'], PATHINFO_EXTENSION);
                $fileName = $id . '_image.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['img_file']['tmp_name'], $targetPath)) {
                    $img_url = $targetPath;
                }
            } else {
                $img_url = $_POST['img_url'] ?? null;
            }

            $anomaly = new Anomalies($id, $nickname, $class, $contencion, $description, $doc_extensa, $img_url, $id_sitio);

            try {
                $this->repository->create($anomaly);
                echo "<script>
                        alert('Anomaly registered successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    public function edit($id)
    {
        //Añado para generar el token CSRF ya que el formulario se abre en una ventana nueva
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        $anomaly = $this->repository->getById($id);
        if ($anomaly) {
            require_once 'views/CRUD/anomalies/anomaliesEdit.php';
        } else {
            echo "<script>alert('Anomaly not found.'); window.close();</script>";
        }
    }

    public function update()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nickname = trim($_POST['nickname']);
            $class = trim($_POST['class']);
            $contencion = $_POST['contencion'];
            $description = $_POST['description'];
            $id_sitio = !empty($_POST['id_sitio']) ? $_POST['id_sitio'] : null;
            $doc_extensa = $_POST['doc_extensa'] ?? null;

            // Imagen Actual (hidden input)
            $img_url = $_POST['current_img'] ?? null;

            // Si suben nueva imagen, reemplazamos
            if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'views/CRUD/anomalies/assets/img/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileExtension = pathinfo($_FILES['img_file']['name'], PATHINFO_EXTENSION);
                $fileName = $id . '_image_' . time() . '.' . $fileExtension; // Time para evitar caché
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['img_file']['tmp_name'], $targetPath)) {
                    // Opcional: Borrar imagen vieja si existe y es local
                    if ($img_url && file_exists($img_url)) {
                        unlink($img_url);
                    }
                    $img_url = $targetPath;
                }
            }

            $anomaly = new Anomalies($id, $nickname, $class, $contencion, $description, $doc_extensa, $img_url, $id_sitio);

            try {
                $this->repository->update($anomaly);
                echo "<script>
                        alert('Anomaly file updated.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    public function delete()
    {
        $this->verifyAuth();
        $id = $_POST['id'] ?? null;

        if ($id) {
            // Opcional: Borrar imagen asociada antes de borrar registro
            $anomaly = $this->repository->getById($id);
            if ($anomaly && $anomaly->getImgUrl() && file_exists($anomaly->getImgUrl())) {
                unlink($anomaly->getImgUrl());
            }

            $this->repository->delete($id);
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
