/**
 * views/CRUD/assets/js/taskCreate.js
 */
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('createTaskForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {

        const descInput = document.getElementById('description');
        const errorDesc = document.getElementById('errorDesc');

        // Reset error
        errorDesc.innerHTML = "";
        errorDesc.hidden = true;
        errorDesc.classList.remove('text-danger', 'small', 'mt-1');

        let isValid = true;

        // Validation: Description required
        if (descInput.value.trim() === '') {
            errorDesc.hidden = false;
            errorDesc.classList.add('text-danger', 'small', 'mt-1');
            errorDesc.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>ERROR:</strong> Description cannot be empty.`;
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});