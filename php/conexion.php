<?php
// conexion.php — conexión a base de datos MySQL

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sistema';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

$conn->set_charset('utf8mb4');
$ADMIN_PASSWORD = 'admin1234'; // Puedes cambiar esta contraseña secreta
?>
