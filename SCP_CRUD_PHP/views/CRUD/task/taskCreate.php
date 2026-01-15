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

                <form action="index.php?action=task_store" method="POST" id="createTaskForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Task Directive / Description</label>
                        <textarea name="description" id="description" class="form-control" rows="5"
                            placeholder="Enter protocol instructions or daily duties here..." required></textarea>
                        <div id="errorDesc" hidden></div>
                    </div>

                    <div class="alert alert-secondary small" style="font-family: var(--font-mono);">
                        <i class="fas fa-info-circle"></i> This task will be automatically assigned to your Personnel ID:
                        <strong><?= $_SESSION['user_id'] ?? 'UNKNOWN' ?></strong>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn" style="background-color: var(--accent-color); color: #fff;">
                            <i class="fas fa-check"></i> CONFIRM ASSIGNMENT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="views/CRUD/assets/js/taskCreate.js"></script>
<?php require_once 'views/templates/footer.php'; ?>