<?php
// Basic error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Get POST data
$nom = isset($_POST['nom']) ? trim(strip_tags($_POST['nom'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';
$telephone = isset($_POST['telephone']) ? trim(strip_tags($_POST['telephone'])) : '';
$sujet = isset($_POST['sujet']) ? trim(strip_tags($_POST['sujet'])) : '';

// Debug log
error_log("Received POST data: " . print_r($_POST, true));

// Validate inputs
$errors = array();

if (empty($nom) || !preg_match("/^[a-zA-ZÀ-ÿ\s]+$/u", $nom)) {
    $errors[] = "Nom invalide";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide";
}

if (empty($message)) {
    $errors[] = "Message requis";
}

if (!empty($telephone) && !preg_match("/^[0-9\-\+\(\)\s]+$/", $telephone)) {
    $errors[] = "Téléphone invalide";
}

if (empty($sujet)) {
    $errors[] = "Service requis";
}

// If there are errors, return them
if (!empty($errors)) {
    echo json_encode(array(
        'success' => false,
        'message' => implode(', ', $errors)
    ));
    exit;
}

// Current date in French format
setlocale(LC_TIME, 'fr_FR.UTF-8');
$date = strftime("%d %B %Y");

// Prepare email
$to = 'commercial@central-tic.dz';
$email_subject = "Nouvelle demande de service - $sujet";

// HTML email template
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .detail { margin: 10px 0; }
        .label { font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>CENTRAL TIC - Nouvelle Demande de Service</h2>
            <p>$date</p>
        </div>
        <div class='content'>
            <div class='detail'><span class='label'>Nom:</span> $nom</div>
            <div class='detail'><span class='label'>Email:</span> $email</div>
            <div class='detail'><span class='label'>Téléphone:</span> $telephone</div>
            <div class='detail'><span class='label'>Service Demandé:</span> $sujet</div>
            <div class='detail'>
                <span class='label'>Message:</span><br>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
        </div>
        <div class='footer'>
            <p>Ce message a été envoyé via le formulaire de contact de Central TIC</p>
            <p>  " . date('Y') . " Central TIC. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>";

// Plain text version for email clients that don't support HTML
$plain_text = "CENTRAL TIC - Nouvelle Demande de Service\n\n";
$plain_text .= "Date: $date\n\n";
$plain_text .= "INFORMATIONS DU CLIENT\n";
$plain_text .= "-------------------\n";
$plain_text .= "Nom: $nom\n";
$plain_text .= "Email: $email\n";
$plain_text .= "Téléphone: $telephone\n";
$plain_text .= "Service Demandé: $sujet\n\n";
$plain_text .= "MESSAGE\n";
$plain_text .= "-------------------\n";
$plain_text .= "$message\n\n";
$plain_text .= "-------------------\n";
$plain_text .= "Envoyé via le formulaire de contact de Central TIC\n";

// Email headers
$boundary = md5(time());
$headers = array(
    "MIME-Version: 1.0",
    "Content-Type: multipart/alternative; boundary=\"$boundary\"",
    "From: Central TIC <noreply@" . $_SERVER['HTTP_HOST'] . ">",
    "Reply-To: $email",
    "X-Mailer: PHP/" . phpversion()
);

// Compose the message with both HTML and plain text versions
$message = "--$boundary\n";
$message .= "Content-Type: text/plain; charset=UTF-8\n";
$message .= "Content-Transfer-Encoding: 7bit\n\n";
$message .= $plain_text . "\n\n";
$message .= "--$boundary\n";
$message .= "Content-Type: text/html; charset=UTF-8\n";
$message .= "Content-Transfer-Encoding: 7bit\n\n";
$message .= $email_body . "\n\n";
$message .= "--$boundary--";

// Try to send email
$mail_sent = @mail($to, $email_subject, $message, implode("\r\n", $headers));

// Log the attempt
error_log("Mail attempt to $to: " . ($mail_sent ? 'Success' : 'Failed'));

// Return response
echo json_encode(array(
    'success' => $mail_sent,
    'message' => $mail_sent ? 'Votre message a été envoyé avec succès! Nous vous contacterons bientôt.' : "L'envoi du message a échoué. Veuillez réessayer."
));
