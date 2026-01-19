<?php
$pageTitle = "Edit Personnel File";
require_once 'views/templates/header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>views/CRUD/users/assets/styles/main.css">

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="form-section shadow">
                <h2 class="mb-4" style="font-family: var(--font-mono); color: #ffc107; border-bottom: 2px solid #ffc107;">
                    <i class="fas fa-user-edit"></i> UPDATE PERSONNEL FILE
                </h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php
                        echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>
                <form action="index.php?action=users_update" method="POST" id="createUserForm">

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user->getId()) ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Operative ID (Locked)</label>
                            <input type="text" value="<?= htmlspecialchars($user->getId()) ?>" class="form-control" disabled style="background:#333; color:#aaa;">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" placeholder="(Leave blank to keep current)">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-color: #6c757d;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="errorPassword" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user->getname()) ?>">
                            <div id="errorName" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control" value="<?= htmlspecialchars($user->getlastname()) ?>">
                            <div id="errorLastName" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user->getEmail()) ?>">
                            <div id="errorEmail" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Terminal Theme</label>
                            <select name="theme" id="theme" class="form-select">
                                <option value="gears" <?= $user->getTheme() == 'gears' ? 'selected' : '' ?>>Gears (Standard)</option>
                                <option value="unicorn" <?= $user->getTheme() == 'unicorn' ? 'selected' : '' ?>>Unicorn (Dr. Afton)</option>
                                <option value="ice" <?= $user->getTheme() == 'ice' ? 'selected' : '' ?>>Ice (Cryo)</option>
                                <option value="admin" <?= $user->getTheme() == 'admin' ? 'selected' : '' ?>>Admin (High Contrast)</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Clearance Level</label>
                            <input type="number" name="level" id="level" class="form-control" min="0" max="10" value="<?= $user->getLevel() ?>">
                            <div id="errorLevel" hidden></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned Role</label>
                            <select name="rol" id="rol" class="form-select">
                                <option value="cleaner" <?= $user->getRol() == 'cleaner' ? 'selected' : '' ?>>Cleaner</option>
                                <option value="researcher" <?= $user->getRol() == 'researcher' ? 'selected' : '' ?>>Researcher</option>
                                <option value="security" <?= $user->getRol() == 'security' ? 'selected' : '' ?>>Security</option>
                                <option value="scienct" <?= $user->getRol() == 'scienct' ? 'selected' : '' ?>>Scientist</option>
                                <option value="admin" <?= $user->getRol() == 'admin' ? 'selected' : '' ?>>Administrator</option>
                                <option value="class-d" <?= $user->getRol() == 'class-d' ? 'selected' : '' ?>>Class-D</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="state" id="stateCheck" <?= $user->isstate() ? 'checked' : '' ?>>
                        <label class="form-check-label" for="stateCheck">User Active</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> CANCEL
                        </button>
                        <button type="submit" class="btn btn-warning" style="color: #000;">
                            <i class="fas fa-sync-alt"></i> UPDATE PERSONNEL
                        </button>
                    </div>
                </form>

                <script src="<?php echo BASE_URL; ?>views/CRUD/users/assets/js/usersCreate.js"></script>
            </div>
        </div>
    </div>
</main>

<?php require_once 'views/templates/footer.php'; ?>