<?php
session_start();  // Aquí sí, para usar $_SESSION

include '../php/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../html/iniciar_sesion.html');
    exit;
}

$carrera = $_SESSION['usuario']['carrera'] ?? 'Sistemas';

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <style>
    /* Estilos simplificados para el ejemplo */
    body {
      font-family: 'Open Sans', sans-serif;
      margin: 0;
      background-color: <?= $_SESSION['usuario']['modo_tema'] ? '#222' : '#fff'; ?>;
      color: <?= $_SESSION['usuario']['modo_tema'] ? '#ddd' : '#000'; ?>;
      display: flex;
      flex-direction: row;
      min-height: 100vh;
    }

    nav.sidebar {
      width: 250px;
      background-color: #402F4E;
      color: #D7AC71;
      padding: 20px;
      overflow-y: auto;
      box-shadow: 3px 0 8px rgba(0,0,0,0.3);
      flex-shrink: 0;
      user-select: none;
    }

    nav.sidebar h2 {
      margin-top: 0;
      font-weight: 700;
      font-size: 1.8rem;
      margin-bottom: 25px;
      color: #F5E1A4;
      text-align: center;
      user-select: none;
      text-shadow: 0 0 6px rgba(215,172,113,0.7);
    }

    nav.sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    nav.sidebar ul li {
      margin-bottom: 15px;
    }

    nav.sidebar ul li a,
    nav.sidebar ul li .menu-link {
      color: #D7AC71;
      text-decoration: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      padding: 8px 12px;
      border-radius: 8px;
      transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
      gap: 10px;
      cursor: pointer;
      box-shadow: inset 0 0 0 0 transparent;
      user-select: none;
    }
    nav.sidebar ul li a:hover,
    nav.sidebar ul li .menu-link:hover,
    nav.sidebar ul li a:focus,
    nav.sidebar ul li .menu-link:focus {
      background-color: #D7AC71;
      color: #402F4E;
      outline: none;
      box-shadow: inset 0 0 18px 5px rgba(215,172,113,0.7);
    }

    nav.sidebar.hidden {
      display: none;
    }

    .submenu {
      margin-left: 15px;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
      padding-left: 10px;
    }

    .submenu.open {
      max-height: 500px;
    }

    .submenu li a {
      font-size: 0.9rem;
      padding-left: 25px;
      gap: 8px;
    }

    .toggle-btn {
      cursor: pointer;
      font-weight: 700;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 12px;
      border-radius: 8px;
      background-color: #5a3c6d;
      color: #f5e1a4;
      user-select: none;
      box-shadow: 0 0 6px 0 rgba(215,172,113,0.6);
      transition: background-color 0.3s, box-shadow 0.3s;
      gap: 10px;
    }

    .toggle-btn:hover {
      background-color: #D7AC71;
      color: #402F4E;
      box-shadow: 0 0 12px 4px rgba(215,172,113,0.9);
    }

    .arrow {
      border: solid #D7AC71;
      border-width: 0 3px 3px 0;
      display: inline-block;
      padding: 3px;
      transform: rotate(45deg);
      transition: transform 0.3s ease;
      margin-left: 8px;
    }

    .arrow.down {
      transform: rotate(135deg);
    }

    main.content {
      flex-grow: 1;
      padding: 30px;
      padding-top: 70px; /* Agregado para que no lo tape el botón */
      overflow-y: auto;
      background-color: <?= $_SESSION['usuario']['modo_tema'] ? '#121212' : '#f9f9f9'; ?>;
    }

    main.content h1 {
      margin-top: 0;
      font-size: 2.5rem;
      color: <?= $_SESSION['usuario']['modo_tema'] ? '#fff' : '#402F4E'; ?>;
      text-shadow: 0 0 6px rgba(215,172,113,0.7);
    }

    main.content p {
      font-size: 1.2rem;
      margin-bottom: 20px;
    }

    .logout-btn {
      background-color: #aa4a44;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: 700;
      cursor: pointer;
      width: 100%;
      margin-top: 20px;
      transition: background-color 0.3s ease;
      box-shadow: 0 4px 8px rgba(170, 74, 68, 0.85);
    }

    .logout-btn:hover {
      background-color: #e04c4c;
    }

    /* Iconos SVG estilo */
    .icon {
      width: 22px;
      height: 22px;
      fill: currentColor;
      flex-shrink: 0;
      filter: drop-shadow(0 0 1px rgba(0,0,0,0.3));
      transition: transform 0.2s ease;
    }
    nav.sidebar ul li a:hover .icon,
    nav.sidebar ul li .menu-link:hover .icon {
      transform: scale(1.2);
      filter: drop-shadow(0 0 3px rgba(0,0,0,0.5));
    }
  </style>
</head>
<body>
<button id="toggleSidebar" style="
  position: absolute;
  top: 20px;
  left: 20px;
  z-index: 1000;
  background-color: #D7AC71;
  color: #402F4E;
  border: none;
  padding: 10px 15px;
  border-radius: 10px;
  font-weight: bold;
  cursor: pointer;
">
  ☰ Menú
</button>

<nav class="sidebar" role="navigation" aria-label="Menú principal">
  <h2>Menú</h2>
  <ul>
    <li>
      <a href="dashboard.php" tabindex="0" class="menu-link">
        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8v-10h-8v10zm0-18v6h8V3h-8z"/></svg>
        Inicio
      </a>
    </li>
    <li>
      <div class="toggle-btn" id="gruposToggle" tabindex="0" role="button" aria-expanded="false" aria-controls="submenuGrupos" aria-label="Mostrar u ocultar submenu grupos">
        Grupos
        <i class="arrow"></i>
      </div>
      <ul class="submenu" id="submenuGrupos" role="region" aria-label="Submenú de grupos" hidden>
        <?php if ($_SESSION['usuario']['carrera'] === 'Sistemas'): ?>
          <li>
            <a href="grupo6502.php" tabindex="0" class="menu-link">
              <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 16h8M8 12h8M8 8h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              Grupo 6502
            </a>
          </li>
          <li>
            <a href="grupo6503.php" tabindex="0" class="menu-link">
              <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 16h8M8 12h8M8 8h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              Grupo 6503
            </a>
          </li>
        <?php elseif ($_SESSION['usuario']['carrera'] === 'Contador Publico'): ?>
          <li>
            <a href="grupo6504.php" tabindex="0" class="menu-link">
              <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 16h8M8 12h8M8 8h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              Grupo 6504
            </a>
          </li>
          <li>
            <a href="grupo6505.php" tabindex="0" class="menu-link">
              <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 16h8M8 12h8M8 8h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              Grupo 6505
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </li>

    <li>
      <a href="ajustes.php" tabindex="0" class="menu-link">
        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M19.14 12.94a7.004 7.004 0 000-1.88l2.03-1.58a.5.5 0 00.12-.64l-1.92-3.32a.5.5 0 00-.6-.22l-2.39.96a6.978 6.978 0 00-1.6-.93l-.36-2.54a.5.5 0 00-.5-.43h-3.84a.5.5 0 00-.5.43l-.36 2.54a6.978 6.978 0 00-1.6.93l-2.39-.96a.5.5 0 00-.6.22l-1.92 3.32a.5.5 0 00.12.64l2.03 1.58a7.004 7.004 0 000 1.88l-2.03 1.58a.5.5 0 00-.12.64l1.92 3.32a.5.5 0 00.6.22l2.39-.96c.48.36 1 .66 1.6.93l.36 2.54a.5.5 0 00.5.43h3.84a.5.5 0 00.5-.43l.36-2.54a6.978 6.978 0 001.6-.93l2.39.96a.5.5 0 00.6-.22l1.92-3.32a.5.5 0 00-.12-.64l-2.03-1.58zM12 15.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7z"/></svg>
        Ajustes
      </a>
    </li>

    <li>
      <a href="../php/perfil.php" tabindex="0" class="menu-link">
        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" fill="none"/><path d="M4 20c0-4 8-4 8-4s8 0 8 4v1H4v-1z" stroke="currentColor" stroke-width="2" fill="none"/></svg>
        Perfil
      </a>
    </li>

    <li>
      <form action="../php/logout.php" method="POST" onsubmit="return confirm('¿Seguro que deseas cerrar sesión?');" style="margin:0;">
        <button type="submit" class="logout-btn" tabindex="0">
          <svg class="icon" viewBox="0 0 24 24" aria-hidden="true" style="margin-right:8px;">
            <path d="M16 17l5-5-5-5M21 12H9M13 5v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-2"/>
          </svg>
          Cerrar Sesión
        </button>
      </form>
    </li>
  </ul>
</nav>

<main class="content" role="main">
  <div style="
    background: linear-gradient(135deg, #D7AC71 0%, #f5e1a4 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 25px;
  ">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Usuario" width="100" height="100" style="border-radius: 50%; border: 4px solid #402F4E; background: #fff;" />
    <div>
      <h1 style="margin: 0; font-size: 2.2rem; color: #402F4E;">¡Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?>!</h1>
      <p style="font-size: 1.1rem; margin-top: 5px; color: #333;">
        Carrera: <strong><?= htmlspecialchars($_SESSION['usuario']['carrera']); ?></strong>
      </p>
    </div>
  </div>

  <div style="
    background-color: <?= $_SESSION['usuario']['modo_tema'] ? '#2a2a2a' : '#fff' ?>;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    color: <?= $_SESSION['usuario']['modo_tema'] ? '#f5f5f5' : '#222' ?>;
  ">
    <h2 style="margin-top: 0; color: <?= $_SESSION['usuario']['modo_tema'] ? '#f5e1a4' : '#402F4E' ?>;">Panel de inicio</h2>
    <p style="font-size: 1.05rem;">Selecciona un grupo del menú lateral para ingresar a su sección. Aquí podrás administrar la asistencia, ver perfiles, ajustar configuraciones y mucho más.</p>
    <img src="https://cdn-icons-png.flaticon.com/512/2164/2164706.png" alt="Dashboard" width="100" style="margin-top: 10px; opacity: 0.7;" />
  </div>
</main>

<script>
  const toggleBtn = document.getElementById('gruposToggle');
  const submenu = document.getElementById('submenuGrupos');
  const arrow = toggleBtn.querySelector('.arrow');

  toggleBtn.addEventListener('click', () => {
    const expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
    toggleBtn.setAttribute('aria-expanded', String(!expanded));
    if (!expanded) {
      submenu.classList.add('open');
      submenu.removeAttribute('hidden');
    } else {
      submenu.classList.remove('open');
      submenu.setAttribute('hidden', '');
    }
    arrow.classList.toggle('down');
  });
</script>
<script>
  // Script para mostrar/ocultar menú lateral
  document.getElementById('toggleSidebar').addEventListener('click', () => {
    const sidebar = document.querySelector('nav.sidebar');
    sidebar.classList.toggle('hidden');
  });
</script>

</body>
</html>
