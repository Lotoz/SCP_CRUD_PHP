<?php
$pageTitle = "New Personnel Onboarding";
require_once 'views/templates/header.php';
?>

<style>
    header nav,
    .logo-area .security-level {
        display: none;
    }

    header {
        justify-content: center;
        border-bottom: none;
    }

    body {
        padding-bottom: 2rem;
        background-color: var(--bg-color);
    }

    .form-section {
        background: var(--card-bg);
        padding: 20px;
        border: 1px solid var(--accent-color);
    }
</style>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="form-section shadow">
                <h2 class="mb-4" style="font-family: var(--font-mono); color: var(--highlight-color); border-bottom: 2px solid var(--accent-color);">
                    <i class="fas fa-id-card"></i> PERSONNEL RECRUITMENT FORM
                </h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert"
                        style="border: 1px solid red; color: #000;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>ERROR:</strong>
                        <?php
                        echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form action="index.php?action=users_store" method="POST" id="createUserForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Operative ID (Username)</label>
                            <input type="text" name="id" id="id" class="form-control" style="font-family: var(--font-mono);">
                            <div id="errorId" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Initial Password</label>

                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control">

                                <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                    style="border-color: #ced4da; /* O usa var(--accent-color) si prefieres borde de color */">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="errorPassword" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                            <div id="errorName" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control">
                            <div id="errorLastName" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control">
                            <div id="errorEmail" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Terminal Theme</label>
                            <select name="theme" id="theme" class="form-select">
                                <option value="gears">Gears (Standard)</option>
                                <option value="unicorn">Unicorn (Dr. Afton)</option>
                                <option value="ice">Ice (Cryo)</option>
                                <option value="admin">Admin (High Contrast)</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Clearance Level</label>
                            <input type="number" name="level" id="level" class="form-control" min="1" max="5" value="1">
                            <small class="text-muted">1 = Janitor, 5 = O5 Council</small>
                            <div id="errorLevel" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned Role</label>
                            <select name="rol" id="rol" class="form-select">
                                <option value="cleaner">Cleaner</option>
                                <option value="researcher">Researcher</option>
                                <option value="security">Security</option>
                                <option value="scienct">Scientist</option>
                                <option value="admin">Administrator</option>
                                <option value="class-d">Class-D</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="state" id="stateCheck" checked>
                        <label class="form-check-label" for="stateCheck">Activate User Immediately</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn" style="background-color: var(--accent-color); color: #fff;">
                            <i class="fas fa-user-plus"></i> REGISTER PERSONNEL
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="<?php echo BASE_URL; ?>views/CRUD/users/assets/js/usersCreate.js"></script>
<?php require_once 'views/templates/footer.php'; ?>