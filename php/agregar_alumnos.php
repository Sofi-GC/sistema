<?php
session_start();
include 'conexion.php';

$grupo = $_GET['grupo'] ?? '';
$grupos_validos = ['6502', '6503', '6504', '6505'];
if (!in_array($grupo, $grupos_validos)) {
    die('Grupo inválido');
}

$admin_pass = 'admin123'; // 🔐 CAMBIA AQUÍ LA CONTRASEÑA DEL ADMIN

// Validar si el formulario fue desbloqueado
$acceso = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clave_admin'])) {
    if ($_POST['clave_admin'] === $admin_pass) {
        $_SESSION['admin_acceso'] = true;
        $acceso = true;
    } else {
        $error = "Contraseña incorrecta.";
    }
} elseif (isset($_SESSION['admin_acceso']) && $_SESSION['admin_acceso'] === true) {
    $acceso = true;
}

// Agregar alumno
if ($acceso && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $matricula = trim($_POST['matricula']);
    $carrera = ($grupo === '6502' || $grupo === '6503') ? 'Sistemas' : 'Contador Público';

    if ($nombre && $apellido && $matricula) {
        $stmt = $conn->prepare("INSERT INTO alumnos (nombre, apellido, matricula, grupo, carrera) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $apellido, $matricula, $grupo, $carrera);
        if ($stmt->execute()) {
            $msg = "Alumno agregado con éxito.";
        } else {
            $msg = "Error: puede que la matrícula ya exista.";
        }
    } else {
        $msg = "Todos los campos son obligatorios.";
    }
}

// Borrar alumno
if ($acceso && isset($_POST['accion']) && $_POST['accion'] === 'borrar' && isset($_POST['matricula_borrar'])) {
    $mat_borrar = $_POST['matricula_borrar'];
    $stmt = $conn->prepare("DELETE FROM alumnos WHERE matricula = ? AND grupo = ?");
    $stmt->bind_param("ss", $mat_borrar, $grupo);
    $stmt->execute();
    $msg = "Alumno eliminado.";
}

// Obtener alumnos del grupo
$alumnos = [];
if ($acceso) {
    $stmt = $conn->prepare("SELECT * FROM alumnos WHERE grupo = ?");
    $stmt->bind_param("s", $grupo);
    $stmt->execute();
    $res = $stmt->get_result();
    $alumnos = $res->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Alumnos - Grupo <?= htmlspecialchars($grupo) ?></title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0ede7, #d7c9a7);
      padding: 30px;
      color: #402F4E;
      min-height: 100vh;
      margin: 0;
      user-select: none;
    }

    a.btn-regresar {
      display: inline-block;
      background-color: #D7AC71;
      color: #402F4E;
      padding: 12px 28px;
      border-radius: 20px;
      font-weight: 700;
      text-decoration: none;
      box-shadow: 0 6px 16px rgba(215, 172, 113, 0.7);
      margin-bottom: 30px;
      transition: background-color 0.3s ease, transform 0.2s ease;
      user-select: none;
    }
    a.btn-regresar:hover,
    a.btn-regresar:focus {
      background-color: #b59a5e;
      transform: scale(1.05);
      outline: none;
      box-shadow: 0 8px 22px rgba(215, 172, 113, 0.9);
    }

    h1 {
      font-weight: 800;
      font-size: 2.8rem;
      text-align: center;
      margin-bottom: 40px;
      text-shadow: 1px 1px 3px rgba(117, 89, 62, 0.7);
      user-select: text;
    }

    form {
      max-width: 500px;
      margin: 20px auto 40px auto;
      background: #fff;
      padding: 30px 35px;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(64, 47, 78, 0.12);
      user-select: text;
      transition: box-shadow 0.3s ease;
    }
    form:hover {
      box-shadow: 0 12px 40px rgba(64, 47, 78, 0.25);
    }

    form h3 {
      font-weight: 700;
      font-size: 1.6rem;
      color: #402F4E;
      margin-bottom: 25px;
      border-bottom: 2px solid #D7AC71;
      padding-bottom: 10px;
      user-select: text;
    }

    input[type="text"], input[type="password"] {
      width: calc(100% - 24px);
      padding: 14px 12px;
      margin: 10px 0 20px 0;
      border-radius: 15px;
      border: 2px solid #ccc;
      font-size: 1.1rem;
      color: #402F4E;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      user-select: text;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #d7ac71;
      box-shadow: 0 0 12px 3px rgba(215,172,113,0.6);
      outline: none;
    }

    button {
      background: linear-gradient(45deg, #402F4E, #D7AC71);
      color: #fff;
      border: none;
      border-radius: 20px;
      padding: 14px 35px;
      font-weight: 700;
      font-size: 1.2rem;
      cursor: pointer;
      box-shadow: 0 10px 25px rgba(215, 172, 113, 0.8);
      transition: background 0.35s ease, box-shadow 0.35s ease, transform 0.15s ease;
      user-select: none;
    }
    button:hover, button:focus {
      background: linear-gradient(45deg, #f0d18c, #402f4e);
      outline: none;
      box-shadow: 0 14px 40px rgba(215, 172, 113, 0.95);
      transform: scale(1.05);
    }

    /* Botón eliminar en rojo con efecto */
    form[style] button {
      background-color: #b93232 !important;
      box-shadow: 0 8px 22px rgba(185, 50, 50, 0.8) !important;
      transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.15s ease !important;
    }
    form[style] button:hover, form[style] button:focus {
      background-color: #e85050 !important;
      box-shadow: 0 12px 35px rgba(232, 80, 80, 0.95) !important;
      transform: scale(1.07) !important;
    }

    p {
      max-width: 500px;
      margin: 15px auto;
      padding: 15px 20px;
      background: #fef8e9;
      border: 1.5px solid #d7ac71;
      border-radius: 15px;
      font-weight: 700;
      color: #5b4d26;
      box-shadow: 0 4px 15px rgba(215,172,113,0.3);
      user-select: text;
      text-align: center;
    }

    table {
      width: 90%;
      margin: 30px auto 50px auto;
      border-collapse: separate;
      border-spacing: 0 10px;
      box-shadow: 0 8px 40px rgba(64, 47, 78, 0.1);
      border-radius: 20px;
      overflow: hidden;
      background: #fff;
      user-select: text;
    }
    th, td {
      text-align: center;
      padding: 14px 18px;
      font-size: 1.1rem;
    }
    th {
      background-color: #D7AC71;
      color: #402F4E;
      font-weight: 700;
      letter-spacing: 1.1px;
    }
    tbody tr {
      background-color: #fff;
      transition: background-color 0.3s ease;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(64, 47, 78, 0.05);
    }
    tbody tr:hover {
      background-color: #f9f4e9;
    }
    tbody tr td {
      border-bottom: 1px solid #eee;
      user-select: text;
    }

    /* Responsive */
    @media (max-width: 600px) {
      body {
        padding: 20px 15px;
      }
      form, table {
        width: 100%;
        margin: 20px 0;
      }
      input, button {
        width: 100% !important;
        box-sizing: border-box;
      }
      a.btn-regresar {
        padding: 10px 20px;
        font-size: 1rem;
      }
      h1 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>

  <a href="../html/grupo<?= htmlspecialchars($grupo) ?>.php" class="btn-regresar" aria-label="Regresar al grupo <?= htmlspecialchars($grupo) ?>">← Regresar</a>

  <h1>Modificar lista de alumnos - Grupo <?= htmlspecialchars($grupo) ?></h1>

  <?php if (!$acceso): ?>
    <form method="POST" aria-label="Formulario de acceso administrador">
      <label for="clave_admin" style="font-weight:700; font-size:1.1rem;">Introduce la contraseña de administrador:</label><br>
      <input type="password" id="clave_admin" name="clave_admin" required autocomplete="off" aria-required="true" aria-describedby="error_admin" />
      <button type="submit">Acceder</button>
      <?php if (isset($error)) echo "<p id='error_admin' style='color:#b93232; margin-top:12px; font-weight:700;'>$error</p>"; ?>
    </form>
  <?php else: ?>
    <form method="POST" aria-label="Formulario para agregar nuevo alumno">
      <h3>Agregar nuevo alumno</h3>
      <input type="hidden" name="accion" value="agregar" />
      <input type="text" name="nombre" placeholder="Nombre" required autocomplete="off" />
      <input type="text" name="apellido" placeholder="Apellido" required autocomplete="off" />
      <input type="text" name="matricula" placeholder="Matrícula" required autocomplete="off" />
      <button type="submit">Agregar</button>
    </form>

    <form method="POST" style="margin-top:-10px;" aria-label="Formulario para eliminar alumno">
      <input type="hidden" name="accion" value="borrar" />
      <input type="text" name="matricula_borrar" placeholder="Matrícula a borrar" required autocomplete="off" />
      <button type="submit" style="background:#b93232;">Eliminar</button>
    </form>

    <?php if (isset($msg)) echo "<p><strong>$msg</strong></p>"; ?>

    <h3 style="text-align:center; font-weight:700; margin-top: 40px; user-select: text;">Lista de alumnos del grupo <?= htmlspecialchars($grupo) ?></h3>
    <table role="grid" aria-label="Lista de alumnos">
      <thead>
        <tr>
          <th>Matrícula</th>
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Carrera</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($alumnos as $alumno): ?>
        <tr>
          <td><?= htmlspecialchars($alumno['matricula']) ?></td>
          <td><?= htmlspecialchars($alumno['nombre']) ?></td>
          <td><?= htmlspecialchars($alumno['apellido']) ?></td>
          <td><?= htmlspecialchars($alumno['carrera']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</body>
</html>
