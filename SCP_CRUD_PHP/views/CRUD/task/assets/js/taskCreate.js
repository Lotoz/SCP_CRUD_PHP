document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("createTaskForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;

    // 1. Obtener elementos
    const descInput = document.getElementById("description");
    const dateValue = document.getElementById("due_date").value; // Esto es un String "YYYY-MM-DD"

    // 2. Obtener contenedores de error (Debes agregarlos al HTML, ver paso 2 abajo)
    const errorDesc = document.getElementById("errorDesc");
    const errorDate = document.getElementById("errorDate");

    // 3. Funciones de utilidad
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

    // --- VALIDACIÓN 1: Descripción requerida ---
    if (!descInput.value.trim()) {
      showError(errorDesc, "Description cannot be empty.");
      isValid = false;
    }

    // --- VALIDACIÓN 2: Fecha ---
    if (dateValue) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const inputDate = new Date(dateValue + "T00:00:00");

      if (inputDate < today) {
        showError(errorDate, "The target date cannot be in the past.");
        isValid = false;
      }
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
});
