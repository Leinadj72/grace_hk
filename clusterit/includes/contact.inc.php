<?php

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendMail()
{
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->SMTPDebug = 2;
	$mail->Host = 'smtp.hostinger.com';
	$mail->Port = 587;
	$mail->SMTPAuth = true;
	$mail->Username = 'test@hostinger-tutorials.com';
	$mail->Password = 'EMAIL_ACCOUNT_PASSWORD';
	$mail->setFrom('test@hostinger-tutorials.com', 'Your Name');
	$mail->addReplyTo('test@hostinger-tutorials.com', 'Your Name');
	$mail->addAddress('example@email.com', 'Receiver Name');
	$mail->Subject = 'Testing PHPMailer';
	$mail->msgHTML(file_get_contents('message.html'), __DIR__);
	$mail->Body = 'This is a plain text message body';
//$mail->addAttachment('test.txt');
	if (!$mail->send()) {
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
		echo 'The email message was sent.';
	}
}

// check if the it is set to the name
if (isset($_POST['name']) && isset($_POST['mail']) && isset($_POST['subject']) && isset($_POST['message'])) {
	// get input of the user and trim it
	$name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
	$subject = trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS));
	$mailFrom = trim(filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_SPECIAL_CHARS));
	$message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS));

	$mailTo = "contact@clusteritsolutions.com";
	$headers = "From: " . $mailFrom;
	$txt = "You have received an e-mail from " . $name . ".\n\n" . $message;

	$data = [
		'name_err' => '',
		'email_err' => '',
		'subject_err' => '',
		'message_err' => '',
		'success' => '',
	];

	// validate user input
	if (empty($name)) {
		$data['name_err'] = "Please enter your name";
	}
	if (empty($mailFrom)) {

		if (!filter_var($mailFrom, FILTER_VALIDATE_EMAIL)) {
			$data['email_err'] = "Invalid email format";
		}
	}

	if (empty($subject)) {
		$data['subject_err'] = "Please the subject";
	}
	if (empty($message)) {
		$data['message_err'] = "Please enter your message";
	}

	if (empty($data['name_err']) && empty($data['email_err']) && empty($data['subject_err']) && empty($data['message_err'])) {

		//Send the mail
		mail($mailTo, $subject, $txt, $headers);
		$data['success'] = "Thank you for contacting us";
	}

	//send error to the frontend
	echo json_encode($data);
}
