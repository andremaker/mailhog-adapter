<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include './PHPMailer/src/Exception.php';
include './PHPMailer/src/PHPMailer.php';
include './PHPMailer/src/SMTP.php';

if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
    echo('{"error": "Only POST requests are allowed"}');
    exit;
}
  
  // Make sure Content-Type is application/json 
$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
if (stripos($content_type, 'application/json') === false) {
    echo('{"error": "Content-Type must be application/json"}');
    exit;
}

$json = file_get_contents("php://input");
$params = json_decode($json,true);

$from = $params['from'];
$to = $params['to'];
$ccs = $params['ccs'];
$subject = $params['subject'];
$msg = $params['message'];

$mail = new PHPMailer;
$mail->isSendmail();
$mail->setFrom($from['email'], $from['name']);
$mail->addAddress($to['email'], $to['name']);

foreach($ccs as $cc){
    $mail->addReplyTo($cc['email'], $cc['name']);
}

$mail->Subject = $subject;
$mail->msgHTML($msg, __DIR__);

if(isset($params['alt_body'])){
    $mail->AltBody = $params['alt_body'];
}

if(isset($params['attachments'])){
    foreach($params['attachments'] as $attachment){
        $mail->addAttachment($attachment);
    }
}
//send the message, check for errors


if (!$mail->send()) {
    echo '{"error": "Mailer Error: '. $mail->ErrorInfo.'"}';
} else {
    echo '{"ok": "Message sent!"}';
}
echo "\n";