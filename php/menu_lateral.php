<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../html/iniciar_sesion.html');
    exit;
}

$modo_tema = $_SESSION['usuario']['modo_tema'] ?? 0;
$color_fondo = $modo_tema ? '#222' : '#402F4E';
$color_texto = $modo_tema ? '#ddd' : '#D7AC71';
?>

<style>
  /* Estilos menú lateral fijo */
  #menu-lateral {
    position: fixed;
    top: 0;
    left: 0;
    height: 60px;
    width: 100%;
    background-color: <?= $color_fondo ?>;
    color: <?= $color_texto ?>;
    display: flex;
    align-items: center;
    padding: 0 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    z-index: 1000;
    font-family: 'Open Sans', sans-serif;
  }
  #menu-lateral nav {
    display: flex;
    gap: 25px;
  }
  #menu-lateral nav a {
    color: <?= $color_texto ?>;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    padding: 8px 12px;
    border-radius: 8px;
    transition: background-color 0.3s ease;
  }
  #menu-lateral nav a:hover {
    background-color: <?= $modo_tema ? '#444' : '#D7AC71' ?>;
    color: <?= $modo_tema ? '#fff' : '#402F4E' ?>;
  }
  #menu-lateral #usuario-nombre {
    margin-left: auto;
    font-weight: 700;
    font-size: 1rem;
  }
</style>

<div id="menu-lateral">
  <nav>
    <a href="../php/dashboard.php">Inicio</a>
    <a href="../php/perfil.php">Perfil</a>
    <a href="../php/ajustes.php">Ajustes</a>
    <a href="../php/logout.php" onclick="return confirm('¿Cerrar sesión?')">Cerrar Sesión</a>
  </nav>
  <div id="usuario-nombre">
    <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>
  </div>
</div>
