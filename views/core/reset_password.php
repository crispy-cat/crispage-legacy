<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/reset_password.php - Frontend password reset page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/header.php";

	$session = \Crispage\Assets\Session::getCurrentSession();
	if ($session)
		$app->redirectWithMessages("/", array("type" => "error", "content" => $app("i18n")->getString("active_session")));

	if (isset($app->request->query["user_id"]) && isset($app->request->query["token"]) && isset($app->request->query["password"])) {
		$app->events->trigger("frontend.view.reset_password.submit");
		$id = $app->request->query["user_id"];
		$ars = $app->database->readRow("authreset", $id);
		if (!$ars)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => $app("i18n")->getString("invalid_user_id")));
		if ($app->request->query["token"] != $ars["token"])
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => $app("i18n")->getString("invalid_token")));

		$password = $app->request->query["password"];
		$password_min =  $app->getSetting("users.password_min", 8);
		$password_min_letters = $app->getSetting("users.password_min_letters", 2);
		$password_min_numbers = $app->getSetting("users.password_min_numbers", 2);
		$password_min_special = $app->getSetting("users.password_min_special", 1);

		if (strlen($password) < $password_min)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => $app("i18n")->getString("password_min_chars", null, $password_min)));

		if (preg_match_all("/[a-z]/i", $password) < $password_min_letters)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => $app("i18n")->getString("password_min_letters", null, $password_min_letters)));

		if (preg_match_all("/[0-9]/", $password) < $password_min_numbers)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => $app("i18n")->getString("password_min_numbers", null, $password_min_numbers)));

		if (preg_match_all("/[^a-z0-9]/i", $password) < $password_min_special)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => $app("i18n")->getString("password_min_special", null, $password_min_special)));

		$app->auth->setPassword($id, $password);
		$app->database->deleteRow("authreset", $id);

		$app->redirectWithMessages("/login", array("type" => "success", "content" => $app("i18n")->getString("password_reset")));
	} elseif (isset($app->request->query["user_id"]) && isset($app->request->query["user_email"])) {
		$id = $app->request->query["user_id"];
		$email = $app->request->query["user_email"];
		$user = $app("users")->get($id);
		if (!$user)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => $app("i18n")->getString("user_not_exist")));
		if ($email != $user->email)
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => $app("i18n")->getString("email_not_match")));

		$token = \Crispage\Helpers\Randomizer::randomString(64, 36);
		$url = (($_SERVER["HTTPS"]) ? "https://" : "http://") . $_SERVER["SERVER_NAME"] . \Config::WEBROOT . "/reset_password";

		$sent = \Crispage\Helpers\Mailer::sendMail(
			array($email),
			$app("i18n")->getString("mail_reset_subject", null, $app->getSetting("sitename")),
			$app("i18n")->getString("mail_reset_body", null, $app->getSetting("sitename"), $url, $token)
		);
		if ($sent === true) {
			$app->database->writeRow("authreset", $id, array("token" => $token));
			$app->redirectWithMessages("/reset_password", array("type" => "success", "content" => $app("i18n")->getString("token_sent")));
		} else {
			$app->redirectWithMessages("/reset_password", array("type" => "error", "content" => $app("i18n")->getString("token_not_sent", null, $sent)));
		}
	}

	$app->page->setTitle($app("i18n")->getString("reset_password"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<form method="post">
				<h2><?php $app("i18n")("get_reset_token"); ?></h2>
				<label for="user_id"><?php $app("i18n")("user_id_c"); ?></label>
				<input type="text" class="form-control" name="user_id" required />
				<label for="user_email"><?php $app("i18n")("email_c"); ?></label>
				<input type="email" class="form-control" name="user_email" required />
				<button type="submit" class="btn btn-primary mt-3"><?php $app("i18n")("get_token"); ?></button>
				<hr />
			</form>
			<form method="post">
				<h2><?php $app("i18n")("reset_password"); ?></h2>
				<label for="user_id"><?php $app("i18n")("user_id_c"); ?></label>
				<input type="text" class="form-control" name="user_id" value="<?php echo $app->request->query["user_id"] ?? null; ?>" required />
				<label for="token"><?php $app("i18n")("token_c"); ?></label>
				<input type="text" class="form-control" name="token" value="<?php echo $app->request->query["token"] ?? null; ?>" required />
				<label for="password"><?php $app("i18n")("password_c"); ?></label>
				<input type="password" class="form-control" name="password" required />
				<button type="submit" class="btn btn-primary mt-3"><?php $app("i18n")("reset_password"); ?></button>
			</form>
		</div>
<?php
	});

	$app->events->trigger("frontend.view.reset_password");

	$app->renderPage();
?>
