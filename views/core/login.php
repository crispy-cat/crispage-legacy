<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/login.php - Frontend login page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/core/header.php";

	$ploc = preg_replace("/\/\//", "/", "/" . ($app->request->query["ploc"] ?? "/"));

	$session = $app->session->getCurrentSession();
	if ($session)
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => "There is an active session"));

	if (isset($app->request->query["user_id"]) && isset($app->request->query["user_password"])) {
		$id = $app->request->query["user_id"];
		$password = $app->request->query["user_password"];

		$user = $app->users->getUser($id);
		if (!$user)
			$app->redirectWithMessages("/login?ploc=" . ($app->request->query["ploc"] ?? ""), array("type" => "error", "content" => "User does not exist"));

		if (!$user->activated)
			$app->redirectWithMessages("/login?ploc=" . ($app->request->query["ploc"] ?? ""), array("type" => "error", "content" => "User is not activated"));

		if (!$app->users->userHasPermissions($session->user, UserPermissions::LOGIN))
			$app->redirectWithMessages("/login?ploc=" . ($app->request->query["ploc"] ?? ""), array("type" => "error", "content" => "You do not have permission to log in"));
			
		if (!$app->auth->authenticateUser($id, $password))
			$app->redirectWithMessages("/login?ploc=" . ($app->request->query["ploc"] ?? ""), array("type" => "error", "content" => "Invalid ID or password"));

		$app->vars["bans"] = $app->bans->getBans($id);
		$app->vars["banned"] = false;
		foreach ($app->vars["bans"] as $ban) if ($ban->expires > time()) $app->vars["banned"] = true;

		if (!$app->vars["banned"]) {
			$app->session->startSession($id);
			$user->loggedin = time();
			$app->users->setUser($id, $user);
			$app->events->trigger("users.log_in", $id);
			$app->redirectWithMessages($ploc, array("type" => "success", "content" => "Welcome, $id"));
		}
	}

	$app->page->setTitle("Log in");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<?php
				if (isset($app->vars["banned"]) && $app->vars["banned"]) {
					foreach ($app->vars["bans"] as $ban) {
						if ($ban->expires <= time()) continue;
			?>
						<div class="ban">
							<h2>You are banned until <?php echo date($app->getSetting("date_format") . " " . $app->getSetting("time_format"), $ban->expires); ?></h2>
							<span>Reason: <?php echo $ban->reason; ?><br />Please contact the administrator.</span>
						</div>
			<?php
					}
				}
			?>
			<form method="post">
				<input type="hidden" name="ploc" value="<?php echo $app->request->query["ploc"] ?? ""; ?>" />

				<label for="user_id">User ID:</label>
				<input type="text" class="form-control" name="user_id" required />

				<label for="user_password">Password:</label>
				<input type="password" class="form-control" name="user_password" required />

				<button type="submit" class="btn btn-primary mt-3">Log in</button>
				<a class="btn btn-link mt-3" href="<?php echo Config::WEBROOT; ?>/reset_password">Reset password</a>
				<a class="btn btn-link mt-3" href="<?php echo Config::WEBROOT; ?>/register">Register</a>
			</form>
		</div>
<?php
	});

	$app->renderPage();
?>
