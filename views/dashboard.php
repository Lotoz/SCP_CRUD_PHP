<?php
// Define el titulo de la página
$pageTitle = "SCP Foundation - Restricted Access";

// Incluye el header
require_once 'views/templates/header.php';
?>

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

<?php
// Incluye el footer al final, para cerrar las etiquetas HTML correctamente
require_once 'views/templates/footer.php';
?>