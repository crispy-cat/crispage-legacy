<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/login.php - Frontend login page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/header.php";

	$ploc = preg_replace("/\/\//", "/", "/" . ($app->request->query["ploc"] ?? "/"));

	$session = \Crispage\Assets\Session::getCurrentSession();
	if ($session)
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => $app("i18n")->getString("active_session")));

	if (isset($app->request->query["user_id"]) && isset($app->request->query["user_password"])) {
		$app->events->trigger("frontend.view.login.submit");
		$id = $app->request->query["user_id"];
		$password = $app->request->query["user_password"];

		$user = $app("users")->get($id);
		if (!$user)
			$app->redirectWithMessages("/login?ploc=" . ($app->request->query["ploc"] ?? ""), array("type" => "error", "content" => $app("i18n")->getString("user_does_not_exist")));

		if (!$user->activated)
			$app->redirectWithMessages("/login?ploc=" . ($app->request->query["ploc"] ?? ""), array("type" => "error", "content" => $app("i18n")->getString("user_not_activated")));

		if (!\Crispage\Assets\User::userHasPermissions($user->id, \Crispage\Users\UserPermissions::LOGIN))
			$app->redirectWithMessages("/login?ploc=" . ($app->request->query["ploc"] ?? ""), array("type" => "error", "content" => $app("i18n")->getString("no_permission_login")));

		if (!$app->auth->authenticateUser($id, $password))
			$app->redirectWithMessages("/login?ploc=" . ($app->request->query["ploc"] ?? ""), array("type" => "error", "content" => $app("i18n")->getString("invalid_id_or_password")));

		$app->vars["bans"] = $app("bans")->getAll(array("user" => $id));
		$app->vars["banned"] = false;
		foreach ($app->vars["bans"] as $ban) if ($ban->expires > time()) $app->vars["banned"] = true;

		if (!$app->vars["banned"]) {
			\Crispage\Assets\Session::startSession($id);
			$user->loggedin = time();
			$app("users")->set($id, $user);
			$app->events->trigger("users.log_in", $id);
			$app->redirectWithMessages($ploc, array("type" => "success", "content" => $app("i18n")->getString("welcome_v", null, $id)));
		}
	}

	$app->page->setTitle($app("i18n")->getString("log_in"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<?php
				if (isset($app->vars["banned"]) && $app->vars["banned"]) {
					foreach ($app->vars["bans"] as $ban) {
						if ($ban->expires <= time()) continue;
			?>
						<div class="ban">
							<h2><?php $app("i18n")("banned_until", null, date($app->getSetting("date_format") . " " . $app->getSetting("time_format"), $ban->expires)); ?></h2>
							<span><?php $app("i18n")("ban_reason", null, $ban->reason); ?></span>
						</div>
			<?php
					}
				}
			?>
			<form method="post">
				<input type="hidden" name="ploc" value="<?php echo $app->request->query["ploc"] ?? ""; ?>" />

				<label for="user_id"><?php $app("i18n")("user_id_c"); ?></label>
				<input type="text" class="form-control" name="user_id" required />

				<label for="user_password"><?php $app("i18n")("password_c"); ?></label>
				<input type="password" class="form-control" name="user_password" required />

				<button type="submit" class="btn btn-primary mt-3"><?php $app("i18n")("log_in"); ?></button>
				<a class="btn btn-link mt-3" href="<?php echo \Config::WEBROOT; ?>/reset_password"><?php $app("i18n")("reset_password"); ?></a>
				<a class="btn btn-link mt-3" href="<?php echo \Config::WEBROOT; ?>/register"><?php $app("i18n")("register"); ?></a>
			</form>
		</div>
<?php
	});

	$app->events->trigger("frontend.view.login");

	$app->renderPage();
?>
