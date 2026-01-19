<?php
// views/wiki/detail.php
if (!isset($scp)) {
    header("Location: index.php?action=wiki_index");
    exit();
}
$pageTitle = "SCP Foundation - " . $scp->getId();
require_once 'views/templates/header.php';
?>
<link rel="stylesheet" href="views/wiki/assets/styles/detail.css">

<main class="container my-4">

    <div class="row">
        <div class="col-12 back-btn">
            <a href="index.php?action=scpwiki" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> RETURN TO ARCHIVES
            </a>
        </div>
    </div>

    <div class="wiki-container clearfix">

        <div class="wiki-header">
            <?php echo htmlspecialchars($scp->getId()); ?>
        </div>

        <div class="scp-image-block">
            <?php if ($scp->hasImage()): ?>
                <img src="<?php echo htmlspecialchars($scp->getImgUrl()); ?>" alt="SCP Image">
                <div class="scp-image-caption">
                    Depiction of <?php echo htmlspecialchars($scp->getId()); ?> in containment.
                </div>
            <?php else: ?>
                <div style="height: 200px; background: #000; color: #fff; display: flex; align-items: center; justify-content: center;">
                    [REDACTED]
                </div>
                <div class="scp-image-caption">Image file corrupted or expunged.</div>
            <?php endif; ?>
        </div>

        <div class="report-text">
            <p>
                <span class="label">Item #:</span> <?php echo htmlspecialchars($scp->getId()); ?>
            </p>

            <p>
                <span class="label">Object Class:</span>
                <span style="text-transform: capitalize;"><?php echo htmlspecialchars($scp->getClass()); ?></span>
            </p>

            <br>

            <p>
                <span class="label">Special Containment Procedures:</span><br>
                <?php echo nl2br(htmlspecialchars($scp->getContencion())); ?>
            </p>

            <hr style="border-top: 1px solid #999;">

            <p>
                <span class="label">Description:</span><br>
                <?php echo nl2br(htmlspecialchars($scp->getDescription())); ?>
            </p>

            <br>

            <?php if ($scp->getDocExtensa()): ?>
                <div class="alert alert-secondary mt-4" style="border: 1px dashed #666; background: #e0e0e0;">
                    <span class="label">Addendum:</span> Extended documentation is available.
                    <br><br>
                    <a href="<?php echo htmlspecialchars($scp->getDocExtensa()); ?>" target="_blank" class="text-danger fw-bold">
                        [<i class="bi bi-shield-lock-fill"></i> ACCESS FILE ATTACHMENT ]
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>

</main>

<?php require_once 'views/templates/footer.php'; ?>