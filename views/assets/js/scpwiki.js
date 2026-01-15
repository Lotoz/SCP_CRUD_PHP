document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const cards = document.querySelectorAll('.scp-card');

        cards.forEach(card => {
            // Buscamos texto dentro de la tarjeta (ID, Apodo, Descripción)
            const text = card.innerText.toLowerCase();
            // Buscamos el contenedor columna padre para ocultarlo completo
            const column = card.closest('.col-md-6'); // Ajusta según tu grid (col-md-6 col-lg-4)

            if (text.includes(filter)) {
                column.style.display = '';
            } else {
                column.style.display = 'none';
            }
        });
    });
});