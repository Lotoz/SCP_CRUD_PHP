/**
 * views/CRUD/assets/js/usersCreate.js
 * Validation logic for the Admin User Creation Form
 */

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('createUserForm');

    // Safety check to ensure form exists
    if (!form) return;

    form.addEventListener('submit', function (e) {

        // --- INPUTS ---
        // I grab elements by their ID
        const idInput = document.getElementById('id');
        const passwordInput = document.getElementById('password');
        const nameInput = document.getElementById('name');
        const lastNameInput = document.getElementById('lastname');
        const emailInput = document.getElementById('email');
        const levelInput = document.getElementById('level');

        // --- ERROR CONTAINERS ---
        const errorIdDiv = document.getElementById('errorId');
        const errorPassDiv = document.getElementById('errorPassword');
        const errorNameDiv = document.getElementById('errorName'); // Changed from FirstName to match generic
        const errorLastNameDiv = document.getElementById('errorLastName');
        const errorEmailDiv = document.getElementById('errorEmail');
        const errorLevelDiv = document.getElementById('errorLevel');

        let isValid = true;

        // --- RESET ERRORS ---
        // I hide all previous error messages
        const errorDivs = [errorIdDiv, errorPassDiv, errorNameDiv, errorLastNameDiv, errorEmailDiv, errorLevelDiv];
        errorDivs.forEach(div => {
            if (div) {
                div.innerHTML = "";
                div.hidden = true; // Using the 'hidden' attribute as in your example
                div.style.display = 'none'; // Double safety for bootstrap/css
                div.classList.remove('text-danger', 'small', 'mt-1', 'd-block');
            }
        });

        // --- REGEX PATTERNS ---
        const idRegex = /^[a-zA-Z0-9_-]+$/;
        // Password: 8-64 chars, no forbidden chars.
        const passRegex = /^(?!.*["\\\/<>=()]).{8,64}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // --- VALIDATIONS ---

        // 1. Validate Operative ID
        if (idInput.value.trim() === '') {
            showError(errorIdDiv, 'Operative ID is required.');
            isValid = false;
        } else if (/\s/.test(idInput.value)) {
            showError(errorIdDiv, 'Invalid ID format. Spaces are not allowed.');
            isValid = false;
        } else if (!idRegex.test(idInput.value)) {
            showError(errorIdDiv, 'Invalid ID format. Only letters, numbers, "-" and "_" allowed.');
            isValid = false;
        }

        // 2. Validate Password (Initial)
        if (passwordInput.value.trim() === '') {
            showError(errorPassDiv, 'Initial Password is required.');
            isValid = false;
        } else if (!passRegex.test(passwordInput.value)) {
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

        // 6. Validate Level (Numeric Range)
        const levelValue = parseInt(levelInput.value);
        if (isNaN(levelValue) || levelValue < 1 || levelValue > 5) {
            showError(errorLevelDiv, 'Clearance Level must be between 1 and 5.');
            isValid = false;
        }

        // If any validation failed, I prevent the form submission
        if (!isValid) {
            e.preventDefault();
        }
    });

    /**
     * Helper function to display errors
     */
    function showError(element, message) {
        if (element) {
            element.hidden = false;
            element.style.display = 'block';
            // Added some Bootstrap classes for styling
            element.classList.add('text-danger', 'small', 'mt-1');
            element.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>ERROR:</strong> ${message}`;
        } else {
            // Fallback if the div doesn't exist in HTML
            alert(message);
        }
    }
});