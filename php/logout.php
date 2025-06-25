<?php
session_start();
session_destroy();
header('Location: ../html/iniciar_sesion.html');
exit;
?>
