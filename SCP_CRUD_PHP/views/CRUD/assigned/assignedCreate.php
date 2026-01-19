<?php
$pageTitle = "New Assignment Protocol";
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
                <h2 class="mb-4" style="font-family: var(--font-mono); color: var(--highlight-color); border-bottom: 2px solid var(--accent-color);">
                    <i class="fas fa-project-diagram"></i> LINK PERSONNEL TO ASSET
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
                <form action="index.php?action=assigned_store" method="POST" id="createAssignedForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Anomaly (SCP ID)</label>
                        <input type="text" name="scp_id" id="scp_id" class="form-control" placeholder="e.g. SCP-173" required>
                        <div id="errorScp" hidden></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Operative ID (User)</label>
                        <input type="text" name="user_id" id="user_id" class="form-control" placeholder="e.g. DrGears" required>
                        <div id="errorUser" hidden></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assignment Role</label>
                        <select name="role" id="role" class="form-select">
                            <option value="Lead Researcher">Lead Researcher</option>
                            <option value="Containment Specialist">Containment Specialist</option>
                            <option value="Security Detail">Security Detail</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Observer">Observer</option>
                        </select>
                    </div>

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> Ensure both IDs exist in the database before linking.
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn" style="background-color: var(--accent-color); color: #fff;">
                            <i class="fas fa-link"></i> AUTHORIZE LINK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="<?php echo BASE_URL; ?>views/CRUD/assigned/assets/js/assignedCreate.js"></script>
<?php require_once 'views/templates/footer.php'; ?>