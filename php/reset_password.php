<?php
header('Content-Type: application/json');
require 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$token = $data['token'] ?? '';
$newPassword = $data['newPassword'] ?? '';

if (!$token || !$newPassword) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
    exit;
}

// Buscar usuario por token válido
$stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE reset_token = ? AND token_expira > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Token inválido o expirado.']);
    exit;
}

$user = $result->fetch_assoc();
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Actualizar contraseña
$update = $conn->prepare("UPDATE usuarios SET contrasena = ?, reset_token = NULL, token_expira = NULL WHERE id_usuario = ?");
$update->bind_param("si", $hashedPassword, $user['id_usuario']);
$update->execute();

echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
