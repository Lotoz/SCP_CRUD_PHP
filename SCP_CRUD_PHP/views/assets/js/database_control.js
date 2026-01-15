document.addEventListener('DOMContentLoaded', function () {

    // 1. REFERENCIAS AL DOM
    const selectDatabase = document.getElementById('datasbases');

    // 2. EVENTO: CAMBIAR DE TABLA (Select)
    if (selectDatabase) {
        selectDatabase.addEventListener('change', function () {
            const selectedTable = this.value;
            // Redirige pasando la variable 'table' por URL
            window.location.href = `index.php?action=database&table=${selectedTable}`;
        });
    }
});

/**
 * Función Maestra de Eliminación
 * Maneja las diferentes lógicas de seguridad según la entidad.
 * * @param {string} type - Tipo de entidad ('sites', 'anomalies', 'users', etc.)
 * @param {string} id - ID del registro (ej: 'SCP-173', 'Site-19', 'DrGears')
 */
function confirmAction(type, id) {
    let message = "";
    let subMessage = "";
    let actionUrl = "";

    switch (type) {
        case 'sites':
            message = `⚠ CRITICAL WARNING: DELETION OF [${id}]`;
            subMessage = "PROTOCOL REQUIREMENT:\nHave all contained anomalies been reassigned to a new Site?\n\nDeleting this site without reassignment will cause database inconsistencies.\n\nProceed?";
            actionUrl = `index.php?action=delete_site&id=${id}`;
            break;

        case 'anomalies':
            message = `CONFIRM NEUTRALIZATION: [${id}]`;
            subMessage = `This action will NOT delete the record.\nIt will reclassify the object as "${id}-EX" and set status to "Neutralized".\n\nAre you sure?`;
            actionUrl = `index.php?action=neutralize_scp&id=${id}`;
            break;

        case 'users':
            message = `TERMINATE PERSONNEL RECORD: [${id}]`;
            subMessage = "This user will be moved to the EX-EMPLOYEES archive automatically.\nAccess credentials will be revoked immediately.\n\nConfirm termination?";
            actionUrl = `index.php?action=delete_user&id=${id}`;
            break;

        case 'personal_asignado':
            message = `REVOKE ASSIGNMENT`;
            subMessage = `Remove personnel from [${id}]?\nThey will lose access to this anomaly's documentation.`;
            // Nota: Para borrar asignaciones compuestas, quizás necesites pasar 2 IDs (user y scp)
            // Aquí asumo que pasas un ID combinado o manejas la lógica diferente.
            actionUrl = `index.php?action=delete_assignment&id=${id}`;
            break;

        default:
            message = `CONFIRM DELETION`;
            subMessage = `Are you sure you want to delete entry [${id}]?`;
            actionUrl = `index.php?action=delete_generic&table=${type}&id=${id}`;
            break;
    }

    // Usamos el confirm nativo del navegador (Simple y robusto)
    // Para algo más estético, se requeriría un Modal de Bootstrap
    if (confirm(message + "\n\n" + subMessage)) {
        window.location.href = actionUrl;
    }
}

/**
 * Función para Editar (Redirección simple)
 */
function editRecord(table, id) {
    window.location.href = `index.php?action=edit&table=${table}&id=${id}`;
}