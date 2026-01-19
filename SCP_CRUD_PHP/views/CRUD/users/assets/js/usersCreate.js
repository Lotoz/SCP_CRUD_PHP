/**
 * views/CRUD/users/assets/js/usersCreate.js
 * Validation logic for the Admin User Creation and Edit Form
 */

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('createUserForm');

    // Safety check to ensure form exists
    if (!form) return;

    form.addEventListener('submit', function (e) {

        // --- INPUTS ---
        const idInput = document.getElementById('id');
        const passwordInput = document.getElementById('password');
        const nameInput = document.getElementById('name');
        const lastNameInput = document.getElementById('lastname');
        const emailInput = document.getElementById('email');
        const levelInput = document.getElementById('level');

        // --- CHECK MODE (CREATE vs EDIT) ---
        // Si el ID está deshabilitado, estamos en modo EDITAR.
        const isEditMode = idInput.hasAttribute('disabled') || idInput.readOnly;

        // --- ERROR CONTAINERS ---
        const errorIdDiv = document.getElementById('errorId');
        const errorPassDiv = document.getElementById('errorPassword');
        const errorNameDiv = document.getElementById('errorName');
        const errorLastNameDiv = document.getElementById('errorLastName');
        const errorEmailDiv = document.getElementById('errorEmail');
        const errorLevelDiv = document.getElementById('errorLevel');

        let isValid = true;

        // --- RESET ERRORS ---
        const errorDivs = [errorIdDiv, errorPassDiv, errorNameDiv, errorLastNameDiv, errorEmailDiv, errorLevelDiv];
        errorDivs.forEach(div => {
            if (div) {
                div.innerHTML = "";
                div.hidden = true;
                div.style.display = 'none';
                div.classList.remove('text-danger', 'small', 'mt-1');
            }
        });

        // --- REGEX PATTERNS ---
        // [MATCH PHP] Only letters, numbers, hyphens and underscores
        const idRegex = /^[a-zA-Z0-9_-]+$/;

        // Password: 8-64 chars, no forbidden chars.
        const passRegex = /^(?!.*["\\\/<>=()]).{8,64}$/;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // --- VALIDATIONS ---

        // 1. Validate Operative ID (ONLY IN CREATE MODE)
        if (!isEditMode) {
            if (idInput.value.trim() === '') {
                showError(errorIdDiv, 'Operative ID is required.');
                isValid = false;
            } else if (/\s/.test(idInput.value)) {
                showError(errorIdDiv, 'Invalid ID format. Spaces are not allowed.');
                isValid = false;
            } else if (!idRegex.test(idInput.value)) {
                // Aquí aplicamos tu regla estricta de PHP
                showError(errorIdDiv, 'ID format error: Only letters, numbers, "-" and "_" allowed.');
                isValid = false;
            }
        }

        // 2. Validate Password
        // En CREATE: Obligatoria. En EDIT: Opcional (solo valida si escriben algo).
        const passValue = passwordInput.value;
        if (!isEditMode && passValue.trim() === '') {
            // Caso Create: Vacía
            showError(errorPassDiv, 'Initial Password is required.');
            isValid = false;
        } else if (passValue.length > 0 && !passRegex.test(passValue)) {
            // Caso Ambos: Si escribieron algo, debe cumplir el formato
            showError(errorPassDiv, 'Password must be 8-64 chars long and avoid special system characters (<, >, ", etc).');
            isValid = false;
        }

        // 3. Validate First Name
        if (nameInput.value.trim() === '') {
            showError(errorNameDiv, 'First Name is required.');
            isValid = false;
        }

        // 4. Validate Last Name
        if (lastNameInput.value.trim() === '') {
            showError(errorLastNameDiv, 'Last Name is required.');
            isValid = false;
        }

        // 5. Validate Email
        if (emailInput.value.trim() === '') {
            showError(errorEmailDiv, 'Email address is required.');
            isValid = false;
        } else if (!emailRegex.test(emailInput.value.trim())) {
            showError(errorEmailDiv, 'Invalid email format.');
            isValid = false;
        }

        // 6. Validate Level
        const levelValue = parseInt(levelInput.value);
        if (isNaN(levelValue) || levelValue <= 0 || levelValue > 10) {
            showError(errorLevelDiv, 'Clearance Level must be between 1 and 5.(Admin levels 6-10 are restricted)');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function showError(element, message) {
        if (element) {
            element.hidden = false;
            element.style.display = 'block';
            element.classList.add('text-danger', 'small', 'mt-1');
            element.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>ERROR:</strong> ${message}`;
        } else {
            alert(message);
        }
    }
});

/* LOGICA PARA MOSTRAR/OCULTAR CONTRASEÑA */
const togglePassword = document.querySelector('#togglePassword');
const passwordInput = document.querySelector('#password');

if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        const icon = this.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    });
}