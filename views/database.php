<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control database</title>
</head>

<body>
    <!--El mismo header-->
    <h1>Que base de datos desea acceder?</h1>
    <select name="datasbases" id="datasbases">
        <option value="users">Users</option>
        <option value="anomalies">Anomalies</option>
        <option value="sites">Sites</option>
        <option value="personal_asignado">Personal Asignado</option>
        <option value="ex-empleados">Ex-empleados</option>
    </select>
    <!--Se debe generar una tabla segun la selecionada, refrescando la pagina automaticamente.
Esta tabla debera tener las siguientes opciones:
1.Crear
2.Eliminar
3.Editar
4.Solo ver
En el caso de 'eliminar una anomalia', es cambiar su id por SCP-XXX-EX y poner en su estado Neutralizada, es decir, se sigue guardando en la base de datos
En el caso de 'eliminar un usuario', se guarda con el triggered de base de datos
Si se elimina un sitio, se debera de dar una advertencia de que las anomalias deben ser reasignadas, y deberan reasignarlas a su nuevos sitios-->
</body>

</html>