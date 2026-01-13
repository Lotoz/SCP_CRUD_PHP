<?php
// views/wiki/index.php

$pageTitle = "SCP Foundation Database - Access Clearance Verified";
require_once 'views/templates/header.php';

// --- SIMULACIÓN DE DATOS (Esto vendrá de tu base de datos real luego) ---
// Cuando programes tu lógica, $anomalies vendrá de $scpController->getByLevel($_SESSION['level']);
if (!isset($anomalies)) {
    $anomalies = [
        [
            'id' => 'SCP-173',
            'apodo' => 'The Sculpture',
            'class' => 'Euclid',
            'imagen' => 'https://upload.wikimedia.org/wikipedia/commons/e/ec/SCP-173_fan_art.png', // Placeholder
            'descripcion' => 'Moved to Site-19 in 1993. Origin is as of yet unknown. It is constructed from concrete and rebar with traces of Krylon brand spray paint.'
        ],
        [
            'id' => 'SCP-096',
            'apodo' => 'The Shy Guy',
            'class' => 'Euclid',
            'imagen' => 'https://static.wikia.nocookie.net/villains/images/2/22/SCP-096_model.jpg',
            'descripcion' => 'SCP-096 is a humanoid creature measuring approximately 2.38 meters in height. Subject shows very little muscle mass.'
        ],
        [
            'id' => 'SCP-682',
            'apodo' => 'Hard-to-Destroy Reptile',
            'class' => 'Keter',
            'imagen' => 'https://static.wikia.nocookie.net/scp-foundation-reboot/images/e/e4/SCP-682.jpg',
            'descripcion' => 'SCP-682 is a large, vaguely reptile-like creature of unknown origin. It appears to be extremely intelligent.'
        ],
        [
            'id' => 'SCP-999',
            'apodo' => 'The Tickle Monster',
            'class' => 'Safe',
            'imagen' => 'https://static.wikia.nocookie.net/villains/images/e/e3/SCP-999_D.png',
            'descripcion' => 'SCP-999 appears to be a large, amorphous, gelatinous mass of translucent orange slime.'
        ]
    ];
}
?>

<main class="container my-5">

    <div class="row mb-4">
        <div class="col-12">
            <div class="msgUser text-center" style="border-left-color: #d9534f;">
                <h1 style="font-family: 'Share Tech Mono'; text-transform: uppercase; letter-spacing: 2px;">
                    ⚠ Classified Archives
                </h1>
                <p class="mb-0">
                    <strong>CLEARANCE VERIFIED:</strong> Level <?php echo $_SESSION['level'] ?? '1'; ?> Personnel.
                    <br>
                    <span class="text-muted">Displaying accessible anomalies based on your security clearance.</span>
                </p>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-8 mx-auto">
            <div class="input-group">
                <span class="input-group-text bg-dark text-white border-dark" style="font-family: 'Share Tech Mono';">CMD://SEARCH_</span>
                <input type="text" class="form-control border-dark" placeholder="Enter Item # or Keywords...">
                <button class="btn btn-dark" type="button">EXECUTE</button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($anomalies as $scp): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card scp-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong style="font-family: 'Share Tech Mono'; font-size: 1.2rem;">
                            <?php echo $scp['id']; ?>
                        </strong>
                        <span class="badge scp-class-badge <?php echo strtolower($scp['class']); ?>">
                            <?php echo strtoupper($scp['class']); ?>
                        </span>
                    </div>

                    <div class="scp-img-container">
                        <img src="<?php echo $scp['imagen'] ?? 'views/assets/img/redacted_image.png'; ?>"
                            alt="<?php echo $scp['apodo']; ?>" class="card-img-top scp-img">
                        <div class="overlay">
                            <span>ACCESS FILE</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title text-uppercase fw-bold border-bottom pb-2">
                            <?php echo $scp['apodo']; ?>
                        </h5>
                        <p class="card-text text-muted scp-description">
                            <strong style="font-family: 'Share Tech Mono';">CONTAINMENT:</strong><br>
                            <?php
                            // Recortamos la descripción para que no sea muy larga
                            echo substr($scp['descripcion'], 0, 120) . '...';
                            ?>
                        </p>
                    </div>

                    <div class="card-footer bg-transparent border-top-0 pb-3">
                        <a href="index.php?action=wiki_detail&id=<?php echo $scp['id']; ?>" class="btn btn-outline-dark w-100" style="font-family: 'Share Tech Mono';">
                            [ OPEN FILE ]
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-secondary text-center" role="alert" style="border: 2px dashed #444;">
                <h4 style="font-family: 'Share Tech Mono';">EXTERNAL DATABASE LINK</h4>
                <p>For extended documentation and archival records, please refer to the central mainframe.</p>
                <a href="https://scp-wiki.wikidot.com/" target="_blank" class="btn btn-dark">
                    ACCESS SCP-WIKI.WIKIDOT.COM
                </a>
                <p class="mt-2 mb-0"><small>Warning: You are leaving the secure intranet.</small></p>
            </div>
        </div>
    </div>

</main>

<style>
    /* Estilo base de la tarjeta SCP */
    .scp-card {
        border: 1px solid #999;
        border-radius: 2px;
        /* Bordes cuadrados estilo documento */
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .scp-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        border-color: #333;
    }

    .card-header {
        background-color: #e9ecef;
        border-bottom: 2px solid #333;
    }

    /* Imagen con efecto de hover */
    .scp-img-container {
        position: relative;
        height: 200px;
        overflow: hidden;
        background-color: #000;
        border-bottom: 1px solid #ddd;
    }

    .scp-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
        filter: grayscale(80%);
        /* Efecto blanco y negro para dar realismo */
        transition: filter 0.3s;
    }

    .scp-card:hover .scp-img {
        filter: grayscale(0%);
        /* Color al pasar el mouse */
    }

    /* Colores de las Clases (Importante para lore SCP) */
    .scp-class-badge {
        font-family: 'Share Tech Mono', monospace;
        font-size: 0.9rem;
        border: 1px solid rgba(0, 0, 0, 0.2);
        color: #fff;
        text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.5);
    }

    .safe {
        background-color: #28a745;
        /* Verde */
    }

    .euclid {
        background-color: #ffc107;
        /* Amarillo */
        color: #000;
        /* Texto negro para contraste */
    }

    .keter {
        background-color: #dc3545;
        /* Rojo */
    }

    .apollyn {
        background-color: #000;
        /* Negro */
        border: 1px solid #dc3545;
    }

    .neutralized,
    .anulado {
        background-color: #6c757d;
        /* Gris */
    }

    /* Tipografía técnica para la descripción */
    .scp-description {
        font-size: 0.9rem;
        line-height: 1.5;
    }
</style>

<?php require_once 'views/templates/footer.php'; ?>