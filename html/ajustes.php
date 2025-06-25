<?php
session_start();
include '../php/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../html/iniciar_sesion.html');
    exit;
}

$usuario = $_SESSION['usuario'];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modo_tema = isset($_POST['modo_tema']) ? 1 : 0;
    $notificaciones = isset($_POST['notificaciones']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE usuarios SET modo_tema=?, notificaciones=? WHERE id_usuario=?");
    $stmt->bind_param('iii', $modo_tema, $notificaciones, $usuario['id']);

    if ($stmt->execute()) {
        $_SESSION['usuario']['modo_tema'] = $modo_tema;
        $_SESSION['usuario']['notificaciones'] = $notificaciones;
        $mensaje = "Ajustes guardados correctamente.";
        $usuario['modo_tema'] = $modo_tema;
        $usuario['notificaciones'] = $notificaciones;
    } else {
        $mensaje = "Error al guardar ajustes.";
    }

    $stmt->close();
}

$modoTema = $usuario['modo_tema'] ?? 0;
$bgBody = $modoTema ? '#121212' : '#f5f5f5';
$colorBody = $modoTema ? '#e0e0e0' : '#222';
$sidebarBg = '#402F4E';
$sidebarColor = '#D7AC71';
$containerBg = $modoTema ? '#1e1e1e' : '#fff';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ajustes</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: <?= $bgBody ?>;
      color: <?= $colorBody ?>;
      margin: 0;
      padding: 0;
      display: flex;
      min-height: 100vh;
    }

    .wrapper {
      display: flex;
      flex-direction: row;
      width: 100%;
    }

    nav.sidebar {
      width: 250px;
      background-color: <?= $sidebarBg ?>;
      color: <?= $sidebarColor ?>;
      padding: 30px 20px;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      box-shadow: 4px 0 12px rgba(0, 0, 0, 0.3);
    }

    nav.sidebar ul {
      list-style: none;
      padding: 0;
    }

    nav.sidebar ul li {
      margin: 20px 0;
    }

    nav.sidebar ul li a,
    nav.sidebar form button {
      color: <?= $sidebarColor ?>;
      text-decoration: none;
      font-weight: bold;
      display: block;
      padding: 10px;
      border-radius: 8px;
      background: none;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
    }

    nav.sidebar ul li a:hover,
    nav.sidebar form button:hover {
      background-color: <?= $sidebarColor ?>;
      color: <?= $sidebarBg ?>;
    }

    main.container {
      margin-left: 270px;
      padding: 40px;
      background-color: <?= $containerBg ?>;
      border-radius: 20px;
      margin-top: 60px;
      max-width: 600px;
      width: 90%;
      margin-right: auto;
      margin-left: auto;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: <?= $sidebarColor ?>;
    }

    form label {
      display: block;
      margin: 20px 0;
      font-weight: bold;
    }

    form input[type="checkbox"] {
      margin-right: 10px;
    }

    .mensaje {
      text-align: center;
      color: green;
      font-weight: bold;
    }

    button[type="submit"] {
      display: block;
      margin: 30px auto 0;
      background-color: <?= $sidebarColor ?>;
      color: <?= $sidebarBg ?>;
      border: none;
      padding: 12px 30px;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
    }

    a.back-btn {
      display: inline-block;
      margin-bottom: 30px;
      text-decoration: none;
      background-color: <?= $sidebarColor ?>;
      color: <?= $sidebarBg ?>;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <nav class="sidebar">
      <ul>
        <li><a href="dashboard.php">🏠 Inicio</a></li>
        <li><a href="ajustes.php">⚙️ Ajustes</a></li>
        <li><a href="../php/perfil.php">👤 Perfil</a></li>
        <li>
          <form action="../php/logout.php" method="POST" onsubmit="return confirm('¿Cerrar sesión?');">
            <button type="submit">🚪 Cerrar sesión</button>
          </form>
        </li>
      </ul>
    </nav>

    <main class="container">
      <a class="back-btn" href="dashboard.php">← Regresar</a>

      <h2>⚙️ Ajustes de Usuario</h2>

      <?php if ($mensaje): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje); ?></p>
      <?php endif; ?>

      <form method="POST">
        <label>
          <input type="checkbox" name="modo_tema" <?= $usuario['modo_tema'] ? 'checked' : ''; ?> />
          Activar modo oscuro
        </label>

        <label>
          <input type="checkbox" name="notificaciones" <?= $usuario['notificaciones'] ? 'checked' : ''; ?> />
          Activar notificaciones
        </label>

        <button type="submit">💾 Guardar cambios</button>
      </form>
    </main>
  </div>
</body>

</html>
