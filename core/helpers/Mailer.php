<?php
	/*
		Crispage - A lightweight CMS for developers
		core/helpers/Mailer.php - Mailing helper

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/lib/PHPMailer/PHPMailer.php";
	require_once Config::APPROOT . "/lib/PHPMailer/SMTP.php";
	require_once Config::APPROOT . "/lib/PHPMailer/Exception.php";

	class Mailer {
		public static function sendMail(array $to, string $subject, string $body, array $options = array()) {
			global $app;

			$mail = new PHPMailer();
			$mail->isSMTP();
			$mail->SMTPDebug = Config::SMTP_DEBUG;

			$mail->Host = $app->getSetting("mail.hostname", "localhost");
			$mail->Port = $app->getSetting("mail.port", 25);

			$username = $app->getSetting("mail.username");
			if (strlen($username)) {
				$mail->SMTPAuth = true;
				$mail->Username = $username;
				$mail->Password = $app->getSetting("mail.password");
			}

			if (isset($options["from"])) $mail->setFrom($options["from"][0], $options["from"][1]);
			else $mail->setFrom($app->getSetting("mail.from"), $app->getSetting("sitename"));

			foreach ($to as $addr) $mail->addAddress($addr);
			$mail->Subject = $subject;

			$mail->isHTML(false);
			$mail->Body = $body;

			if ($mail->send()) return true;
			else return $mail->ErrorInfo;
		}
	}
?>
