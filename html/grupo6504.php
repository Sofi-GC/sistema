<?php
$grupo = "6504";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Grupo <?= htmlspecialchars($grupo) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    /* Reset y base */
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: 'Open Sans', sans-serif;
      background: linear-gradient(135deg, #f5e1a4 0%, #402f4e 100%);
      color: #402F4E;
      display: flex;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Sidebar */
    nav.sidebar {
      width: 260px;
      background-color: #3f2e59;
      color: #f5e1a4;
      padding: 30px 25px;
      box-shadow: 3px 0 12px rgba(0,0,0,0.4);
      flex-shrink: 0;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      overflow-y: auto;
      transition: transform 0.3s ease;
      z-index: 1100;
      border-radius: 0 25px 25px 0;
      background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=260&q=80');
      background-size: cover;
      background-blend-mode: multiply;
      background-color: rgba(64,47,78,0.8);
    }

    nav.sidebar.hidden {
      transform: translateX(-270px);
      box-shadow: none;
    }

    nav.sidebar h2 {
      margin-top: 0;
      font-weight: 800;
      font-size: 2.2rem;
      margin-bottom: 40px;
      text-align: center;
      letter-spacing: 2px;
      user-select: none;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.7);
    }

    nav.sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    nav.sidebar ul li {
      margin-bottom: 22px;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: inset 0 0 10px rgba(0,0,0,0.3);
    }

    nav.sidebar ul li a {
      color: #f5e1a4;
      text-decoration: none;
      font-weight: 600;
      display: block;
      padding: 14px 18px;
      border-radius: 14px;
      transition: background-color 0.3s, color 0.3s;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
      backdrop-filter: blur(4px);
      background-color: rgba(255,255,255,0.05);
      user-select: none;
    }

    nav.sidebar ul li a:hover {
      background-color: #f5e1a4;
      color: #3f2e59;
      box-shadow: 0 6px 16px rgba(0,0,0,0.4);
      transform: translateX(5px);
    }

    nav.sidebar ul li a.active {
      background-color: #f5e1a4;
      color: #3f2e59;
      font-weight: 700;
      box-shadow: 0 6px 20px rgba(0,0,0,0.5);
    }

    /* Botón de menú */
    #toggleSidebar {
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 1200;
      background-color: #f5e1a4;
      color: #402F4E;
      border: none;
      padding: 12px 18px;
      border-radius: 16px;
      font-weight: 800;
      cursor: pointer;
      user-select: none;
      box-shadow: 0 6px 15px rgba(215, 172, 113, 0.85);
      transition: background-color 0.3s ease, transform 0.15s ease;
    }

    #toggleSidebar:hover {
      background-color: #d1b864;
      transform: scale(1.1);
    }

    /* Contenido principal */
    main.content {
      flex-grow: 1;
      margin-left: 260px;
      padding: 80px 40px 40px 40px;
      width: calc(100% - 260px);
      transition: margin-left 0.3s ease;
      background: #fff;
      border-radius: 20px 0 0 20px;
      box-shadow: 0 15px 40px rgba(64, 47, 78, 0.3);
      position: relative;
      min-height: 100vh;
    }

    nav.sidebar.hidden ~ main.content {
      margin-left: 0;
      width: 100%;
      padding-top: 80px;
      border-radius: 0;
      box-shadow: none;
    }

    /* Botón regresar */
    .btn-regresar {
      display: inline-block;
      margin-bottom: 40px;
      background-color: #402F4E;
      color: #f5e1a4;
      font-weight: 700;
      padding: 14px 30px;
      border-radius: 25px;
      cursor: pointer;
      text-decoration: none;
      box-shadow: 0 8px 16px rgba(64, 47, 78, 0.5);
      transition: background-color 0.3s ease, transform 0.25s ease;
      user-select: none;
      position: relative;
      z-index: 10;
      border: 2px solid #f5e1a4;
      font-size: 1.1rem;
    }

    .btn-regresar:hover {
      background-color: #f5e1a4;
      color: #402F4E;
      transform: scale(1.05);
      border-color: #402F4E;
    }

    /* Titular */
    h1 {
      font-weight: 800;
      font-size: 3rem;
      margin-bottom: 40px;
      letter-spacing: 2px;
      color: #402F4E;
      text-shadow: 1px 1px 3px rgba(215, 172, 113, 0.7);
    }

    /* Contenedor botones */
    .btn-container {
      display: flex;
      gap: 28px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .btn-container a.button {
      background: linear-gradient(135deg, #402F4E 0%, #D7AC71 100%);
      color: #fff;
      padding: 20px 42px;
      border-radius: 35px;
      font-weight: 700;
      text-decoration: none;
      box-shadow: 0 10px 25px rgba(215, 172, 113, 0.8);
      user-select: none;
      font-size: 1.25rem;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: box-shadow 0.3s ease, transform 0.3s ease;
      border: 2px solid #fff;
      position: relative;
      overflow: hidden;
    }

    .btn-container a.button:hover {
      box-shadow: 0 15px 40px rgba(215, 172, 113, 1);
      transform: translateY(-3px);
      border-color: #402F4E;
      color: #402F4E;
      background: #f5e1a4;
    }

    /* Icono pequeño en botón */
    .btn-container a.button::before {
      content: "✏️";
      font-size: 1.4rem;
      animation: pulse 2.5s infinite ease-in-out alternate;
    }

    .btn-container a.button:last-child::before {
      content: "📋";
      animation: none;
    }

    @keyframes pulse {
      0% { transform: scale(1); opacity: 1;}
      100% { transform: scale(1.2); opacity: 0.7;}
    }
  </style>
</head>
<body>

<button id="toggleSidebar" aria-label="Toggle menú">☰ Menú</button>

<nav class="sidebar" id="sidebar" role="navigation" aria-label="Menú lateral">
  <h2>Menú</h2>
  <ul>
    <li><a href="dashboard.php">Inicio</a></li>
    <li><a href="grupo6504.php" class="active">Grupo 6504 (Actual)</a></li>
    <li><a href="grupo6505.php">Grupo 6505</a></li>
  </ul>
</nav>

<main class="content" id="mainContent">
  <a href="dashboard.php" class="btn-regresar" aria-label="Regresar al inicio">← Regresar</a>

  <h1>Grupo <?= htmlspecialchars($grupo) ?></h1>

  <div class="btn-container" role="group" aria-label="Opciones para el grupo">
   <a href="../php/agregar_alumnos.php?grupo=<?= urlencode($grupo) ?>" class="button" title="Modificar lista de alumnos">
  Modificar lista de alumnos
</a>

    <a href="../php/ver_lista.php?grupo=6504" class="button">Lista del grupo</a>

  </div>
</main>

<script>
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('hidden');
    if(sidebar.classList.contains('hidden')){
      mainContent.style.marginLeft = '0';
      mainContent.style.width = '100%';
      mainContent.style.paddingTop = '80px';
    } else {
      mainContent.style.marginLeft = '260px';
      mainContent.style.width = 'calc(100% - 260px)';
      mainContent.style.paddingTop = '80px';
    }
  });
</script>

</body>
</html>
