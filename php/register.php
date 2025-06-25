<?php
header('Content-Type: application/json');
include 'conexion.php'; // Ajusta la ruta si es necesario

// Obtener datos JSON del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$nombre = trim($data['nombre']);
$apellido = trim($data['apellido']);
$matricula = trim($data['matricula']);
$correo = trim($data['correo']);
$password = $data['password'];
$carrera = trim($data['carrera']); // ✅ Nuevo campo

// Validaciones básicas
if (empty($nombre) || empty($apellido) || empty($matricula) || empty($correo) || empty($password) || empty($carrera)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, complete todos los campos.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo no válido.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
    exit;
}

// Validar carrera
if (!in_array($carrera, ['Sistemas', 'Contador Publico'])) {
    echo json_encode(['success' => false, 'message' => 'Carrera inválida.']);
    exit;
}

// Verificar si existe matrícula o correo
$stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE matricula = ? OR correo = ?");
$stmt->bind_param('ss', $matricula, $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'La matrícula o correo ya están registrados.']);
    $stmt->close();
    exit;
}
$stmt->close();

// Hashear contraseña
$hash_password = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario con carrera
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, matricula, correo, contrasena, carrera) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssssss', $nombre, $apellido, $matricula, $correo, $hash_password, $carrera);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar usuario.']);
}

$stmt->close();
$conn->close();
?>
