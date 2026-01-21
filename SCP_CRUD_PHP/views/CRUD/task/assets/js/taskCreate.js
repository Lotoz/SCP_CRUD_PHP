/**
 * views/CRUD/assets/js/taskCreate.js
 */
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("createTaskForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    // 1. Obtener elementos
    const descInput = document.getElementById("description");
    const dateInput = document.getElementById("due_date").value;

    // 2. Obtener contenedores de error
    const errorDesc = document.getElementById("errorDesc");
    const errorDate = document.getElementById("errorDate");

    // 3. Función para limpiar errores (DRY)
    function clearError(el) {
      if (el) {
        el.innerHTML = "";
        el.hidden = true;
        el.classList.remove("text-danger", "small", "mt-1");
      }
    }

    function showError(el, msg) {
      if (el) {
        el.hidden = false;
        el.classList.add("text-danger", "small", "mt-1");
        el.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>ERROR:</strong> ${msg}`;
      }
    }

    // Reset inicial
    clearError(errorDesc);
    clearError(errorDate);

    let isValid = true;

    // --- VALIDACIÓN 1: Descripción requerida ---
    if (descInput.value.trim() === "") {
      showError(errorDesc, "Description cannot be empty.");
      isValid = false;
    }

    // --- VALIDACIÓN 2: Fecha ---
    if (dateInput && dateInput.value !== "") {
      let today = new Date.now();

      if (dateInput.getYear() < today.getYear()) {
        showError(errorDate, "The target date cannot be in the past.");
        isValid = false;
      }
      if (dateInput.getMonth() < today.getMonth()) {
        showError(errorDate, "The target date cannot be in the past.");
        isValid = false;
      }

      if (dateInput.getDay() < today.getDay()) {
        showError(errorDate, "The target date cannot be in the past.");
        isValid = false;
      }
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
});
