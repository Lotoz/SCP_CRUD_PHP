<?php
$pageTitle = "SCP Foundation - Database Access";
require_once 'views/templates/header.php';
?>

<main>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="font-family: var(--font-mono); color: var(--highlight-color); text-transform: uppercase;">
                SCP Database Registry
            </h1>

            <a href="#" onclick="openCreateSCP(); return false;" class="btn"
                style="background-color: var(--accent-color); color: #fff; font-family: var(--font-mono);">
                <i class="fas fa-plus-circle"></i> FILE NEW SCP
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="font-family: var(--font-mono);">
                <?= htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="font-family: var(--font-mono); margin-top:0;">Classified Records</h2>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: var(--nav-bg); color: var(--nav-text); font-family: var(--font-mono);">
                        <tr>
                            <th>Item #</th>
                            <th>Nickname</th>
                            <th>Object Class</th>
                            <th>Containment Site</th>
                            <th>Image</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-family: var(--font-main); color: var(--text-color);">

                        <?php if (empty($anomaliesList)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No records found in the database.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($anomaliesList as $scp): ?>
                                <tr>
                                    <td style="font-family: var(--font-mono); font-weight: bold; color: var(--accent-color);">
                                        <?= htmlspecialchars($scp->getId()) ?>
                                    </td>

                                    <td><?= htmlspecialchars($scp->getNickname()) ?></td>

                                    <td>
                                        <?php
                                        $class = strtoupper($scp->getClass());
                                        $badgeColor = '#0bd600';
                                        if ($class === 'EUCLID') $badgeColor = '#ffc107';
                                        if ($class === 'KETER') $badgeColor = '#dc3545';
                                        if ($class === 'THAUMIEL') $badgeColor = '#0d6efd';
                                        ?>
                                        <span class="badge" style="background-color: <?= $badgeColor ?>; color: #000; border: 1px solid #333;">
                                            <?= htmlspecialchars($class) ?>
                                        </span>
                                    </td>

                                    <td>
                                        Site-<?= htmlspecialchars($scp->getIdSitio() ?? 'REDACTED') ?>
                                    </td>

                                    <td>
                                        <?php if ($scp->hasImage()): ?>
                                            <img src="<?= htmlspecialchars($scp->getImgUrl()) ?>" alt="SCP Img" class="img-thumbnail"
                                                style="height: 50px; width: 50px; object-fit: cover; border-color: var(--accent-color);">
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 0.8rem;">[NO DATA]</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">
                                        <div class="btn-group" role="group">

                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                onclick="openEditSCP('<?= $scp->getId() ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <form action="index.php?action=anomalies_delete" method="POST" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="id" value="<?= $scp->getId() ?>">

                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('WARNING: Are you sure you want to expunge this record?');">
                                                    <i class="bi bi-x-octagon-fill"></i> Neutralized
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
    const popupFeatures = "width=800,height=900,resizable=yes,scrollbars=yes";

    function openCreateSCP() {
        const left = (screen.width - 800) / 2;
        const top = (screen.height - 900) / 2;
        window.open('index.php?action=anomalies_create', 'CreateSCP', `${popupFeatures},top=${top},left=${left}`);
    }

    function openEditSCP(id) {
        const left = (screen.width - 800) / 2;
        const top = (screen.height - 900) / 2;
        window.open(`index.php?action=anomalies_edit&id=${id}`, 'EditSCP', `${popupFeatures},top=${top},left=${left}`);
    }
</script>
<?php require_once 'views/templates/footer.php'; ?>