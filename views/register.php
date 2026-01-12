<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCP - REGISTER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anonymous+Pro:wght@400;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/img/scpLogo.png" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>views/assets/styles/secondary.css">
</head>

<body>
    <div class="box-general">
        <div class="header-section">
            <img src="<?php echo BASE_URL; ?>views/assets/img/scpLogo.png" alt="SCP Logo" class="logoSCP">
            <h1>REGISTER NEW OPERATIVE</h1>
            <p class="warning-text">AUTHORIZED PERSONNEL REGISTRATION</p>
        </div>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger mb-3 bg-dark border border-danger scp-error" role="alert">' . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success mb-3 bg-dark border border-success" role="alert" style="color: #4aff4a; font-weight: bold;">' . htmlspecialchars($_SESSION['success']) . "</div>";
            unset($_SESSION['success']);
        }
        ?>
        <form action="index.php?action=registerprocess" method="POST" class="login-form" id="register-form" autocomplete="off">

            <div class="mb-3 text-start">
                <label for="id" class="form-label">OPERATIVE ID (USERNAME)</label>
                <input type="text" name="id" id="id" class="form-control scp-input" placeholder="Enter operative ID..." autocomplete="off">
            </div>
            <div class="alert alert-danger mb-3 bg-dark border border-danger scp-error" role="alert" id="errorId" hidden></div>

            <div class="mb-3 text-start">
                <label for="nombre" class="form-label">FIRST NAME</label>
                <input type="text" name="name" id="name" class="form-control scp-input" placeholder="Enter first name..." autocomplete="off">
            </div>
            <div class="alert alert-danger mb-3 bg-dark border border-danger scp-error" role="alert" id="errorFirstName" hidden></div>

            <div class="mb-3 text-start">
                <label for="apellido" class="form-label">LAST NAME</label>
                <input type="text" name="lastname" id="lastname" class="form-control scp-input" placeholder="Enter last name..." autocomplete="off">
            </div>
            <div class="alert alert-danger mb-3 bg-dark border border-danger scp-error" role="alert" id="errorLastName" hidden></div>

            <div class="mb-3 text-start">
                <label for="email" class="form-label">EMAIL ADDRESS</label>
                <input type="email" name="email" id="email" class="form-control scp-input" placeholder="Enter email..." autocomplete="off">
            </div>
            <div class="alert alert-danger mb-3 bg-dark border border-danger scp-error" role="alert" id="errorEmail" hidden></div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label">SECURITY CLEARANCE (PASSWORD)</label>
                <input type="password" name="password" id="password" class="form-control scp-input" placeholder="Enter password...">
                <small class="form-text text-muted">Min 8 chars, alphanumeric & symbols allowed.</small>
            </div>
            <div class="alert alert-danger mb-3 bg-dark border border-danger scp-error" role="alert" id="errorPassword" hidden></div>

            <div class="mb-4 text-start">
                <label for="confirm_password" class="form-label">CONFIRM SECURITY CLEARANCE</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control scp-input" placeholder="Confirm password...">
            </div>
            <div class="alert alert-danger mb-3 bg-dark border border-danger scp-error" role="alert" id="errorConfirmPassword" hidden></div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-scp" id="btnRegister">REGISTER OPERATIVE</button>
            </div>

            <div class="text-center mt-3">
                <a href="index.php?action=login" class="link-secondary" style="text-decoration: none;">Already registered? Return to LOGIN</a>
            </div>
        </form>

        <div class="footer-code">
            SECURE. CONTAIN. PROTECT.
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>views/assets/js/controlRegister.js"></script>
</body>

</html>