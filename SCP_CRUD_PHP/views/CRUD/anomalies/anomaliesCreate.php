<?php
$pageTitle = "New SCP Entry";
require_once 'views/templates/header.php';
?>

<link rel="stylesheet" href="views/CRUD/anomalies/assets/styles/create.css">

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="form-section shadow">
                <h2 class="mb-4" style="font-family: var(--font-mono); color: var(--highlight-color); border-bottom: 2px solid var(--accent-color);">
                    <i class="fas fa-file-medical"></i> CREATE NEW FILE
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
                <form action="index.php?action=anomalies_store" method="POST" enctype="multipart/form-data" id="createForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Item # (ID)</label>

                            <input type="text" name="id" id="idInput" class="form-control"
                                placeholder="SCP-XXX" required
                                pattern="^SCP-.*$"
                                title="Must start with 'SCP-'"
                                style="font-family: var(--font-mono); font-weight: bold;">

                            <div class="form-text text-danger" id="idHelp" style="display:none;">
                                Format invalid. Must start with "SCP-".
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Object Class</label>
                            <select name="class" class="form-select" required>
                                <option value="SAFE">SAFE</option>
                                <option value="EUCLID">EUCLID</option>
                                <option value="KETER">KETER</option>
                                <option value="THAUMIEL">THAUMIEL</option>
                                <option value="NEUTRALIZED">NEUTRALIZED</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nickname / Codename</label>
                            <input type="text" name="nickname" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Containment Site ID</label>
                            <input type="number" name="id_sitio" class="form-control" placeholder="Example: 19">
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Original Database Link (Extended Doc)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                            <input type="url" name="doc_extensa" class="form-control"
                                placeholder="https://scp-wiki.wikidot.com/scp-xxx">
                        </div>
                        <div class="form-text">Leave empty if no external documentation exists.</div>
                    </div>

                    <div class="mb-3 p-3" style="background: rgba(0,0,0,0.05); border-radius: 5px;">
                        <label class="form-label fw-bold">Visual Records (Image)</label>
                        <input type="file" name="img_file" id="imgInput" class="form-control" accept="image/*">
                        <div class="mt-2 text-center" id="previewContainer" style="display:none;">
                            <p class="text-muted small">PREVIEW</p>
                            <img id="imgPreview" src="" alt="Preview" style="max-height: 200px; border: 2px solid var(--accent-color);">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--alert-color);">Special Containment Procedures</label>
                        <textarea name="contencion" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="6" required></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn" style="background-color: var(--accent-color); color: #fff;">
                            <i class="fas fa-save"></i> SAVE RECORD
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</main>

<script src="<?php echo BASE_URL; ?>views/CRUD/anomalies/assets/js/anomaliesCreate.js"></script>
<?php require_once 'views/templates/footer.php'; ?>