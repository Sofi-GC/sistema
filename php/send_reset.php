<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

require __DIR__ . '/../vendor/autoload.php';
require 'conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';

    if (empty($email)) {
        throw new Exception('No se proporcionó correo');
    }

    // Buscar usuario por correo
    $stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Correo no encontrado']);
        exit;
    }

    $user = $result->fetch_assoc();
    $token = bin2hex(random_bytes(32));
    $token_expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Guardar token en base de datos
    $stmt_update = $conn->prepare("UPDATE usuarios SET reset_token = ?, token_expira = ? WHERE correo = ?");
    if (!$stmt_update) throw new Exception('Error al guardar token: ' . $conn->error);
    $stmt_update->bind_param("sss", $token, $token_expira, $email);
    $stmt_update->execute();

    // Enviar correo
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sg886970@gmail.com'; // TU CORREO GMAIL
    $mail->Password = 'qwar jtlb vofx jyee'; // CONTRASEÑA DE APLICACIÓN
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('sg886970@gmail.com', 'Sistema Asistencia');
    $mail->addAddress($email, $user['nombre']);
    $mail->isHTML(true);
    $mail->Subject = 'Recuperación de Contraseña';
    $mail->Body = "
        <h2>Hola, {$user['nombre']}</h2>
        <p>Haz clic en este enlace para restablecer tu contraseña:</p>
        <p><a href='http://localhost:8080/Sistema/html/reset_password.html?token=$token'>Restablecer Contraseña</a></p>
        <p>Este enlace expirará en 1 hora.</p>
    ";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Correo enviado con éxito. Revisa tu bandeja.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
