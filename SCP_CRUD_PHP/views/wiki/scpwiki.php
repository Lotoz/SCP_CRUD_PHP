<?php
// views/wiki/index.php
// SEGURIDAD: Evitar acceso directo al archivo.
// Si no existe la variable del controlador, significa que entraron "por la ventana".
if (!isset($anomaliesList)) {
    header("Location: ../index.php?action=login");
    exit();
}
$pageTitle = "SCP Foundation Database - Access Clearance Verified";
require_once 'views/templates/header.php';
?>
<link rel="stylesheet" href="views/wiki/assets/styles/detail.css">
<main class="container my-5">

    <div class="row mb-4">
        <div class="col-12">
            <div class="msgUser text-center" style="border-left-color: #d9534f;">
                <h1 style="font-family: 'Share Tech Mono'; text-transform: uppercase; letter-spacing: 2px;">
                    <i class="fas fa-database"></i> Classified Archives
                </h1>
                <p class="mb-0">
                    <strong>CLEARANCE VERIFIED:</strong> Level <?php echo $_SESSION['level'] ?? '0'; ?> Personnel.
                    <br>
                    <span class="text-muted">Displaying accessible anomalies based on your security clearance.</span>
                </p>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-8 mx-auto">
            <div class="input-group">
                <span class="input-group-text bg-dark text-white border-dark" style="font-family: 'Share Tech Mono';">CMD://SEARCH_</span>
                <input type="text" id="searchInput" class="form-control border-dark" placeholder="Enter Item # or Keywords...">
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if (empty($anomaliesList)): ?>
            <div class="col-12 text-center">
                <div class="alert alert-warning">
                    No records found matching your security clearance.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($anomaliesList as $scp): ?>

                <div class="col-md-6 col-lg-4">
                    <div class="card scp-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong style="font-family: 'Share Tech Mono'; font-size: 1.2rem;">
                                <?php echo htmlspecialchars($scp->getId()); ?>
                            </strong>
                            <span class="badge scp-class-badge <?php echo strtolower($scp->getClass()); ?>">
                                <?php echo strtoupper($scp->getClass()); ?>
                            </span>
                        </div>

                        <div class="scp-img-container">
                            <?php if ($scp->hasImage()): ?>
                                <img src="<?php echo htmlspecialchars($scp->getImgUrl()); ?>"
                                    alt="<?php echo htmlspecialchars($scp->getNickname()); ?>"
                                    class="scp-img" width="320" height="380">
                            <?php else: ?>
                                <div class="d-flex justify-content-center align-items-center w-100 h-100 text-white bg-secondary">
                                    <span style="font-family: 'Share Tech Mono'; letter-spacing: 1px;">[IMAGE REDACTED]</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title text-uppercase fw-bold border-bottom pb-2">
                                <?php echo htmlspecialchars($scp->getNickname()); ?>
                            </h5>
                            <p class="card-text text-muted scp-description">
                                <strong style="font-family: 'Share Tech Mono';">CONTAINMENT:</strong><br>
                                <?php
                                // I truncate the description to 120 chars
                                $desc = htmlspecialchars($scp->getDescription());
                                echo strlen($desc) > 120 ? substr($desc, 0, 120) . '...' : $desc;
                                ?>
                            </p>
                            <div class="overlay">
                                <a href="index.php?action=wiki_show&id=<?php echo $scp->getId(); ?>"
                                    class="text-decoration-none fw-bold stretched-link"
                                    style="font-family: 'Share Tech Mono'; letter-spacing: 2px; border: 1px solid #fff; padding: 10px 20px;">
                                    ACCESS FILE<i class="bi bi-box-arrow-in-down"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-secondary text-center" role="alert" style="border: 2px dashed #444;">
                <h4 style="font-family: 'Share Tech Mono';">EXTERNAL DATABASE LINK</h4>
                <p>For extended documentation and archival records, please refer to the central mainframe.</p>
                <a href="https://scp-wiki.wikidot.com/" target="_blank" class="btn btn-dark">
                    ACCESS SCP-WIKI.WIKIDOT.COM
                </a>
                <p class="mt-2 mb-0"><small>Warning: You are leaving the secure intranet.</small></p>
            </div>
        </div>
    </div>
    <script src="views/assets/js/scpwiki.js"></script>
</main>


<?php require_once 'views/templates/footer.php'; ?>