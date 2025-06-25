<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$matricula = $data['matricula'] ?? '';
$password = $data['password'] ?? '';

if (!$matricula || !$password) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE matricula = ?");
$stmt->bind_param('s', $matricula);
$stmt->execute();
$result = $stmt->get_result();

if ($usuario = $result->fetch_assoc()) {
    if (password_verify($password, $usuario['contrasena'])) {
        $_SESSION['usuario'] = [
            'id' => $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'matricula' => $usuario['matricula'],
            'correo' => $usuario['correo'],
            'carrera' => $usuario['carrera'],
            'modo_tema' => $usuario['modo_tema'],
            'notificaciones' => $usuario['notificaciones'],
            'foto' => $usuario['foto']
        ];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

$stmt->close();
$conn->close();
?>
