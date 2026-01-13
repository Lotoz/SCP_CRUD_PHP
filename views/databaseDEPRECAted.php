<?php
// views/database/index.php

$pageTitle = "SCP Database Control - Level 5 Clearance";
require_once 'views/templates/header.php';

// Capturamos la tabla actual de la URL, por defecto 'users'
$currentTable = $_GET['table'] ?? 'users';

// Lógica Visual: Definir los nombres de las columnas según la tabla
$colName = "Name / Designation";
$colStatus = "Status / Role";

switch ($currentTable) {
    case 'sites':
        $colName = "Site Name";
        $colStatus = "Location / Admin";
        break;
    case 'personal_asignado':
        $colName = "SCP ID"; // En esta tabla de relación, mostramos el ID del SCP como nombre principal
        $colStatus = "Assignment Role";
        break;
    case 'anomalies':
        $colName = "Nickname";
        $colStatus = "Object Class";
        break;
}
?>

<main class="container my-5">

    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 style="font-family: 'Share Tech Mono', monospace; color: #dcdcdc; text-transform: uppercase; letter-spacing: 2px;">
                <span style="color: #ffd700;">⚠</span> Master Database Control
            </h1>
            <p class="text-muted">Secure Access Terminal. All actions are logged.</p>
        </div>
    </div>

    <div class="card bg-dark text-light border-secondary mb-4 shadow-lg">
        <div class="card-header border-secondary">
            <strong style="font-family: 'Share Tech Mono', monospace;">DATABASE SELECTION</strong>
        </div>
        <div class="card-body">
            <form>
                <label for="datasbases" class="form-label text-muted">Select Target Data Stream:</label>
                <select name="datasbases" id="datasbases" class="form-select bg-secondary text-white border-0"
                    onchange="window.location.href='index.php?action=database&table=' + this.value">
                    <option value="users" <?php echo $currentTable == 'users' ? 'selected' : ''; ?>>Personnel Records (Users)</option>
                    <option value="anomalies" <?php echo $currentTable == 'anomalies' ? 'selected' : ''; ?>>Anomalous Objects (SCP)</option>
                    <option value="sites" <?php echo $currentTable == 'sites' ? 'selected' : ''; ?>>Foundation Sites</option>
                    <option value="personal_asignado" <?php echo $currentTable == 'personal_asignado' ? 'selected' : ''; ?>>Assignment Registry</option>
                    <option value="ex-empleados" <?php echo $currentTable == 'ex-empleados' ? 'selected' : ''; ?>>Terminated/Ex-Personnel</option>
                </select>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="index.php?action=create&table=<?php echo $currentTable; ?>" class="btn btn-outline-success">
            + Create New Entry in [<?php echo strtoupper($currentTable); ?>]
        </a>
    </div>

    <div class="card bg-dark text-light border-secondary shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover table-striped mb-0 align-middle">
                    <thead class="table-secondary text-uppercase" style="font-family: 'Share Tech Mono', monospace;">
                        <tr>
                            <th>ID / Code</th>
                            <th><?php echo $colName; ?></th>
                            <th><?php echo $colStatus; ?></th>
                            <th class="text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Verificamos si hay datos para mostrar
                        if (!empty($data) && is_array($data)):
                            foreach ($data as $row):
                                // 1. ID: Intentamos encontrar la clave primaria
                                $id = $row['id'] ?? $row['code'] ?? $row['id_usuario'] ?? 'UNKNOWN';

                                // 2. NOMBRE: Buscamos campos de nombre, apodo o sitio
                                $name = $row['nombre'] ?? $row['name'] ?? $row['apodo'] ?? $row['nombre_sitio'] ?? $row['id_scp'] ?? 'N/A';

                                // 3. ESTADO/ROL: Buscamos clase, rol, ubicación o rol de anomalía
                                $status = $row['class'] ?? $row['rol'] ?? $row['status'] ?? $row['ubicacion'] ?? $row['rol_anomalia'] ?? 'Active';
                        ?>
                                <tr>
                                    <td class="fw-bold text-info"><?php echo htmlspecialchars($id); ?></td>
                                    <td><?php echo htmlspecialchars($name); ?></td>
                                    <td>
                                        <span class="badge <?php
                                                            if ($status === 'Keter' || $status === 'Admin' || $status === 'Anulado') echo 'bg-danger';
                                                            elseif ($status === 'Euclid' || $status === 'Researcher') echo 'bg-warning text-dark';
                                                            else echo 'bg-success'; // Safe, Active, etc.
                                                            ?>">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="index.php?action=show&table=<?php echo $currentTable; ?>&id=<?php echo $id; ?>"
                                            class="btn btn-sm btn-info" title="View details">
                                            👁
                                        </a>

                                        <?php if ($currentTable !== 'ex-empleados'): ?>
                                            <button class="btn btn-sm btn-warning"
                                                onclick="editRecord('<?php echo $currentTable; ?>', '<?php echo $id; ?>')"
                                                title="Edit Record">
                                                ✏
                                            </button>
                                        <?php endif; ?>

                                        <button class="btn btn-sm btn-danger"
                                            onclick="confirmAction('<?php echo $currentTable; ?>', '<?php echo $id; ?>')"
                                            title="Delete / Neutralize">
                                            ⚠
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    // NO DATA FOUND IN STREAM [<?php echo strtoupper($currentTable); ?>]
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<style>
    .table-dark {
        --bs-table-bg: #1c1c1c;
        --bs-table-striped-bg: #2c2c2c;
    }

    .btn-outline-success {
        color: #00ff41;
        border-color: #00ff41;
    }

    .btn-outline-success:hover {
        background-color: #00ff41;
        color: black;
    }
</style>

<script src="views/assets/js/database_control.js"></script>

<?php require_once 'views/templates/footer.php'; ?>