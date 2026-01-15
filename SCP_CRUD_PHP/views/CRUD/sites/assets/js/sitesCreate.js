/**
 * views/CRUD/assets/js/sitesCreate.js
 */

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('createSiteForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {

        const nameInput = document.getElementById('name_sitio');
        const locInput = document.getElementById('ubicacion');
        const adminInput = document.getElementById('id_administrador');

        const errorName = document.getElementById('errorName');
        const errorLoc = document.getElementById('errorLocation');
        const errorAdmin = document.getElementById('errorAdmin');

        // Reset
        [errorName, errorLoc, errorAdmin].forEach(div => {
            div.innerHTML = "";
            div.hidden = true;
            div.classList.remove('text-danger', 'small', 'mt-1');
        });

        let isValid = true;

        // 1. Validate Name
        if (nameInput.value.trim().length < 3) {
            showError(errorName, 'Site Designation is too short.');
            isValid = false;
        }

        // 2. Validate Location
        if (locInput.value.trim() === '') {
            showError(errorLoc, 'Location is required.');
            isValid = false;
        }

        // 3. Validate Admin ID (Optional but if present check format)
        if (adminInput.value.trim() !== '') {
            // Check if it has spaces (IDs shouldn't have spaces based on previous rules)
            if (/\s/.test(adminInput.value)) {
                showError(errorAdmin, 'User ID cannot contain spaces.');
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function showError(element, message) {
        element.hidden = false;
        element.classList.add('text-danger', 'small', 'mt-1');
        element.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>ERROR:</strong> ${message}`;
    }
});