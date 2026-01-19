/**
 * views/CRUD/assets/js/taskCreate.js
 */
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('createTaskForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {

        // 1. Obtener elementos
        const descInput = document.getElementById('description');
        const dateInput = document.getElementById('due_date'); 

        // 2. Obtener contenedores de error
        const errorDesc = document.getElementById('errorDesc');
        const errorDate = document.getElementById('errorDate'); 

        // 3. Función para limpiar errores (DRY)
        function clearError(el) {
            if (el) {
                el.innerHTML = "";
                el.hidden = true;
                el.classList.remove('text-danger', 'small', 'mt-1');
            }
        }

        function showError(el, msg) {
            if (el) {
                el.hidden = false;
                el.classList.add('text-danger', 'small', 'mt-1');
                el.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>ERROR:</strong> ${msg}`;
            }
        }

        // Reset inicial
        clearError(errorDesc);
        clearError(errorDate);

        let isValid = true;

        // --- VALIDACIÓN 1: Descripción requerida ---
        if (descInput.value.trim() === '') {
            showError(errorDesc, 'Description cannot be empty.');
            isValid = false;
        }

        // --- VALIDACIÓN 2: Fecha NO VA, revisar luego ---
        if (dateInput && dateInput.value !== '') {
            const inputDate = new Date(dateInput.value);

            // Obtenemos la fecha de hoy y le quitamos la hora (00:00:00) para comparar solo días
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Ajuste de zona horaria: A veces el input date toma UTC y today toma local.
            // Una forma segura de comparar YYYY-MM-DD es tratarlo como string o ajustar el offset.
            // Esta comparación simple suele funcionar bien para validación básica:
            // Si la fecha ingresada + 1 día (para compensar UTC) es menor a hoy.

            // Solución robusta: comparar strings ISO (YYYY-MM-DD)
            const todayString = new Date().toISOString().split('T')[0];
            const inputString = inputDate.value;

            if (inputString < todayString) {
                showError(errorDate, 'The target date cannot be in the past.');
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});