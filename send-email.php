<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

// Haal POST data op
$data = json_decode(file_get_contents("php://input"), true);
file_put_contents("debug.txt", print_r($data, true));

$name = $data['name'] ?? '';
$phone = $data['phone'] ?? '';
$email = $data['email'] ?? '';
$subject = $data['subject'] ?? 'Geen onderwerp';
$message = $data['message'] ?? '';

// Email validatie
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ongeldig e-mailadres']);
    exit;
}

// PHPMailer configuratie
$mail = new PHPMailer(true);

try {
    // SMTP instellingen
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mamskusinacontact@gmail.com';
    $mail->Password = 'ttsrvfuedkczvvep'; // jouw app-wachtwoord
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
error_log(print_r($data, true));
    // Afzender & ontvanger
$mail->setFrom('mamskusinacontact@gmail.com', 'Website Contactformulier');
if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $mail->addReplyTo($email, $name);
}    $mail->addAddress('mamskusinacontact@gmail.com');

    // Inhoud
    $mail->isHTML(false);
    $mail->Subject = "Nieuw bericht: " . $subject;
    $mail->Body = "
Naam: $name
Telefoon: $phone
Email: $email

Bericht:
$message
    ";

    $mail->send();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Kon e-mail niet verzenden.',
        'error' => $mail->ErrorInfo
    ]);
}
