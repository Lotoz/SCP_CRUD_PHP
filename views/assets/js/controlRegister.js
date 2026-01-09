/**
 * SCP Registration Controller
 */
document.getElementById('register-form').addEventListener('submit', function (e) {
    // --- INPUTS ---
    const idInput = document.getElementById('id');
    const nameInput = document.getElementById('nombre');
    const lastNameInput = document.getElementById('apellido');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    // --- ERROR CONTAINERS ---
    const errorIdDiv = document.getElementById('errorId');
    const errorNameDiv = document.getElementById('errorFirstName');
    const errorLastNameDiv = document.getElementById('errorLastName');
    const errorEmailDiv = document.getElementById('errorEmail');
    const errorPassDiv = document.getElementById('errorPassword');
    const errorConfirmPassDiv = document.getElementById('errorConfirmPassword');

    let isValid = true;

    // --- RESET ERRORS ---
    const errorDivs = [errorIdDiv, errorNameDiv, errorLastNameDiv, errorEmailDiv, errorPassDiv, errorConfirmPassDiv];
    errorDivs.forEach(div => {
        if (div) {
            div.innerHTML = "";
            div.hidden = true;
        }
    });

    // --- REGEX PATTERNS ---
    const idRegex = /^[a-zA-Z0-9_-]+$/;
    const passRegex = /^(?!.*["\\\/<>=()]).{8,64}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // --- VALIDATIONS ---

    // 1. Validate Operative ID (NO SPACES ALLOWED)
    if (idInput.value.trim() === '') {
        showError(errorIdDiv, 'Operative ID is required.');
        isValid = false;
    } else if (/\s/.test(idInput.value)) { 
        // Nueva validación explícita para espacios en blanco
        showError(errorIdDiv, 'Invalid ID format. Spaces are not allowed.');
        isValid = false;
    } else if (!idRegex.test(idInput.value)) {
        showError(errorIdDiv, 'Invalid ID format. Only letters, numbers, "-" and "_" allowed.');
        isValid = false;
    }

    // 2. Validate First Name
    if (nameInput.value.trim() === '') {
        showError(errorNameDiv, 'First Name is required.');
        isValid = false;
    }

    // 3. Validate Last Name
    if (lastNameInput.value.trim() === '') {
        showError(errorLastNameDiv, 'Last Name is required.');
        isValid = false;
    }

    // 4. Validate Email
    if (emailInput.value.trim() === '') {
        showError(errorEmailDiv, 'Email address is required.');
        isValid = false;
    } else if (!emailRegex.test(emailInput.value.trim())) {
        showError(errorEmailDiv, 'Invalid email format.');
        isValid = false;
    }

    // 5. Validate Password
    if (passwordInput.value.trim() === '') {
        showError(errorPassDiv, 'Security Clearance (Password) is required.');
        isValid = false;
    } else if (!passRegex.test(passwordInput.value)) {
        showError(errorPassDiv, 'Password must be 8-64 chars long and free of prohibited characters (<, >, ", etc).');
        isValid = false;
    }

    // 6. Validate Password Match
    if (confirmPasswordInput.value.trim() === '') {
        showError(errorConfirmPassDiv, 'Please confirm your Security Clearance.');
        isValid = false;
    } else if (passwordInput.value !== confirmPasswordInput.value) {
        showError(errorConfirmPassDiv, 'Security Clearances do not match.');
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault(); 
    }
});

function showError(element, message) {
    if (element) {
        element.hidden = false;
        element.innerHTML = `<span class="bi bi-exclamation-triangle-fill"></span> <strong>ERROR:</strong> ${message}`;
    } else {
        alert(message);
    }
}