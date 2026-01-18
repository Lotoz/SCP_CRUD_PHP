<?php

require_once 'models/Anomalies.php';
require_once 'interfaces/IAnomaliesRepository.php';

class WikiController
{
    private $repository;

    public function __construct(IAnomaliesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Muestra la lista de anomalías accesibles (Grid)
     */
    public function index()
    {
        // 1. Verificar sesión
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userLevel = (int)$_SESSION['level'];
        $allAnomalies = $this->repository->getAll();
        $accessibleAnomalies = [];

        // Filtramos usando la función auxiliar
        foreach ($allAnomalies as $scp) {
            if ($this->canAccess($scp, $userLevel)) {
                $accessibleAnomalies[] = $scp;
            }
        }

        // Pasamos la variable a la vista
        $anomaliesList = $accessibleAnomalies;
        require_once 'views/wiki/scpwiki.php';
    }

    /**
     * Muestra el detalle de un SCP específico (Estilo Wiki)
     */
    public function show()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?action=wiki_index');
            exit;
        }

        // Buscamos el SCP
        $scp = $this->repository->getById($id);

        // Si no existe o el usuario no tiene nivel suficiente, denegamos
        if (!$scp || !$this->canAccess($scp, (int)$_SESSION['level'])) {
            echo "<script>alert('ACCESS DENIED: Insufficient Security Clearance for this file.'); window.location.href='index.php?action=wiki_index';</script>";
            exit;
        }

        // Cargamos la vista de detalle
        require_once 'views/wiki/detail.php';
    }

    /**
     * Lógica centralizada de permisos (Nivel vs Clase)
     */
    private function canAccess($scp, $userLevel)
    {
        // Nivel 5 ve todo
        if ($userLevel >= 5) return true;

        $class = strtoupper($scp->getClass());

        switch ($class) {
            case 'SAFE':
                return $userLevel >= 1;
            case 'EUCLID':
                return $userLevel >= 2;
            case 'KETER':
                return $userLevel >= 3;
            case 'THAUMIEL':
                return $userLevel >= 4;
            default:
                // Clases exóticas o desconocidas requieren nivel 4
                return $userLevel >= 4;
        }
    }
}
