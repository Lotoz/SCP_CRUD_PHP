<?php
$pageTitle = "Foundation Facilities - Restricted Access";
require_once 'views/templates/header.php';
?>

<main>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="font-family: var(--font-mono); color: var(--highlight-color); text-transform: uppercase;">
                Global Sites Registry
            </h1>

            <a href="#" onclick="openCreateSite(); return false;" class="btn"
                style="background-color: var(--accent-color); color: #fff; font-family: var(--font-mono);">
                <i class="fas fa-plus-circle"></i> ESTABLISH NEW SITE
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="font-family: var(--font-mono);">
                <?= htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="font-family: var(--font-mono); margin-top:0;">Operational Facilities</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: var(--nav-bg); color: var(--nav-text); font-family: var(--font-mono);">
                        <tr>
                            <th>Site ID</th>
                            <th>Designation</th>
                            <th>Geographic Location</th>
                            <th>Site Director (Admin)</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-family: var(--font-main); color: var(--text-color);">
                        <?php if (empty($sitesList)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No active sites found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sitesList as $site): ?>
                                <tr>
                                    <td style="font-family: var(--font-mono); font-weight: bold; color: var(--accent-color);">
                                        Site-<?= htmlspecialchars($site->getId()) ?>
                                    </td>

                                    <td class="fw-bold">
                                        <?= htmlspecialchars($site->getNameSitio()) ?>
                                    </td>

                                    <td>
                                        <i class="fas fa-map-marker-alt text-muted"></i>
                                        <?= htmlspecialchars($site->getUbicacion()) ?>
                                    </td>

                                    <td>
                                        <?php if ($site->getIdAdministrador()): ?>
                                            <span class="badge bg-dark">
                                                <i class="fas fa-user-shield"></i> <?= htmlspecialchars($site->getIdAdministrador()) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">VACANT</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">
                                        <div class="btn-group" role="group">

                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning"
                                                onclick="openEditSite(<?= $site->getId() ?>)"
                                                title="Modify Site Protocols">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <form action="index.php?action=sites_delete" method="POST" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="id" value="<?= $site->getId() ?>">

                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('WARNING: Decommissioning a Site is a major event. Are you sure?');"
                                                    title="Decommission Site">
                                                    <i class="fas fa-trash-alt"></i> Delete
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
    // Configuración de la ventana popup
    const popupFeatures = "width=700,height=600,resizable=yes,scrollbars=yes";

    function openCreateSite() {
        const left = (screen.width - 700) / 2;
        const top = (screen.height - 600) / 2;
        window.open('index.php?action=sites_create', 'CreateSite', `${popupFeatures},top=${top},left=${left}`);
    }

    function openEditSite(id) {
        const left = (screen.width - 700) / 2;
        const top = (screen.height - 600) / 2;
        window.open(`index.php?action=sites_edit&id=${id}`, 'EditSite', `${popupFeatures},top=${top},left=${left}`);
    }
</script>

<?php require_once 'views/templates/footer.php'; ?>