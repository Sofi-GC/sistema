<?php
session_start();
include 'conexion.php';

$grupo = $_GET['grupo'] ?? '';
$grupos_validos = ['6502', '6503', '6504', '6505'];
if (!in_array($grupo, $grupos_validos)) {
    die('Grupo inválido');
}

$fecha_hoy = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_asistencia'])) {
    $estados = $_POST['estado'] ?? [];
    foreach ($estados as $matricula => $estado) {
        $estado = in_array($estado, ['asistio','no asistio','retardo']) ? $estado : 'no asistio';
        $stmt = $conn->prepare("INSERT INTO asistencia (matricula, grupo, fecha, estado) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE estado = VALUES(estado)");
        $stmt->bind_param("ssss", $matricula, $grupo, $fecha_hoy, $estado);
        $stmt->execute();
    }
    $msg = "Asistencia guardada correctamente para la fecha $fecha_hoy.";
}

$stmt = $conn->prepare("SELECT * FROM alumnos WHERE grupo = ? ORDER BY apellido ASC, nombre ASC");
$stmt->bind_param("s", $grupo);
$stmt->execute();
$res = $stmt->get_result();
$alumnos = $res->fetch_all(MYSQLI_ASSOC);

$stmt2 = $conn->prepare("SELECT matricula, estado FROM asistencia WHERE grupo = ? AND fecha = ?");
$stmt2->bind_param("ss", $grupo, $fecha_hoy);
$stmt2->execute();
$res2 = $stmt2->get_result();
$asistencias = [];
while($row = $res2->fetch_assoc()) {
    $asistencias[$row['matricula']] = $row['estado'];
}

if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=lista_asistencia_{$grupo}_{$fecha_hoy}.xls");
    echo "<table border='1'>";
    echo "<tr><th>#</th><th>Matrícula</th><th>Nombre</th><th>Apellido</th><th>Asistencia</th></tr>";
    $i = 1;
    foreach ($alumnos as $alumno) {
        $estado = $asistencias[$alumno['matricula']] ?? 'No registrado';
        echo "<tr>";
        echo "<td>" . $i++ . "</td>";
        echo "<td>" . htmlspecialchars($alumno['matricula']) . "</td>";
        echo "<td>" . htmlspecialchars($alumno['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($alumno['apellido']) . "</td>";
        echo "<td>" . htmlspecialchars($estado) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Lista de Asistencia - Grupo <?= htmlspecialchars($grupo) ?></title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');
  body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f0ede7, #d7c9a7);
    padding: 30px;
    color: #402F4E;
    margin: 0;
  }
  h1 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.2rem;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  }
  th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
  }
  th {
    background-color: #D7AC71;
    color: #402F4E;
  }
  .btn, .btn-regresar {
    display: inline-block;
    margin: 15px 10px 25px 0;
    background-color: #402F4E;
    color: #fff;
    padding: 12px 25px;
    border: none;
    border-radius: 12px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
  }
  .btn:hover, .btn-regresar:hover {
    background-color: #D7AC71;
    color: #402F4E;
  }
  .radio-group label {
    font-size: 1.2rem;
  }
  footer {
    margin-top: 50px;
    text-align: center;
    font-size: 0.9rem;
    color: #5c4b3a;
  }
</style>
</head>
<body>

<a href="../html/grupo<?= htmlspecialchars($grupo) ?>.php" class="btn-regresar">← Regresar al grupo</a>

<h1>📋 Lista de asistencia - Grupo <?= htmlspecialchars($grupo) ?> <br> <small style="font-size:1.1rem;">Fecha: <?= $fecha_hoy ?></small></h1>

<?php if (isset($msg)) echo "<p style='color:green; font-weight:bold; text-align:center;'>$msg</p>"; ?>

<form method="POST">
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Matrícula</th>
      <th>Nombre</th>
      <th>Apellido</th>
      <th>✔ Asistió</th>
      <th>✘ No asistió</th>
      <th>⏰ Retardo</th>
    </tr>
  </thead>
  <tbody>
    <?php $i=1; foreach($alumnos as $alumno): 
      $mat = $alumno['matricula'];
      $estado_actual = $asistencias[$mat] ?? '';
    ?>
    <tr>
      <td><?= $i++ ?></td>
      <td><?= htmlspecialchars($mat) ?></td>
      <td><?= htmlspecialchars($alumno['nombre']) ?></td>
      <td><?= htmlspecialchars($alumno['apellido']) ?></td>
      <td><input type="radio" name="estado[<?= $mat ?>]" value="asistio" <?= $estado_actual === 'asistio' ? 'checked' : '' ?> /></td>
      <td><input type="radio" name="estado[<?= $mat ?>]" value="no asistio" <?= $estado_actual === 'no asistio' ? 'checked' : '' ?> /></td>
      <td><input type="radio" name="estado[<?= $mat ?>]" value="retardo" <?= $estado_actual === 'retardo' ? 'checked' : '' ?> /></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div style="text-align:center;">
  <button type="submit" name="guardar_asistencia" class="btn">💾 Guardar asistencia</button>
  <a href="ver_lista.php?grupo=<?= htmlspecialchars($grupo) ?>&export=excel" class="btn" style="background:#D7AC71; color:#402F4E;">📥 Descargar Excel</a>
</div>
</form>

<footer>
  <hr style="width:60%; border:none; border-top:1px solid #D7AC71; margin-bottom:10px;">
  Hecho con ❤️ para el grupo <?= htmlspecialchars($grupo) ?> — <?= date('Y') ?>
</footer>

</body>
</html>
