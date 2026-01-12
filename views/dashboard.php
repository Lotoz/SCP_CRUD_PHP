<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCP Foundation - Restricted Access</title>
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

    <main>
        <div class="msgUser container">
            <h1>Welcome, <span class="user-name"><?php echo $_SESSION['name'] ?? 'Agent'; ?></span></h1>
            <p>Remember to log off. The Foundation is watching. <br>
                <strong>WARNING:</strong> Unauthorized access will be monitored and terminated.
            </p>
        </div>

        <div class="dashboard-grid">
            <div class="card taskPendients">
                <h2>📋 Pending Tasks</h2>
                <ul>
                    <li>Review SCP-173 containment</li>
                    <li>Approve Site-19 budget</li>
                    <li><span class="redacted">[REDACTED]</span> weekly report</li>
                </ul>
            </div>

            <div class="card alertsContention">
                <h2>⚠️ Containment Alerts</h2>
                <div class="alert-item">
                    <strong>SCP-682:</strong> Breach attempt detected.
                </div>
                <div class="alert-item">
                    <strong>SCP-096:</strong> Status: Docile.
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>SECURE, CONTAIN, PROTECT.</p>
        <small>© SCP Foundation</small>
    </footer>
</body>

</html>