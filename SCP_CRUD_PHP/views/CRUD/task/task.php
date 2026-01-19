<?php
$pageTitle = "My Assignments - Task Log";
require_once 'views/templates/header.php';
?>

<main>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="font-family: var(--font-mono); color: var(--highlight-color); text-transform: uppercase;">
                <i class="fas fa-tasks"></i> Pending Assignments
            </h1>

            <a href="#" onclick="openCreateTask(); return false;" class="btn"
                style="background-color: var(--accent-color); color: #fff; font-family: var(--font-mono);">
                <i class="fas fa-plus-square"></i> ASSIGN NEW TASK
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="font-family: var(--font-mono);">
                <?= htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="font-family: var(--font-mono); margin-top:0;">Operative Duties</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: var(--nav-bg); color: var(--nav-text); font-family: var(--font-mono);">
                        <tr>
                            <th style="width: 10%;">ID</th>
                            <th style="width: 45%;">Directive / Description</th>
                            <th style="width: 15%;">Target Date</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-family: var(--font-main); color: var(--text-color);">
                        <?php if (empty($tasks)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle"></i> No pending tasks. You are currently idle.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td style="font-family: var(--font-mono); font-weight: bold; color: var(--accent-color);">
                                        TSK-<?= htmlspecialchars($task->getId()) ?>
                                    </td>

                                    <td>
                                        <?= nl2br(htmlspecialchars($task->getDescription())) ?>
                                    </td>

                                    <td style="font-family: var(--font-mono);">
                                        <?php if ($task->getDueDate()): ?>
                                            <i class="far fa-calendar-alt text-muted"></i>
                                            <?= htmlspecialchars($task->getDueDate()) ?>
                                        <?php else: ?>
                                            <span class="text-muted small">-- No Deadline --</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($task->isCompleted()): ?>
                                            <span class="badge bg-success" style="font-family: var(--font-mono);">COMPLETED</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark" style="font-family: var(--font-mono);">PENDING</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="openEditTask(<?= $task->getId() ?>)" title="Update Status">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <form action="index.php?action=task_delete" method="POST" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="id" value="<?= $task->getId() ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Confirm: Remove this task?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<script>
    const popupFeatures = "width=600,height=500,resizable=yes,scrollbars=yes";

    function openCreateTask() {
        const left = (screen.width - 600) / 2;
        const top = (screen.height - 500) / 2;
        window.open('index.php?action=task_create', 'CreateTask', `${popupFeatures},top=${top},left=${left}`);
    }

    function openEditTask(id) {
        const left = (screen.width - 600) / 2;
        const top = (screen.height - 500) / 2;
        window.open(`index.php?action=task_edit&id=${id}`, 'EditTask', `${popupFeatures},top=${top},left=${left}`);
    }
</script>
<?php require_once 'views/templates/footer.php'; ?>