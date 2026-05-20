<?php
// PHPMailer laden
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Check POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Haal POST data op
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$subject = isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : '';
$message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

// Validatie
if (empty($name) || empty($phone) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Vul alle verplichte velden in']);
    exit;
}

// Email validatie
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ongeldig e-mailadres']);
    exit;
}

// PHPMailer configuratie
$mail = new PHPMailer(true);

try {
    // Server instellingen
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rhyanthh@gmail.com';
    $mail->Password   = 'vuyafmgarqotkfct';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // ==== EMAIL 1: NAAR JOU (Eigenaar) ====
    $mail->setFrom('rhyanthh@gmail.com', 'Mam\'s Kusina Website');
    $mail->addAddress('rhyanthh@gmail.com');
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Nieuw contactformulier bericht van ' . $name;
    
    $mail->Body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #ffc107; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h1 { margin: 0; color: #000; font-size: 28px; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .info-row { margin-bottom: 15px; padding: 12px; background: white; border-left: 4px solid #ffc107; border-radius: 5px; }
            .label { font-weight: bold; color: #555; margin-right: 10px; }
            .value { color: #000; }
            .message-box { background: white; padding: 20px; margin-top: 20px; border-radius: 5px; border: 1px solid #ddd; }
            .footer { text-align: center; margin-top: 30px; color: #777; font-size: 12px; }
            .button { 
                display: inline-block; 
                padding: 12px 30px; 
                background: #ffc107; 
                color: #fff !important; 
                text-decoration: none; 
                border-radius: 5px; 
                font-weight: bold;
                margin-top: 20px;
            }
            .button:hover { opacity: 0.9; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🍜 Mam's Kusina</h1>
                <p style='margin: 5px 0 0 0; color: #000;'>Nieuw contactformulier bericht</p>
            </div>
            
            <div class='content'>
                <h2 style='color: #333; margin-top: 0;'>Contactgegevens</h2>
                
                <div class='info-row'>
                    <span class='label'>👤 Naam:</span>
                    <span class='value'>{$name}</span>
                </div>
                
                <div class='info-row'>
                    <span class='label'>📞 Telefoon:</span>
                    <span class='value'><a href='tel:{$phone}'>{$phone}</a></span>
                </div>
                
                <div class='info-row'>
                    <span class='label'>📧 Email:</span>
                    <span class='value'><a href='mailto:{$email}'>{$email}</a></span>
                </div>
                
                <div class='info-row'>
                    <span class='label'>🏷️ Onderwerp:</span>
                    <span class='value'>{$subject}</span>
                </div>
                
                <h2 style='color: #333; margin-top: 30px;'>Bericht</h2>
                <div class='message-box'>
                    " . nl2br($message) . "
                </div>
                
                <div style='text-align: center;'>
                    <a href='tel:{$phone}' class='button'>📞 Bel {$name} terug</a>
                    <a href='mailto:{$email}' class='button' style='background: #28a745;'>📧 Stuur Email</a>
                </div>
            </div>
            
            <div class='footer'>
                <p>Dit bericht is verzonden via het contactformulier op mamskusina.nl</p>
                <p>© " . date('Y') . " Mam's Kusina - Kade 22C Piershil, Rotterdam</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $mail->AltBody = "Naam: {$name}\nTelefoon: {$phone}\nEmail: {$email}\nOnderwerp: {$subject}\n\nBericht:\n{$message}";

    // VERSTUUR EERSTE EMAIL
    $mail->send();
    
    // ==== EMAIL 2: BEVESTIGING NAAR KLANT ====
    $mail->clearAddresses();
    $mail->clearReplyTos();
    $mail->addAddress($email, $name);
    $mail->addReplyTo('rhyanthh@gmail.com', 'Mam\'s Kusina');
    
    $mail->Subject = 'Bevestiging - Je bericht is ontvangen bij Mam\'s Kusina';
    
    $mail->Body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #ffc107; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h1 { margin: 0; color: #000; font-size: 28px; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .highlight-box { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #ffc107; border-radius: 5px; }
            .contact-info { background: #fff; padding: 20px; margin: 20px 0; border-radius: 5px; text-align: center; }
            .contact-info a { 
                display: inline-block; 
                margin: 10px; 
                padding: 12px 25px; 
                background: #ffc107; 
                color: #fff !important; 
                text-decoration: none; 
                border-radius: 5px; 
                font-weight: bold;
            }
            .footer { text-align: center; margin-top: 30px; color: #777; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🍜 Mam's Kusina</h1>
                <p style='margin: 5px 0 0 0; color: #000;'>Bedankt voor je bericht!</p>
            </div>
            
            <div class='content'>
                <p>Beste {$name},</p>
                
                <p>Bedankt voor je bericht! We hebben je aanvraag in goede orde ontvangen en nemen zo snel mogelijk contact met je op.</p>
                
                <div class='highlight-box'>
                    <h3 style='margin-top: 0; color: #333;'>📋 Jouw bericht:</h3>
                    <p><strong>Onderwerp:</strong> {$subject}</p>
                    <p style='margin: 0;'><em>" . nl2br($message) . "</em></p>
                </div>
                
                <p>We proberen binnen <strong>24 uur</strong> te reageren. Heb je een dringende vraag? Neem dan direct contact met ons op:</p>
                
                <div class='contact-info'>
                    <h3 style='color: #333; margin-top: 0;'>📞 Direct Contact</h3>
                    <a href='tel:0610480194'>Bel: 0610480194</a>
                    <a href='https://wa.me/31610480194' style='background: #25D366 !important;'>WhatsApp</a>
                </div>
                
                <div style='background: #fff; padding: 15px; margin: 20px 0; border-radius: 5px;'>
                    <p style='margin: 0;'><strong>📍 Bezoekadres:</strong><br>
                    Kade 22C Piershil, Rotterdam</p>
                    <p style='margin: 10px 0 0 0;'><strong>🕐 Openingstijden:</strong><br>
                    Vrijdag: 12:00 - 19:00<br>
                    Zaterdag: 12:00 - 18:00</p>
                </div>
                
                <p>Met vriendelijke groet,<br>
                <strong>Het team van Mam's Kusina</strong></p>
            </div>
            
            <div class='footer'>
                <p>© " . date('Y') . " Mam's Kusina - Authentiek Aziatisch Eten</p>
                <p style='font-size: 10px; color: #999;'>Je kunt op deze email reageren om direct contact met ons op te nemen.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $mail->AltBody = "Beste {$name},\n\n"
        . "Bedankt voor je bericht! We hebben je aanvraag ontvangen en nemen zo snel mogelijk contact met je op.\n\n"
        . "Jouw bericht:\n{$message}\n\n"
        . "Heb je een dringende vraag? Bel ons op 0610480194\n\n"
        . "Met vriendelijke groet,\n"
        . "Mam's Kusina";
    
    // VERSTUUR TWEEDE EMAIL
    $mail->send();
    
    echo json_encode(['success' => true, 'message' => 'Bericht succesvol verzonden!']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Email kon niet verstuurd worden. Error: ' . $mail->ErrorInfo]);
}
?>