<?php
$pageTitle = "Establish New Site";
require_once 'views/templates/header.php';
?>

<link rel="stylesheet" href="views/CRUD/sites/assets/styles/sitesCreate.css">

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="form-section shadow">
                <h2 class="mb-4" style="font-family: var(--font-mono); color: var(--highlight-color); border-bottom: 2px solid var(--accent-color);">
                    <i class="fas fa-industry"></i> NEW SITE CONSTRUCTION
                </h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php
                        echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>
                <form action="index.php?action=sites_store" method="POST" id="createSiteForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Site Designation</label>
                        <input type="text" name="name_sitio" id="name_sitio" class="form-control" placeholder="e.g. Biological Research Area-12" required>
                        <div id="errorName" hidden></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Geographic Location / Coordinates</label>
                        <textarea name="ubicacion" id="ubicacion" class="form-control" rows="3" placeholder="e.g. [REDACTED], Northern Scotland" required></textarea>
                        <div id="errorLocation" hidden></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Site Director ID (Administrator)</label>
                        <input type="text" name="id_administrador" id="id_administrador" class="form-control" placeholder="User ID of the Director">
                        <small class="text-muted">Must match an existing User ID.</small>
                        <div id="errorAdmin" hidden></div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn" style="background-color: var(--accent-color); color: #fff;">
                            <i class="fas fa-save"></i> INITIALIZE SITE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="views/CRUD/sites/assets/js/sitesCreate.js"></script>

<?php require_once 'views/templates/footer.php'; ?>