<?php
// Define el titulo de la página
$pageTitle = "SCP Foundation - DataBases";

// Incluye el header
require_once 'views/templates/header.php';
?>
<main>
    <!--Se debe de ver el nivel, si es de nivel 5 tiene todos los links, si es de nivel 4 solo tiene el link de usuarios-->
    <h1>Selecciona la base de datos a editar</h1>
    <ul>

        <?php
        if ($_SESSION['level'] <= 5) {
            echo '<li><a href="">Users</a></li>
                <li><a href="">Sites</a></li>
                <li><a href="">Anomalies</a></li>
                <li><a href="">Tasks</a></li>
                <li><a href="">Personal assignment</a></li>
                <li><a href="">EX-Empleados</a></li>';
        } else if ($_SESSION['level'] == 4) {
            echo '<li><a href="">Users</a></li>
                <li><a href="">SCP</a></li>
                <li><a href="">Tasks</a></li>
                <li><a href="">Personal assignment</a></li>
                <li><a href="">EX-Empleados</a></li>';
        } else if ($_SESSION['level'] >= 3) {
            echo '<li><a href="">SCP</a></li>>';
        } else if ($_SESSION['level'] >= 1) {
            echo 'No tienes los permisos suficientes para zona.';
        }
        ?>
    </ul>

</main>

<?php
// Incluye el footer al final, para cerrar las etiquetas HTML correctamente
require_once 'views/templates/footer.php';
?>