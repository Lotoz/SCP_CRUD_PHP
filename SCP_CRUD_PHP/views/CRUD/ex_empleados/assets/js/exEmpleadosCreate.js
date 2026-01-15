/**
 * views/CRUD/assets/js/exEmpleadosCreate.js
 */
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('createExForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {

        const nameInput = document.getElementById('name');
        const lastnameInput = document.getElementById('lastname');
        const levelInput = document.getElementById('level');

        const errorName = document.getElementById('errorName');
        const errorLastName = document.getElementById('errorLastName');
        const errorLevel = document.getElementById('errorLevel');

        [errorName, errorLastName, errorLevel].forEach(div => {
            div.innerHTML = "";
            div.hidden = true;
            div.classList.remove('text-danger', 'small', 'mt-1');
        });

        let isValid = true;

        if (nameInput.value.trim() === '') {
            showError(errorName, 'Name is required.');
            isValid = false;
        }

        if (lastnameInput.value.trim() === '') {
            showError(errorLastName, 'Last Name is required.');
            isValid = false;
        }

        const levelValue = parseInt(levelInput.value);
        if (isNaN(levelValue) || levelValue < 1 || levelValue > 5) {
            showError(errorLevel, 'Level must be between 1 and 5.');
            isValid = false;
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