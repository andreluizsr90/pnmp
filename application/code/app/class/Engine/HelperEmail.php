<?php

namespace App\Engine;

use PHPMailer\PHPMailer\PHPMailer;

class HelperEmail
{

	public static function send($to, $subject, $msg)
	{

		$mail = new PHPMailer(true);

		//Enable SMTP debugging.
		//$mail->SMTPDebug = 3;

		$mail->isSMTP();
		$mail->SMTPAuth = true;

		if(defined('CFG_EMAIL_HOST')) { $mail->Host = CFG_EMAIL_HOST; }
		if(defined('CFG_EMAIL_USER')) { $mail->Username = CFG_EMAIL_USER; }
		if(defined('CFG_EMAIL_PASS')) { $mail->Password = CFG_EMAIL_PASS; }
		if(defined('CFG_EMAIL_SMTP')) { $mail->SMTPSecure = CFG_EMAIL_SMTP; }
		if(defined('CFG_EMAIL_PORT')) { $mail->Port = CFG_EMAIL_PORT; }

		if(defined('CFG_EMAIL_FROM')) { $mail->From = CFG_EMAIL_FROM; }
		if(defined('CFG_EMAIL_NAME')) { $mail->FromName = CFG_EMAIL_NAME; }

		if(defined('CFG_EMAIL_FROM') && defined('CFG_EMAIL_NAME')) { 
			$mail->addReplyTo(CFG_EMAIL_FROM, CFG_EMAIL_NAME);
		}

		$mail->addAddress($to);

		$mail->isHTML(true);

		$mail->Subject = $subject;
		$mail->Body = $msg;

		try {
			$mail->send();
			return true;
		} catch (\Exception $e) {
			echo 'Caught exception: ' . $e->getMessage() . "\n";
		}

		return false;
	}
}
