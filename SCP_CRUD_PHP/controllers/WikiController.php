<?php

require_once 'models/Anomalies.php';
require_once 'interfaces/IAnomaliesRepository.php';

/**
 * WikiController - General Access & Research
 *
 * Handles the read-only display of SCP files for general personnel.
 * Implements the "Need-to-know" principle by filtering content based on Clearance Levels.
 */
class WikiController
{
    private $repository;

    public function __construct(IAnomaliesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Displays the grid of accessible anomalies.
     * Filters the full database list, showing only what the user is authorized to see.
     */
    public function index()
    {
        // 1. Verify Session
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userLevel = (int)$_SESSION['level'];
        $allAnomalies = $this->repository->getAll();
        $accessibleAnomalies = [];

        // 2. Filter anomalies based on clearance protocols
        foreach ($allAnomalies as $scp) {
            if ($this->canAccess($scp, $userLevel)) {
                $accessibleAnomalies[] = $scp;
            }
        }

        // Pass filtered list to the view
        $anomaliesList = $accessibleAnomalies;
        require_once 'views/wiki/scpwiki.php';
    }

    /**
     * Displays the detailed Wiki entry for a specific SCP.
     * Performs a secondary security check to prevent URL manipulation (e.g., guessing IDs).
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

        // Retrieve the SCP
        $scp = $this->repository->getById($id);

        // Security Check: If SCP doesn't exist or user lacks clearance, deny access
        if (!$scp || !$this->canAccess($scp, (int)$_SESSION['level'])) {
            echo "<script>
                    alert('ACCESS DENIED: Insufficient Security Clearance for this file.'); 
                    window.location.href='index.php?action=wiki_index';
                  </script>";
            exit;
        }

        // Load the Detail View
        require_once 'views/wiki/detail.php';
    }

    /**
     * Centralized Access Logic (Clearance vs. Object Class).
     *
     * Rules:
     * - Level 5 (O5/Director): Unrestricted access.
     * - SAFE: Level 1+
     * - EUCLID: Level 2+
     * - KETER: Level 3+
     * - THAUMIEL / Exotic: Level 4+
     *
     * @param Anomalies $scp The anomaly object.
     * @param int $userLevel The user's clearance level.
     * @return bool True if access is granted.
     */
    private function canAccess($scp, $userLevel)
    {
        // Level 5 overrides all restrictions
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
                // Unclassified or Exotic classes are treated as High Security (Level 4)
                return $userLevel >= 4;
        }
    }
}
