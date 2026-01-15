<?php
$pageTitle = "Personnel Assignment Matrix";
require_once 'views/templates/header.php';
?>

<main>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="font-family: var(--font-mono); color: var(--highlight-color); text-transform: uppercase;">
                <i class="fas fa-network-wired"></i> Containment Assignments
            </h1>

            <a href="#" onclick="openCreateAssignment(); return false;" class="btn"
                style="background-color: var(--accent-color); color: #fff; font-family: var(--font-mono);">
                <i class="fas fa-link"></i> ASSIGN PERSONNEL
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="font-family: var(--font-mono);">
                <?= htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="font-family: var(--font-mono); margin-top:0;">Active Protocols</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: var(--nav-bg); color: var(--nav-text); font-family: var(--font-mono);">
                        <tr>
                            <th>SCP Designation</th>
                            <th>Assigned Operative</th>
                            <th>Operational Role</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-family: var(--font-main); color: var(--text-color);">
                        <?php if (empty($assignments)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No active assignments.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($assignments as $row): ?>
                                <tr>
                                    <td class="fw-bold text-danger" style="font-family: var(--font-mono);">
                                        <?= htmlspecialchars($row->getScpId()) ?>
                                    </td>

                                    <td class="fw-bold" style="color: var(--accent-color);">
                                        <i class="fas fa-user-tag"></i> <?= htmlspecialchars($row->getUserId()) ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($row->getRole()) ?></span>
                                    </td>

                                    <td class="text-end">
                                        <div class="btn-group" role="group">

                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                onclick="openEditAssignment('<?= $row->getUserId() ?>', '<?= $row->getScpId() ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <form action="index.php?action=assigned_delete" method="POST" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="uid" value="<?= $row->getUserId() ?>">
                                                <input type="hidden" name="sid" value="<?= $row->getScpId() ?>">

                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('CONFIRM REVOCATION: Unlink <?= $row->getUserId() ?> from <?= $row->getScpId() ?>?');">
                                                    <i class="fas fa-unlink"></i> Revoke
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
    const popupFeatures = "width=600,height=550,resizable=yes,scrollbars=yes";

    function openCreateAssignment() {
        const left = (screen.width - 600) / 2;
        const top = (screen.height - 550) / 2;
        window.open('index.php?action=assigned_create', 'AssignStaff', `${popupFeatures},top=${top},left=${left}`);
    }

    // Recibe dos parámetros
    function openEditAssignment(uid, sid) {
        const left = (screen.width - 600) / 2;
        const top = (screen.height - 550) / 2;
        window.open(`index.php?action=assigned_edit&uid=${uid}&sid=${sid}`, 'EditAssign', `${popupFeatures},top=${top},left=${left}`);
    }
</script>

<?php require_once 'views/templates/footer.php'; ?>