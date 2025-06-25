<?php
session_start();
include '../php/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../html/iniciar_sesion.html');
    exit;
}

$usuario = $_SESSION['usuario'];
$mensaje = '';

// Procesar formulario POST para actualizar datos y foto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $carrera = $_POST['carrera'];

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Correo no válido.";
    } else {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg','jpeg','png','gif'];
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $mensaje = "Formato de foto no permitido.";
            } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                $mensaje = "La foto debe ser menor a 2MB.";
            } else {
                $ruta_destino = '../uploads/';
                if (!is_dir($ruta_destino)) {
                    mkdir($ruta_destino, 0755, true);
                }

                $nombre_foto = 'user_'. $usuario['id'] . '_' . time() . '.' . $ext;
                $ruta_final = $ruta_destino . $nombre_foto;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_final)) {
                    $foto_bd = 'uploads/' . $nombre_foto;

                    // Opcional: borrar foto vieja para no acumular archivos
                    if (!empty($usuario['foto']) && file_exists('../' . $usuario['foto'])) {
                        @unlink('../' . $usuario['foto']);
                    }
                } else {
                    $mensaje = "Error al subir la foto.";
                }
            }
        }

        if (!$mensaje) {
            if (isset($foto_bd)) {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, apellido=?, correo=?, carrera=?, foto=? WHERE id_usuario=?");
                $stmt->bind_param('sssssi', $nombre, $apellido, $correo, $carrera, $foto_bd, $usuario['id']);
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, apellido=?, correo=?, carrera=? WHERE id_usuario=?");
                $stmt->bind_param('ssssi', $nombre, $apellido, $correo, $carrera, $usuario['id']);
            }

            if ($stmt->execute()) {
                $_SESSION['usuario']['nombre'] = $nombre;
                $_SESSION['usuario']['apellido'] = $apellido;
                $_SESSION['usuario']['correo'] = $correo;
                $_SESSION['usuario']['carrera'] = $carrera;
                if (isset($foto_bd)) {
                    $_SESSION['usuario']['foto'] = $foto_bd;
                    $usuario['foto'] = $foto_bd; // actualizar variable para mostrar nueva foto
                }
                $mensaje = "Perfil actualizado correctamente.";
            } else {
                $mensaje = "Error al actualizar perfil.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Perfil de Usuario</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

  /* Reset & base */
  * {
    box-sizing: border-box;
  }

  body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    background: <?= ($_SESSION['usuario']['modo_tema']) ? '#121212' : '#f5f5f5'; ?>;
    color: <?= ($_SESSION['usuario']['modo_tema']) ? '#ddd' : '#222'; ?>;
    transition: background 0.3s, color 0.3s;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 60px 15px 30px;
  }

  .perfil-container {
    background: <?= ($_SESSION['usuario']['modo_tema']) ? '#1e1e1e' : '#fff'; ?>;
    border-radius: 25px;
    box-shadow: 0 12px 28px rgba(0,0,0,0.15);
    max-width: 480px;
    width: 100%;
    padding: 40px 35px 50px;
    text-align: center;
  }

  h2 {
    color: #D7AC71;
    font-weight: 800;
    font-size: 2.3rem;
    margin-bottom: 30px;
    letter-spacing: 1.2px;
  }

  .foto-perfil {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #D7AC71;
    box-shadow: 0 0 20px rgba(215,172,113,0.8);
    margin: 0 auto 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .foto-perfil:hover {
    transform: scale(1.1);
    box-shadow: 0 0 35px rgba(215,172,113,1);
  }

  form label {
    display: block;
    font-weight: 700;
    font-size: 1.15rem;
    margin: 20px 0 8px;
    text-align: left;
  }
  form input[type="text"],
  form input[type="email"],
  form select,
  form input[type="file"] {
    width: 100%;
    padding: 12px 15px;
    font-size: 1rem;
    border-radius: 15px;
    border: 1.8px solid #ccc;
    transition: border-color 0.3s ease;
  }
  form input[type="text"]:focus,
  form input[type="email"]:focus,
  form select:focus,
  form input[type="file"]:focus {
    outline: none;
    border-color: #d7ac71;
    box-shadow: 0 0 12px 3px rgba(215,172,113,0.6);
  }

  form input[type="file"] {
    cursor: pointer;
  }

  button[type="submit"] {
    margin-top: 35px;
    width: 100%;
    background: linear-gradient(45deg, #402f4e, #d7ac71);
    border: none;
    padding: 16px 0;
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
    border-radius: 30px;
    cursor: pointer;
    box-shadow: 0 12px 30px rgba(215,172,113,0.75);
    transition: background 0.35s ease, box-shadow 0.35s ease;
    text-transform: uppercase;
    letter-spacing: 1.2px;
  }
  button[type="submit"]:hover,
  button[type="submit"]:focus {
    background: linear-gradient(45deg, #f0d18c, #402f4e);
    outline: none;
    box-shadow: 0 16px 45px rgba(215,172,113,0.95);
  }

  .mensaje {
    margin-top: 25px;
    font-weight: 700;
    font-size: 1.1rem;
    color: #4caf50;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
  }

  /* Sidebar */
  #toggleSidebar {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1100;
    background: linear-gradient(45deg, #d7ac71, #f0d18c);
    color: #402F4E;
    border: none;
    padding: 14px 18px;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1.3rem;
    cursor: pointer;
    box-shadow: 0 6px 12px rgba(215,172,113,0.7);
    user-select: none;
    filter: drop-shadow(0 3px 2px rgba(215,172,113,0.5));
    transition: background 0.3s ease;
  }
  #toggleSidebar:hover, #toggleSidebar:focus {
    background: linear-gradient(45deg, #f0d18c, #d7ac71);
    outline: none;
    filter: drop-shadow(0 4px 3px rgba(215,172,113,0.7));
  }

  nav.sidebar {
    width: 270px;
    background-color: #402F4E;
    color: #D7AC71;
    padding: 30px 25px;
    height: 100vh;
    overflow-y: auto;
    position: fixed;
    top: 0;
    left: 0;
    box-shadow: 4px 0 12px rgba(0, 0, 0, 0.4);
    user-select: none;
    border-top-right-radius: 20px;
    border-bottom-right-radius: 20px;
    transform: translateX(0);
    transition: transform 0.3s ease;
    z-index: 1050;
  }
  nav.sidebar.hidden {
    transform: translateX(-300px);
  }

  nav.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  nav.sidebar ul li {
    margin-bottom: 22px;
  }
  nav.sidebar ul li a {
    color: #D7AC71;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 14px 18px;
    border-radius: 14px;
    display: block;
    text-decoration: none;
    box-shadow: inset 0 0 0 0 transparent;
    transition: background-color 0.25s ease, color 0.25s ease;
  }
  nav.sidebar ul li a:hover, nav.sidebar ul li a:focus {
    background-color: #D7AC71;
    color: #402F4E;
    outline: none;
    box-shadow: inset 0 0 18px 5px rgba(215,172,113,0.7);
  }

  nav.sidebar form button {
    width: 100%;
    margin-top: 12px;
    padding: 14px 0;
    border-radius: 18px;
    background-color: #aa4a44;
    color: #fff;
    border: none;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(170, 74, 68, 0.85);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    user-select: none;
  }
  nav.sidebar form button:hover, nav.sidebar form button:focus {
    background-color: #d14f48;
    outline: none;
    box-shadow: 0 6px 10px rgba(209, 79, 72, 0.95);
  }

  /* Responsive */
  @media (max-width: 700px) {
    nav.sidebar {
      transform: translateX(-300px);
      border-radius: 0;
      position: fixed;
      height: 100%;
      z-index: 1100;
    }
    nav.sidebar.visible {
      transform: translateX(0);
    }
    body {
      padding: 70px 15px 15px;
    }
    .perfil-container {
      margin: auto;
      max-width: 100%;
      padding: 30px 20px 40px;
      border-radius: 15px;
    }
  }
</style>
</head>
<body>

<button id="toggleSidebar" aria-label="Mostrar u ocultar menú lateral" title="Mostrar u ocultar menú lateral">☰ Menú</button>

<nav class="sidebar" id="sidebar" aria-label="Menú principal de navegación">
  <ul>
    <li><a href="../html/dashboard.php" tabindex="0">Inicio</a></li>
    <li><a href="../html/ajustes.php" tabindex="0">Ajustes</a></li>
    <li><a href="perfil.php" tabindex="0" aria-current="page">Perfil</a></li>
    <li>
      <form action="../php/logout.php" method="POST" onsubmit="return confirm('¿Seguro que deseas cerrar sesión?');">
        <button type="submit" tabindex="0">Cerrar sesión</button>
      </form>
    </li>
  </ul>
</nav>

<div class="perfil-container" role="main">
  <h2>Perfil de Usuario</h2>

  <img
    src="../<?= htmlspecialchars($usuario['foto'] ?: 'assets/default-avatar.png'); ?>"
    alt="Foto de perfil de <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>"
    class="foto-perfil"
    loading="lazy"
  />

  <?php if ($mensaje): ?>
    <p class="mensaje" role="alert"><?= htmlspecialchars($mensaje); ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" novalidate>
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']); ?>" required autocomplete="given-name" />

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']); ?>" required autocomplete="family-name" />

    <label for="correo">Correo electrónico:</label>
    <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']); ?>" required autocomplete="email" />

    <label for="carrera">Carrera:</label>
    <select id="carrera" name="carrera" required>
      <option value="Sistemas" <?= ($usuario['carrera'] === 'Sistemas') ? 'selected' : ''; ?>>Sistemas</option>
      <option value="Contador Público" <?= ($usuario['carrera'] === 'Contador Público') ? 'selected' : ''; ?>>Contador Público</option>
    </select>

    <label for="foto">Cambiar foto de perfil (Opcional):</label>
    <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.gif" />

    <button type="submit">Actualizar Perfil</button>
  </form>
</div>

<script>
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');

  toggleBtn.addEventListener('click', () => {
    if(window.innerWidth <= 700){
      sidebar.classList.toggle('visible');
    } else {
      sidebar.classList.toggle('hidden');
    }
  });

  // Cerrar menú al hacer click fuera en móviles
  document.addEventListener('click', e => {
    if(window.innerWidth <= 700 && !sidebar.contains(e.target) && e.target !== toggleBtn){
      sidebar.classList.remove('visible');
    }
  });
</script>

</body>
</html>
