document.addEventListener('DOMContentLoaded', function () {

    // --- Lógica de Imagen (Se mantiene igual) ---
    const imgInput = document.getElementById('imgInput');
    const previewContainer = document.getElementById('previewContainer');
    const imgPreview = document.getElementById('imgPreview');

    if (imgInput) {
        imgInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('ERROR: Only image files are allowed.');
                    imgInput.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }
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
    }

    // --- NUEVA Lógica de validación ID ---
    const idInput = document.querySelector('input[name="id"]');

    if (idInput) {
        // Al escribir, forzamos mayúsculas
        idInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });

        // Al salir del campo (blur), validamos
        idInput.addEventListener('blur', function () {
            let val = this.value.trim();

            // Si el usuario escribió solo numeros "173", ayudamos poniendo "SCP-173"
            if (!val.startsWith('SCP-') && val.length > 0) {
                // Si no tiene el prefijo, verificamos si es solo texto/números y lo agregamos
                if (!val.includes('SCP')) {
                    this.value = 'SCP-' + val;
                }
            }

            // Validación final visual
            if (!this.value.startsWith('SCP-')) {
                this.classList.add('is-invalid'); // Clase error de Bootstrap
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    }

    // Prevenir envio si no es valido
    const form = document.getElementById('createForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!idInput.value.startsWith('SCP-')) {
                e.preventDefault();
                alert("PROTOCOL VIOLATION: The ID must start with 'SCP-'.");
                idInput.focus();
                idInput.classList.add('is-invalid');
            }
        });
    }
});