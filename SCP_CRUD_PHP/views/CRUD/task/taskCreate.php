<?php
$pageTitle = "New Operational Assignment";
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
                    <i class="fas fa-clipboard-list"></i> NEW ASSIGNMENT
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
                <form action="index.php?action=task_store" method="POST" id="createTaskForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Task Directive / Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"
                            placeholder="Enter protocol instructions..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Completion Date (Optional)</label>
                        <input type="date" id="due_date" name="due_date" class="form-control" style="font-family: var(--font-mono);">
                        <small class="text-muted">Leave empty if no strict deadline applies.</small>
                    </div>

                    <div class="alert alert-secondary small" style="font-family: var(--font-mono);">
                        <i class="fas fa-info-circle"></i> Assigned to Personnel ID:
                        <strong><?= $_SESSION['user_id'] ?? 'UNKNOWN' ?></strong>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">CANCEL</button>
                        <button type="submit" class="btn" style="background-color: var(--accent-color); color: #fff;">
                            <i class="fas fa-check"></i> CONFIRM ASSIGNMENT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="views/CRUD/task/assets/js/taskCreate.js"></script>
<?php require_once 'views/templates/footer.php'; ?>