<?php
$pageTitle = "Modify SCP File";
require_once 'views/templates/header.php';
?>

<link rel="stylesheet" href="views/CRUD/anomalies/assets/styles/create.css">

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="form-section shadow">
                <h2 class="mb-4" style="font-family: var(--font-mono); color: #ffc107; border-bottom: 2px solid #ffc107;">
                    <i class="fas fa-edit"></i> UPDATE FILE: <?= htmlspecialchars($anomaly->getId()) ?>
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
                <form action="index.php?action=anomalies_update" method="POST" enctype="multipart/form-data" id="createForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <input type="hidden" name="original_id" value="<?= $anomaly->getId() ?>">

                    <input type="hidden" name="current_img" value="<?= htmlspecialchars($anomaly->getImgUrl() ?? '') ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Item # (ID)</label>
                            <input type="text"
                                name="id"
                                value="<?= htmlspecialchars($anomaly->getId()) ?>"
                                class="form-control"
                                required
                                pattern="^SCP-.*$"
                                title="Must start with 'SCP-'"
                                style="font-family: var(--font-mono); font-weight: bold;">
                            <small class="text-muted">Warning: Changing the ID may affect linked records.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Object Class</label>
                            <select name="class" class="form-select" required>
                                <option value="SAFE" <?= $anomaly->getClass() == 'SAFE' ? 'selected' : '' ?>>SAFE</option>
                                <option value="EUCLID" <?= $anomaly->getClass() == 'EUCLID' ? 'selected' : '' ?>>EUCLID</option>
                                <option value="KETER" <?= $anomaly->getClass() == 'KETER' ? 'selected' : '' ?>>KETER</option>
                                <option value="THAUMIEL" <?= $anomaly->getClass() == 'THAUMIEL' ? 'selected' : '' ?>>THAUMIEL</option>
                                <option value="NEUTRALIZED" <?= $anomaly->getClass() == 'NEUTRALIZED' ? 'selected' : '' ?>>NEUTRALIZED</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nickname / Codename</label>
                            <input type="text" name="nickname" value="<?= htmlspecialchars($anomaly->getNickname()) ?>" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Containment Site ID</label>
                            <input type="number" name="id_sitio" value="<?= htmlspecialchars($anomaly->getIdSitio()) ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Original Database Link</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                            <input type="url" name="doc_extensa" class="form-control"
                                value="<?= htmlspecialchars($anomaly->getDocExtensa() ?? '') ?>"
                                placeholder="https://scp-wiki.wikidot.com/scp-xxx">
                        </div>
                    </div>

                    <div class="mb-3 p-3" style="background: rgba(0,0,0,0.05); border-radius: 5px;">
                        <label class="form-label fw-bold">Visual Records</label>

                        <?php if ($anomaly->hasImage()): ?>
                            <div class="mb-2">
                                <small>Current File:</small><br>
                                <img src="<?= htmlspecialchars($anomaly->getImgUrl()) ?>" style="max-height: 100px; border: 1px solid #555;">
                            </div>
                        <?php endif; ?>

                        <input type="file" name="img_file" id="imgInput" class="form-control" accept="image/*">
                        <div class="mt-2 text-center" id="previewContainer" style="display:none;">
                            <p class="text-muted small">NEW PREVIEW</p>
                            <img id="imgPreview" src="" alt="Preview" style="max-height: 200px; border: 2px solid var(--accent-color);">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--alert-color);">Special Containment Procedures</label>
                        <textarea name="contencion" class="form-control" rows="4" required><?= htmlspecialchars($anomaly->getContencion()) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="6" required><?= htmlspecialchars($anomaly->getDescription()) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn btn-warning" style="color: #000;">
                            <i class="fas fa-sync-alt"></i> UPDATE RECORD
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="views/CRUD/anomalies/assets/js/anomaliesCreate.js"></script>
<?php require_once 'views/templates/footer.php'; ?>