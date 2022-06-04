<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/register.php - Frontend user registration page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/


	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/header.php";

	$session = \Crispage\Assets\Session::getCurrentSession();
	if ($session)
		$app->redirectWithMessages("/", array("type" => "error", "content" => $app("i18n")->getString("active_session")));

	$message = false;

	if (
		isset($app->request->query["user_id"]) &&
		isset($app->request->query["user_name"]) &&
		isset($app->request->query["user_email"]) &&
		isset($app->request->query["user_password"]) &&
		isset($app->request->query["user_confirm"]) &&
		filter_var($app->request->query["user_email"], FILTER_VALIDATE_EMAIL)
	) {
		$app->events->trigger("frontend.view.register.submit");
		$id = $app->request->query["user_id"];
		$name = $app->request->query["user_name"];
		$email = $app->request->query["user_email"];
		$password = $app->request->query["user_password"];
		$confirm = $app->request->query["user_confirm"];

		if ($app("users")->exists($id))
			$app->redirectWithMessages("/register", array("type" => "error", "content" => $app("i18n")->getString("user_already_exists", null, $id)));

		if ($password != $confirm)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => $app("i18n")->getString("passwords_not_match")));

		$password_min =  $app->getSetting("users.password_min", 8);
		$password_min_letters = $app->getSetting("users.password_min_letters", 2);
		$password_min_numbers = $app->getSetting("users.password_min_numbers", 2);
		$password_min_special = $app->getSetting("users.password_min_special", 1);

		if (strlen($password) < $password_min)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => $app("i18n")->getString("password_min_chars", null, $password_min)));

		if (preg_match_all("/[a-z]/i", $password) < $password_min_letters)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => $app("i18n")->getString("password_min_letters", null, $password_min_letters)));

		if (preg_match_all("/[0-9]/", $password) < $password_min_numbers)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => $app("i18n")->getString("password_min_numbers", null, $password_min_numbers)));

		if (preg_match_all("/[^a-z0-9]/i", $password) < $password_min_special)
			$app->redirectWithMessages("/register", array("type" => "error", "content" => $app("i18n")->getString("password_min_special", null, $password_min_special)));

		$token = Randomizer::randomString(64, 36);
		$url = (($_SERVER["HTTPS"]) ? "https://" : "http://") . $_SERVER["SERVER_NAME"] . \Config::WEBROOT . "/activate_account?user_id=$id&token=$token";

		$sent = Mailer::sendMail(
			array($email),
			$app("i18n")->getString("mail_register_subject", null, $app->getSetting("sitename")),
			$app("i18n")->getString("mail_register_body", null, $app->getSetting("sitename"), $url)
		);

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
			$app->page->setContent($app("i18n")->getString("successful_registration"));
		} else {
			$message = true;
			$app->page->setContent($app("i18n")->getString("activation_not_sent", null, $sent));
		}
	}

	$app->page->setTitle($app("i18n")->getString("register"));

	if (!$message) $app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<form method="post">
				<label for="user_name"><?php $app("i18n")("name_c"); ?></label>
				<input type="text" class="form-control" name="user_name" required />

				<label for="user_id"><?php $app("i18n")("user_id_c"); ?></label>
				<input type="text" class="form-control" name="user_id" required />

				<label for="user_email"><?php $app("i18n")("email_c"); ?></label>
				<input type="email" class="form-control" name="user_email" required />

				<label for="user_password"><?php $app("i18n")("password_c"); ?></label>
				<input type="password" class="form-control" name="user_password" required />

				<label for="user_confirm"><?php $app("i18n")("confirm_password_c"); ?></label>
				<input type="password" class="form-control" name="user_confirm" required />

				<button type="submit" class="btn btn-primary mt-3"><?php $app("i18n")("register"); ?></button>
				<a class="btn btn-link mt-3" href="<?php echo \Config::WEBROOT; ?>/login"><?php $app("i18n")("log_in"); ?></a>
			</form>
		</div>
<?php
	});

	$app->events->trigger("frontend.view.register");

	$app->renderPage();
?>
