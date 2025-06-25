<?php
include 'conexion.php';

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

if (!$token || !$password || !$confirmar) {
  echo "Campos incompletos.";
  exit;
}

if ($password !== $confirmar) {
  echo "Las contraseñas no coinciden.";
  exit;
}

// Buscar al usuario con ese token
$stmt = $conn->prepare("SELECT * FROM usuario WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Token inválido o expirado.";
  exit;
}

// Guardar nueva contraseña
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt2 = $conn->prepare("UPDATE usuario SET contrasena = ?, reset_token = NULL WHERE reset_token = ?");
$stmt2->bind_param("ss", $hashed, $token);
$stmt2->execute();

echo "<script>alert('Contraseña actualizada exitosamente'); window.location.href='../html/iniciar_sesion.html';</script>";
?>
