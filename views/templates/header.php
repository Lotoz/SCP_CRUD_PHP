<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SCP Foundation'; ?></title>

    <link id="theme-style" rel="stylesheet" href="<?php echo BASE_URL; ?>views/assets/styles/themes/<?php echo $_SESSION['theme'] ?? 'gears.css'; ?>">

    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>views/assets/img/scpLogo.png" type="image/x-icon">
</head>

<body>
    <header>
        <div class="logo-area">
            <img src="<?php echo BASE_URL; ?>views/assets/img/SCP_Foundation.svg" alt="SCP Logo" class="scp-logo">
            <div class="security-level">
                CLEARANCE LEVEL: <?php echo $_SESSION['level'] ?? '0'; ?>
                <span class="clearance"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Guest'); ?></span>
            </div>
        </div>

        <nav>
            <ul>
                <li><a href="index.php?action=dashboard">Dashboard</a></li>
                <li><a href="index.php?action=scpwiki">SCP Wiki</a></li>
                <li><a href="index.php?action=task_index">Tasks</a></li>

                <?php if (isset($_SESSION['level']) && $_SESSION['level'] >= 3): ?>
                    <li><a href="index.php?action=sites_index">Sites</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['level']) && $_SESSION['level'] >= 4): ?>
                    <li><a href="index.php?action=anomalies_index">Anomalies</a></li>
                    <li><a href="index.php?action=assigned_index">Personal-Assigned</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['level']) && $_SESSION['level'] >= 5): ?>
                    <li><a href="index.php?action=users_index">Personnel</a></li>
                    <li><a href="index.php?action=exempleados_index">EX-Personnel</a></li>
                <?php endif; ?>

                <li>
                    <a href="index.php?action=logout" style="color: var(--alert-color, #ff4444);">
                        [LOG OUT]
                    </a>
                </li>
            </ul>
        </nav>
    </header>