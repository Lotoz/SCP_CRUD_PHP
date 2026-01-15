<?php
$pageTitle = "Modify Site Protocols";
require_once 'views/templates/header.php';
?>

<style>
    header nav,
    .logo-area .security-level {
        display: none;
    }

    header {
        justify-content: center;
        border-bottom: none;
    }

    body {
        padding-bottom: 2rem;
        background-color: var(--bg-color);
    }

    .form-section {
        background: var(--card-bg);
        padding: 20px;
        border: 1px solid var(--accent-color);
    }
</style>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="form-section shadow">
                <h2 class="mb-4" style="font-family: var(--font-mono); color: #ffc107; border-bottom: 2px solid #ffc107;">
                    <i class="fas fa-edit"></i> UPDATE SITE PROTOCOLS
                </h2>

                <form action="index.php?action=sites_update" method="POST" id="createSiteForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $site->getId() ?>">

                    <div class="mb-3">
                        <label class="form-label text-muted">System ID</label>
                        <input type="text" class="form-control" value="Site-<?= htmlspecialchars($site->getId()) ?>" disabled style="background-color: #333; color: #aaa;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Site Designation</label>
                        <input type="text" name="name_sitio" id="name_sitio" class="form-control"
                            value="<?= htmlspecialchars($site->getNameSitio()) ?>" required>
                        <div id="errorName" hidden></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Geographic Location / Coordinates</label>
                        <textarea name="ubicacion" id="ubicacion" class="form-control" rows="3" required><?= htmlspecialchars($site->getUbicacion()) ?></textarea>
                        <div id="errorLocation" hidden></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Site Director ID (Administrator)</label>
                        <input type="text" name="id_administrador" id="id_administrador" class="form-control"
                            value="<?= htmlspecialchars($site->getIdAdministrador()) ?>">
                        <small class="text-muted">Must match an existing User ID.</small>
                        <div id="errorAdmin" hidden></div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn btn-warning" style="color: #000;">
                            <i class="fas fa-sync-alt"></i> UPDATE PROTOCOLS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="views/CRUD/assets/js/sitesCreate.js"></script>

<?php require_once 'views/templates/footer.php'; ?>