<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/reset_password.php - Frontend password reset page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/header.php";

	$session = Session::getCurrentSession();
	if ($session)
		$app->redirectWithMessages("/", array("type" => "error", "content" => "There is an active session"));

	if (isset($app->request->query["user_id"]) && isset($app->request->query["token"]) && isset($app->request->query["password"])) {
		$id = $app->request->query["user_id"];
		$ars = $app->database->readRow("authreset", $id);
		if (!$ars)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => "Invalid user ID"));
		if ($app->request->query["token"] != $ars["token"])
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => "Invalid token"));

		$password = $app->request->query["password"];
		$password_min =  $app->getSetting("users.password_min", 8);
		$password_min_letters = $app->getSetting("users.password_min_letters", 2);
		$password_min_numbers = $app->getSetting("users.password_min_numbers", 2);
		$password_min_special = $app->getSetting("users.password_min_special", 1);

		if (strlen($password) < $password_min)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => "Password must be $password_min or more characters"));

		if (preg_match_all("/[a-z]/i", $password) < $password_min_letters)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => "Password must have $password_min_letters or more letters"));

		if (preg_match_all("/[0-9]/", $password) < $password_min_numbers)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => "Password must have $password_min_numbers or more numbers"));

		if (preg_match_all("/[^a-z0-9]/i", $password) < $password_min_special)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => "Password must have $password_min_special or more special characters"));

		$app->auth->setPassword($id, $password);
		$app->database->deleteRow("authreset", $id);

		$app->redirectWithMessages("/login", array("type" => "success", "content" => "Password reset. Please log in."));
	} elseif (isset($app->request->query["user_id"]) && isset($app->request->query["user_email"])) {
		$id = $app->request->query["user_id"];
		$email = $app->request->query["user_email"];
		$user = $app("users")->get($id);
		if (!$user)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => "User does not exist"));
		if ($email != $user->email)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => "Email does not match"));

		$token = Randomizer::randomString(64, 36);

		$body = "Please reset your password by entering the following token:\n$token\n";
		$body .= "into the form at:\n";
		$body .= "http" . (($_SERVER["HTTPS"]) ? "s" : "") . "://" . $_SERVER["SERVER_NAME"] . Config::WEBROOT . "/reset_password";

		$sent = Mailer::sendMail(array($email), "Reset your " . $app->getSetting("sitename") . " password", $body);
		if ($sent === true) {
			$app->database->writeRow("authreset", $id, array("token" => $token));
			$app->redirectWithMessages("/reset_password", array("type" => "success", "content" => "A reset token has been sent to your email."));
		} else {
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => "Token could not be sent: $sent"));
		}
	}

	$app->page->setTitle("Reset Password");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<form method="post">
				<h2>Get reset token</h2>
				<label for="user_id">User ID:</label>
				<input type="text" class="form-control" name="user_id" required />
				<label for="user_email">User email:</label>
				<input type="email" class="form-control" name="user_email" required />
				<button type="submit" class="btn btn-primary mt-3">Get token</button>
				<hr />
			</form>
			<form method="post">
				<h2>Reset password</h2>
				<label for="user_id">User ID:</label>
				<input type="text" class="form-control" name="user_id" required />
				<label for="token">Token:</label>
				<input type="text" class="form-control" name="token" required />
				<label for="password">New password:</label>
				<input type="password" class="form-control" name="password" required />
				<button type="submit" class="btn btn-primary mt-3">Reset password</button>
			</form>
		</div>
<?php
	});

	$app->renderPage();
?>
