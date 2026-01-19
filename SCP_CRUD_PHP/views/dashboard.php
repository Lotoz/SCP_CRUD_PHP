<?php
// Define el titulo de la página
$pageTitle = "SCP Foundation - Restricted Access";

// Incluye el header
require_once 'views/templates/header.php';
?>

<main>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" style="font-family: var(--font-mono);">
            <?= htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <div class="msgUser container">
        <h1>Welcome, <span class="user-name"><?php echo $_SESSION['name'] ?? 'Agent'; ?></span></h1>
        <p>Remember to log off. The Foundation is watching. <br>
            <strong>WARNING:</strong> Unauthorized access will be monitored and terminated.
        </p>
    </div>
    <div class="dashboard-grid">

        <div class="card taskPendients">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--accent-color); margin-bottom: 10px;">
                <h2>📋 Pending Tasks</h2>
                <a href="index.php?action=task_index" style="font-size: 0.8rem; color: var(--accent-color); text-decoration: none;">[VIEW ALL LOGS]</a>
            </div>

            <ul style="padding-left: 20px;">
                <?php if (empty($tasks)): ?>
                    <li style="list-style: none; color: #777;">
                        <i class="fas fa-check"></i> No active directives assigned. Standby.
                    </li>
                <?php else: ?>
                    <?php
                    // Mostramos máximo 5 tareas en el dashboard para no saturar
                    $previewTasks = array_slice($tasks, 0, 5);
                    ?>
                    <?php foreach ($previewTasks as $task): ?>
                        <li style="margin-bottom: 8px;">
                            <?php if ($task->isCompleted()): ?>
                                <del style="color: #888;"><?= htmlspecialchars($task->getDescription()) ?></del>
                            <?php else: ?>
                                <?= htmlspecialchars($task->getDescription()) ?>
                            <?php endif; ?>

                            <?php if (!$task->isCompleted()): ?>
                                <span class="badge bg-warning text-dark" style="font-size: 0.6em; padding: 2px 4px;">PENDING</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>


            <div style="margin-top: 15px; text-align: right;">
                <a href="#" onclick="openCreateTask(); return false;" class="btn btn-sm"
                    style="background-color: var(--accent-color); color: #fff; font-family: var(--font-mono);">
                    + ASSIGN NEW TASK
                </a>
            </div>

        </div>

        <div class="card alertsContention">
            <h2>⚠️ Containment Alerts</h2>
            <div class="alert-item">
                <strong>SCP-682:</strong> Breach attempt detected.
            </div>
            <div class="alert-item">
                <strong>SCP-096:</strong> Status: Docile.
            </div>
            <div class="alert-item">
                <strong>CLEARANCE:</strong> Level <?php echo $_SESSION['level']; ?> Access Granted.
            </div>
            <div class="alert-item">
                <strong>TO DO :</strong> Someday I will register it in the database with a random event.
            </div>

        </div>
    </div>
</main>


<?php
require_once 'views/templates/footer.php';
?>