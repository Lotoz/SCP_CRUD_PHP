<?php
$pageTitle = "Modify Assignment";
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
                    <i class="fas fa-edit"></i> UPDATE PROTOCOLS
                </h2>

                <form action="index.php?action=assigned_update" method="POST" id="createAssignedForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <input type="hidden" name="scp_id" value="<?= htmlspecialchars($assignment->getScpId()) ?>">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($assignment->getUserId()) ?>">

                    <div class="mb-3">
                        <label class="form-label text-muted">Target Anomaly (Locked)</label>
                        <input type="text" value="<?= htmlspecialchars($assignment->getScpId()) ?>"
                            class="form-control" disabled style="background:#333; color:#aaa;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Operative ID (Locked)</label>
                        <input type="text" value="<?= htmlspecialchars($assignment->getUserId()) ?>"
                            class="form-control" disabled style="background:#333; color:#aaa;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Assignment Role</label>
                        <select name="role" id="role" class="form-select">
                            <option value="Lead Researcher" <?= $assignment->getRole() == 'Lead Researcher' ? 'selected' : '' ?>>Lead Researcher</option>
                            <option value="Containment Specialist" <?= $assignment->getRole() == 'Containment Specialist' ? 'selected' : '' ?>>Containment Specialist</option>
                            <option value="Security Detail" <?= $assignment->getRole() == 'Security Detail' ? 'selected' : '' ?>>Security Detail</option>
                            <option value="Maintenance" <?= $assignment->getRole() == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            <option value="Observer" <?= $assignment->getRole() == 'Observer' ? 'selected' : '' ?>>Observer</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn btn-warning" style="color: #000;">
                            <i class="fas fa-sync-alt"></i> UPDATE ROLE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="views/CRUD/assets/js/assignedCreate.js"></script>
<?php require_once 'views/templates/footer.php'; ?>