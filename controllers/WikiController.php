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
     * I show the public Wiki (filtered by clearance).
     */
    public function index()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        // 1. I verify the session
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userLevel = (int)$_SESSION['level'];

        // 2. I get ALL anomalies from DB
        $allAnomalies = $this->repository->getAll();

        // 3. I filter them based on Security Clearance
        $accessibleAnomalies = [];

        foreach ($allAnomalies as $scp) {
            $class = strtoupper($scp->getClass()); // SAFE, EUCLID, KETER...

            // Logic Rules:
            // Level 5 sees everything.
            if ($userLevel >= 5) {
                $accessibleAnomalies[] = $scp;
                continue;
            }

            // Logic for lower levels
            switch ($class) {
                case 'SAFE':
                    // Everyone (Level 1+) sees Safe
                    if ($userLevel >= 1) $accessibleAnomalies[] = $scp;
                    break;

                case 'EUCLID':
                    // Level 2, 3, 4 see Euclid
                    if ($userLevel >= 2) $accessibleAnomalies[] = $scp;
                    break;

                case 'KETER':
                    // Level 3, 4 see Keter
                    if ($userLevel >= 3) $accessibleAnomalies[] = $scp;
                    break;

                case 'THAUMIEL':
                    // Usually only Level 4 or 5
                    if ($userLevel >= 4) $accessibleAnomalies[] = $scp;
                    break;

                default:
                    // Unknown classes only for Level 4+
                    if ($userLevel >= 4) $accessibleAnomalies[] = $scp;
                    break;
            }
        }

        // 4. I pass the filtered list to the view
        $anomaliesList = $accessibleAnomalies;

        require_once 'views/scpwiki.php';
    }
}
