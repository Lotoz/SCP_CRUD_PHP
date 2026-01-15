/**
 * views/CRUD/assets/js/anomaliesCreate.js
 * Handles image preview and basic validation for the Create Popup
 */

document.addEventListener('DOMContentLoaded', function () {

    const imgInput = document.getElementById('imgInput');
    const previewContainer = document.getElementById('previewContainer');
    const imgPreview = document.getElementById('imgPreview');

    // Event listener for file selection
    imgInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('ERROR: Only image files are allowed for the visual record.');
                imgInput.value = ''; // Clear input
                previewContainer.style.display = 'none';
                return;
            }

            // Create a preview using FileReader
            const reader = new FileReader();

            reader.onload = function (e) {
                imgPreview.src = e.target.result;
                previewContainer.style.display = 'block';
            };

            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
            imgPreview.src = '';
        }
    });

    // Optional: Auto-format ID input (e.g., force uppercase 'SCP-')
    const idInput = document.querySelector('input[name="id"]');
    idInput.addEventListener('blur', function () {
        let val = this.value.toUpperCase();
        if (!val.startsWith('SCP-') && val.length > 0) {
            // If user typed '173', convert to 'SCP-173'
            if (!isNaN(val)) {
                this.value = 'SCP-' + val;
            } else {
                this.value = val;
            }
        } else {
            this.value = val;
        }
    });
});