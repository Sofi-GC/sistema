<!-- mostrar_usuarios.php -->
<?php
include 'conexion.php';

$sql = "SELECT id_usuario, nombre, apellido, correo FROM usuarios";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Usuarios</title>
</head>
<body>
    <h1>Lista de Usuarios</h1>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Apellido</th><th>Correo</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>". htmlspecialchars($row['id_usuario']) ."</td>";
                echo "<td>". htmlspecialchars($row['nombre']) ."</td>";
                echo "<td>". htmlspecialchars($row['apellido']) ."</td>";
                echo "<td>". htmlspecialchars($row['correo']) ."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hay usuarios registrados</td></tr>";
        }
        ?>
        </tbody>
    </table>

</body>
</html>

<?php
$conn->close();
?>
