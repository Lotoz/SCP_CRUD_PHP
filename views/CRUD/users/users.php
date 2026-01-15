<?php
$pageTitle = "Personnel Database - Level 5 Access";
require_once 'views/templates/header.php';
?>

<main>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="font-family: var(--font-mono); color: var(--highlight-color); text-transform: uppercase;">
                Foundation Personnel Registry
            </h1>

            <a href="#" onclick="openCreateUser(); return false;" class="btn"
                style="background-color: var(--accent-color); color: #fff; font-family: var(--font-mono);">
                <i class="fas fa-user-plus"></i> RECRUIT NEW PERSONNEL
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="font-family: var(--font-mono);">
                <?= htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="font-family: var(--font-mono); margin-top:0;">Active Staff</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: var(--nav-bg); color: var(--nav-text); font-family: var(--font-mono);">
                        <tr>
                            <th>Operative ID</th>
                            <th>Full Name</th>
                            <th>Clearance & Role</th>
                            <th>Status</th>
                            <th>Theme</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-family: var(--font-main); color: var(--text-color);">
                        <?php foreach ($usersList as $user): ?>
                            <tr>
                                <td style="font-family: var(--font-mono); font-weight: bold; color: var(--accent-color);">
                                    <?= htmlspecialchars($user->getId()) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($user->getFullName()) ?>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($user->getEmail()) ?></small>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">Lvl <?= $user->getLevel() ?></span>
                                    <span class="badge" style="background-color: var(--highlight-color); color: #000;">
                                        <?= strtoupper($user->getRol()) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($user->isstate()): ?>
                                        <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> ACTIVE</span>
                                    <?php else: ?>
                                        <span class="text-danger fw-bold"><i class="fas fa-ban"></i> SUSPENDED</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <code><?= htmlspecialchars($user->getTheme()) ?></code>
                                </td>

                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            onclick="openEditUser('<?= $user->getId() ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <form action="index.php?action=users_delete" method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="id" value="<?= $user->getId() ?>">

                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('WARNING: Deleting this user will move them to the Ex-Employees archive. Continue?');">
                                                <i class="fas fa-trash-alt"></i> Terminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    const popupFeatures = "width=800,height=900,resizable=yes,scrollbars=yes";

    function openCreateUser() {
        const left = (screen.width - 800) / 2;
        const top = (screen.height - 900) / 2;
        window.open('index.php?action=users_create', 'CreateUser', `${popupFeatures},top=${top},left=${left}`);
    }

    function openEditUser(id) {
        const left = (screen.width - 800) / 2;
        const top = (screen.height - 900) / 2;
        window.open(`index.php?action=users_edit&id=${id}`, 'EditUser', `${popupFeatures},top=${top},left=${left}`);
    }
</script>

<?php require_once 'views/templates/footer.php'; ?>