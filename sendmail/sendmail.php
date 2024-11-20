<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->setLanguage('ru', 'phpmailer/language/');
$mail->IsHTML(true);
//========================================================================================================================================================
// Налаштування SMTP
$mail->SMTPDebug = 0; // Змінити на 0 для виробництва, 1 для простого дебагу, 2 для детального
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'orderrdpsite@gmail.com';
$mail->Password = 'bpuj hpun gwdn doie'; // Переконайтеся, що це правильний пароль
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
//========================================================================================================================================================
$mail->setFrom('orderrdpsite@gmail.com', 'Nimet');
// Вибір email
$mail->addAddress('kosak.major@gmail.com');

// Отримання даних
$name = isset($_POST['form']['text']) ? htmlspecialchars($_POST['form']['text']) : 'Пользователь';
$filePath = __DIR__ . '/leads.txt';

// Читання номеру ліда
if (file_exists($filePath)) {
	$currentLeadNumber = (int)file_get_contents($filePath);
} else {
	$currentLeadNumber = 1;
}

$leadNumber = $currentLeadNumber;
file_put_contents($filePath, $leadNumber + 1);

$mail->Subject = $name . ', Номер ліда: ' . $leadNumber;

// Формування тіла листа
$body = '<h1>Контактні дані:</h1>';
if (!empty($_POST['form']['text'])) {
	$body .= '<p style="font-size: 16px;"><strong>Name:</strong> ' . $name . '</p>';
}
if (!empty($_POST['form']['email'])) {
	$body .= '<p style="font-size: 16px;"><strong>Email:</strong> ' . htmlspecialchars($_POST['form']['email']) . '</p>';
}
if (!empty($_POST['form']['tel'])) {
	$body .= '<p style="font-size: 16px;"><strong>Telephone:</strong> ' . htmlspecialchars($_POST['form']['tel']) . '</p>';
}
if (!empty($_POST['form']['textarea'])) {
	$textareaContent = nl2br(htmlspecialchars($_POST['form']['textarea'])); // Преобразуем переносы строк в <br>
	$body .= '<p style="font-size: 16px;"><strong>Project Description:</strong><br>' . $textareaContent . '</p>';
}


$mail->Body = $body;

// Відправка листа
try {
	if (!$mail->send()) {
		throw new Exception('Помилка відправки: ' . $mail->ErrorInfo);
	} else {
		$message = 'Дані надіслані!';
	}
} catch (Exception $e) {
	$message = 'Помилка: ' . $e->getMessage();
}

$response = ['message' => $message];

header('Content-type: application/json');
echo json_encode($response);
