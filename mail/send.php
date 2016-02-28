<?php

	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: application/json');

	require 'PHPMailer/class.phpmailer.php';
	require 'PHPMailer/class.smtp.php';

	$mailPerHourLimit       = 5; // messages quantity limit
	$lockingDurationInHours = 2; // duration of locking

	$charset        = 'UTF-8'; // set email charset
	$recipientEmail = 'vtx@lambo-project.com';//'o.budenkevych@gmail.com';
	$senderEmail    = 'mailer@lambo-project.com';
	$senderName     = 'Галицьке Джерело';

	$useSMTP = false; // variable that allow mail sending with smtp
	if ($useSMTP)
	{
		$host           = 'smtp.gmail.com';              // SMTP host 'smtp1.example.com;smtp2.example.com';
		$username       = 'support@byte.lv.tcworld.net'; // SMTP username 'user@example.com';
		$password       = 'Gfhjkm<fqnsdcmrjujCfggjhnf';  // SMTP password 'secret';
		$secureProtocol = 'tls';                         // TLS or SLL encryption
		$port           = 587;                           // TCP Port
	}
    
	if ($_POST['form_data'])
	{
		$data = json_decode($_POST['form_data'], true);

		// message data reading
		$subject = $data['subject']['value'];
		$message = $data['message']['value'];
		$name    = $data['name']['value'];
		$email   = $data['email']['value'];

		// message body formatting below
		$body  = '<div>';
		$body .= 'Тема: '  . $subject . '<br />';
		$body .= 'Ім\'я: ' . $name    . '<br />';
		$body .= 'Email: ' . $email   . '<br />';
		$body .= '<br />'  . $message;
		$body .= '</div>';

		$answer = array('status' => -1, 'message' => '');

		$sessionId = date('Y-m-d') . md5('-session-mail');
		session_id($sessionId);

		if (!session_start($sessionId))
		{
			$answer['status'] = 2;
			$answer['message'] = 'session initialize error';

			echo json_encode($answer);
			die();
		}

		if (!isset($_SESSION['ip_mails']))
		{
			$_SESSION['ip_mails'] = array();
		}

		$ip = $_SERVER['REMOTE_ADDR'];

		$isLocked = false;
		if (array_key_exists($ip, $_SESSION['ip_mails']) and
			count($_SESSION['ip_mails'][$ip]) >= $mailPerHourLimit)
		{
			$lastMessages = array_slice($_SESSION['ip_mails'][$ip], -$mailPerHourLimit);

			$firstMsgDt = reset($lastMessages);
			$lastMsgDt  = end($lastMessages);
			$nowDt      = date('Y-m-d H:i:s');
						                    
			$hoursBetweenDates = function($dt1, $dt2)
			{
				return (int)((strtotime($dt1) - strtotime($dt2)) / 3600);
			};

			$hoursBetweenMsgs = $hoursBetweenDates($firstMsgDt, $lastMsgDt);
			$hoursElapsedFromLastMsg = $hoursBetweenDates($nowDt, $lastMsgDt);

			if ($hoursBetweenMsgs == 0 and
				$hoursElapsedFromLastMsg < $lockingDurationInHours)
			{
				$isLocked = true;
			}
		}

		if (!$isLocked)
		{
			$mail = new PHPMailer;
			$mail->CharSet = $charset;

			if ($useSMTP)
			{
				$mail->isSMTP();                     // set mailer to use SMTP
				$mail->Host       = $host;           // set SMTP host(s)
				$mail->SMTPAuth   = true;            // enable SMTP auth
				$mail->Username   = $username;       // set SMTP username
				$mail->Password   = $password;       // set SMTP password
				$mail->SMTPSecure = $secureProtocol; // enable TLS or SSL encryption
				$mail->Port       = $port;           // set TCP port
			}

			$mail->setFrom($senderEmail, $senderName); // set sender info
			$mail->addAddress($recipientEmail);        // set recipient
			$mail->isHTML(true);                       // set email format to HTML
			$mail->Subject = $subject;                 // set message subject
			$mail->Body    = $body;                    // set message body

			if ($mail->send())
			{
				$_SESSION['ip_mails'][$ip][] = date('Y-m-d H:i:s');
				$answer['status'] = 0;
			}
			else
			{
				$answer['status'] = 3;
				$answer['message'] = $mail->ErrorInfo;
			}
		}
		else
		{			
			$answer['status'] = 1;
			$answer['message'] = 'Exceeded mail limit per hour';
		}
	}

	echo json_encode($answer);
	die();

?>