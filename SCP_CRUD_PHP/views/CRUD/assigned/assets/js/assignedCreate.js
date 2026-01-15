/**
 * views/CRUD/assets/js/assignedCreate.js
 */
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('createAssignedForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {

        const scpInput = document.getElementById('scp_id');
        const userInput = document.getElementById('user_id');

        const errorScp = document.getElementById('errorScp');
        const errorUser = document.getElementById('errorUser');

        // Reset
        [errorScp, errorUser].forEach(div => {
            div.innerHTML = "";
            div.hidden = true;
            div.classList.remove('text-danger', 'small', 'mt-1');
        });

        let isValid = true;

        // 1. Validate SCP ID
        if (scpInput.value.trim() === '') {
            showError(errorScp, 'SCP ID is required.');
            isValid = false;
        }

        // 2. Validate User ID
        if (userInput.value.trim() === '') {
            showError(errorUser, 'User ID is required.');
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

    // Auto-uppercase SCP ID
    const scpInput = document.getElementById('scp_id');
    scpInput.addEventListener('blur', function () {
        if (this.value && !this.value.startsWith('SCP-') && !isNaN(this.value)) {
            this.value = 'SCP-' + this.value;
        }
        this.value = this.value.toUpperCase();
    });
});