<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCP - SECURE LOGIN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anonymous+Pro:wght@400;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>views/assets/img/scpLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>views/assets/styles/main.css">

</head>

<body>
    <div class="box-general">
        <div class="header-section">
            <img src="<?php echo BASE_URL; ?>views/assets/img/scpLogo.png" alt="SCP Logo" class="logoSCP">
            <h1>RESTRICTED ACCESS</h1>
            <p class="warning-text">WARNING: AUTHORIZED PERSONNEL ONLY</p>
        </div>

        <form action="index.php?action=authenticate" method="POST" class="login-form" id="login-principal" autocomplete="off">
            <div class="mb-3 text-start">
                <label for="user" class="form-label">OPERATIVE ID</label>
                <input type="text" name="user" id="user" class="form-control scp-input" placeholder="Enter ID..." autocomplete="off">

            </div>
            <!--Alert -->
            <div class="alert alert-danger mb-3 bg-dark border border-danger  scp-error" role="alert" id="errorUser" hidden>
            </div>
            <div class="mb-4 text-start">
                <label for="password" class="form-label">SECURITY CLEARANCE</label>
                <input type="password" name="password" id="password" class="form-control scp-input" placeholder="Enter Password...">
            </div>
            <!--Alert -->
            <div class="alert alert-danger mb-3 bg-dark border border-danger  scp-error" role="alert" id="errorPassword" hidden>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-scp" id="btnLogin">AUTHENTICATE</button>
            </div>
            <br>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger mb-3 bg-dark border border-danger  scp-error" role="alert">' . $_SESSION['error'] . "</div>";
                unset($_SESSION['error']);
            }
            ?>
            <br>
            <div class="header-section">
                <p>Are you new? <a href="index.php?action=register" class="link-secondary">register here</a>.</h1>
            </div>
        </form>

        <div class="footer-code">
            SECURE. CONTAIN. PROTECT.
        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>views/assets/js/controlLogin.js"></script>
</body>

</html>