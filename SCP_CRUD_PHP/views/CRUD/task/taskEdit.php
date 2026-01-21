<?php
// Aseguramos que el título esté definido
$pageTitle = "Update Assignment Status";
require_once 'views/templates/header.php';

$dateValue = '';
if ($task->getDueDate()) {
    $dateValue = date('Y-m-d', strtotime($task->getDueDate()));
}
?>

<link rel="stylesheet" href="views/CRUD/task/assets/styles/style.css">
<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="form-section shadow">
                <h2 class="mb-4" style="font-family: var(--font-mono); color: #ffc107; border-bottom: 2px solid #ffc107;">
                    <i class="fas fa-edit"></i> UPDATE STATUS
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

                <form action="index.php?action=task_update" method="POST" id="createTaskForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= $task->getId() ?>">
                    <input type="hidden" name="id_usuario" value="<?= $task->getIdUsuario() ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Task Directive</label>
                        <textarea name="description" id="description" class="form-control" rows="4" required><?= htmlspecialchars($task->getDescription()) ?></textarea>
                        <div id="errorDesc" hidden></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Completion Date</label>
                        <input type="date" name="due_date" id="due_date" class="form-control"
                            style="font-family: var(--font-mono);"
                            value="<?= $dateValue ?>">
                        <div id="errorDate" hidden></div>
                    </div>

                    <div class="mb-3 form-check p-3" style="background: rgba(0,0,0,0.1); border-radius: 5px; margin-left: 10px;">
                        <input type="checkbox" class="form-check-input" name="completado" id="checkComp"
                            <?= $task->isCompleted() ? 'checked' : '' ?>
                            style="transform: scale(1.2);">
                        <label class="form-check-label fw-bold ms-2" for="checkComp" style="color: var(--text-color);">
                            MARK AS COMPLETED
                        </label>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn btn-warning" style="color: #000;">
                            <i class="fas fa-sync-alt"></i> UPDATE TASK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="views/CRUD/task/assets/js/taskCreate.js"></script>
<?php require_once 'views/templates/footer.php'; ?>