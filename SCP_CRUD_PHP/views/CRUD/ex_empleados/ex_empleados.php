<?php
$pageTitle = "Archived Personnel Records";
require_once 'views/templates/header.php';
?>

<main>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="font-family: var(--font-mono); color: #888; text-transform: uppercase;">
                <i class="fas fa-archive"></i> Terminated Personnel Archive
            </h1>

            <span class="badge bg-secondary" style="font-family: var(--font-mono);">
                READ ONLY MODE
            </span>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="font-family: var(--font-mono);">
                <?= htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="border-top: 5px solid #555;">
            <h2 style="font-family: var(--font-mono); margin-top:0; color: #555;">Decommissioned Log</h2>
            <p class="text-muted small">
                <i class="fas fa-info-circle"></i> This log is automatically populated when a user is deleted from the system.
            </p>

            <div class="table-responsive">
                <table class="table table-secondary table-hover align-middle">
                    <thead class="table-dark" style="font-family: var(--font-mono);">
                        <tr>
                            <th>Archive ID</th>
                            <th>Name</th>
                            <th>Last Held Rank</th>
                            <th>Termination Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-family: var(--font-main);">
                        <?php if (empty($exEmpleadosList)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No archives found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($exEmpleadosList as $ex): ?>
                                <tr>
                                    <td class="fw-bold text-muted">LOG-<?= $ex->getId() ?></td>

                                    <td><?= htmlspecialchars($ex->getFullName()) ?></td>

                                    <td>
                                        <span class="badge bg-secondary">Lvl <?= $ex->getLevel() ?></span>
                                        <?= strtoupper($ex->getRol()) ?>
                                    </td>

                                    <td style="font-family: var(--font-mono);">
                                        <?= htmlspecialchars($ex->getFechaEliminacion()) ?>
                                    </td>

                                    <td class="text-end">
                                        <form action="index.php?action=exempleados_delete" method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="id" value="<?= $ex->getId() ?>">

                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('WARNING: You are about to PERMANENTLY delete an audit log. This action is irreversible. Proceed?');"
                                                title="Purge Record">
                                                <i class="fas fa-trash"></i> Purge
                                            </button>
                                        </form>
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

<?php require_once 'views/templates/footer.php'; ?>