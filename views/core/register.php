<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/register.php - Frontend user registration page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/


	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/header.php";

	$session = Session::getCurrentSession();
	if ($session)
		$app->redirectWithMessages("/", array("type" => "error", "content" => "There is an active session"));

	$message = false;

	if (
		isset($app->request->query["user_id"]) &&
		isset($app->request->query["user_name"]) &&
		isset($app->request->query["user_email"]) &&
		isset($app->request->query["user_password"]) &&
		isset($app->request->query["user_confirm"]) &&
		filter_var($app->request->query["user_email"], FILTER_VALIDATE_EMAIL)
	) {
		$id = $app->request->query["user_id"];
		$name = $app->request->query["user_name"];
		$email = $app->request->query["user_email"];
		$password = $app->request->query["user_password"];
		$confirm = $app->request->query["user_confirm"];

		if ($app("users")->exists($id))
			$app->redirectWithMessages("/register", array("type" => "error", "content" => "User with ID '$id' already exists"));

		if ($password != $confirm)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => "Passwords do not match"));

		$password_min =  $app->getSetting("users.password_min", 8);
		$password_min_letters = $app->getSetting("users.password_min_letters", 2);
		$password_min_numbers = $app->getSetting("users.password_min_numbers", 2);
		$password_min_special = $app->getSetting("users.password_min_special", 1);

		if (strlen($password) < $password_min)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => "Password must be $password_min or more characters"));

		if (preg_match_all("/[a-z]/i", $password) < $password_min_letters)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => "Password must have $password_min_letters or more letters"));

		if (preg_match_all("/[0-9]/", $password) < $password_min_numbers)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => "Password must have $password_min_numbers or more numbers"));

		if (preg_match_all("/[^a-z0-9]/i", $password) < $password_min_special)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => "Password must have $password_min_special or more special characters"));

		$token = Randomizer::randomString(64, 36);
		$body = "Your account on " . $app->getSetting("sitename") . " has been registered.\n";
		$body .= "Please activate it by clicking or pasting the following URL into your browser:\n";
		$body .= "http" . (($_SERVER["HTTPS"]) ? "s" : "") . "://" . $_SERVER["SERVER_NAME"] . Config::WEBROOT . "/activate_account?user_id=$id&token=$token";

		$sent = Mailer::sendMail(array($email), "Activate your " . $app->getSetting("sitename") . " account", $body);
		if ($sent === true) {
			$app->database->writeRow("activation", $id, array("token" => $token));

			$app("users")->set($id, new User(array(
				"id"	=> $id,
				"name"	=> $name,
				"email" => $email,
				"group" => $app->getSetting("users.default_group"),
				"created" => time(),
				"modified" => time(),
				"loggedin" => 0,
				"activated" => 0
			)));

			$app->auth->setPassword($id, $password);

			$message = true;
			$app->page->setContent("Successful registration. Please check your email to activate the account.");
		} else {
			$message = true;
			$app->page->setContent("Activation email could not be sent:\n$sent");
		}
	}

	$app->page->setTitle("Register");

	if (!$message) $app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<form method="post">
				<label for="user_name">Name:</label>
				<input type="text" class="form-control" name="user_name" required />

				<label for="user_id">User ID:</label>
				<input type="text" class="form-control" name="user_id" required />

				<label for="user_email">Email:</label>
				<input type="email" class="form-control" name="user_email" required />

				<label for="user_password">Password:</label>
				<input type="password" class="form-control" name="user_password" required />

				<label for="user_confirm">Confirm Password:</label>
				<input type="password" class="form-control" name="user_confirm" required />

				<button type="submit" class="btn btn-primary mt-3">Register</button>
				<a class="btn btn-link mt-3" href="<?php echo Config::WEBROOT; ?>/login">Log in</a>
			</form>
		</div>
<?php
	});

	$app->renderPage();
?>
