<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link id="theme-style" rel="stylesheet" href="<?php echo BASE_URL; ?>views/assets/styles/themes/<?php echo $_SESSION['theme'] ?? 'gears.css'; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>views/assets/img/scpLogo.png" type="image/x-icon">
</head>

<body>
    <header>
        <div class="logo-area">
            <img src="<?php echo BASE_URL; ?>views/assets/img/SCP_Foundation.svg" alt="SCP Logo" class="scp-logo">
            <div class="security-level">CLEARANCE LEVEL: <?php echo $_SESSION['level'] ?? 'UNKNOWN'; ?> <span class="clearance"><?php echo $_SESSION['nombre'] ?? 'Guest'; ?></span></div>
        </div>
        <nav>
            <ul>
                <li><a href="#">Pending Tasks</a></li>
                <li><a href="#">SCP Wiki</a></li>
                <li><a href="#">Personal Notes</a></li>
                <!--Database.php solo se muestra si es nivel 5 o mas-->
                <li><a href="#">Database</a></li>
                <li><a href="index.php?action=logout" style="color: #ff4444;">[LOG OUT]</a></li>
            </ul>
        </nav>
    </header>