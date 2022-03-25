<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/user_profile.php - Frontend user profile editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/


	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/core/header.php";

	$session = $app->session->getCurrentSession();
	if (!$session)
		$app->redirectWithMessages("/", array("type" => "error", "content" => "You are not logged in"));
	$user = $app->users->getUser($session->user);

	$app->vars["user_id"] = $user->id;
	$app->vars["user_name"] = $user->name;
	$app->vars["user_email"] = $user->email;

	if (
		isset($app->request->query["user_name"]) &&
		isset($app->request->query["user_email"]) &&
		isset($app->request->query["user_password_current"]) &&
		filter_var($app->request->query["user_email"], FILTER_VALIDATE_EMAIL)
	) {
		if (!$app->auth->authenticateUser($user->id, $app->request->query["user_password_current"]))
			$app->redirectWithMessages("/user_profile", array("type" => "error", "content" => "Current password does not match"));

		$user->name = $app->request->query["user_name"];
		$user->email = $app->request->query["user_email"];
		$user->modified = time();

		$app->vars["user_name"] = $user->name;
		$app->vars["user_email"] = $user->email;

		if (isset($app->request->query["user_password"]) && strlen($app->request->query["user_password"])) {
			$password = $app->request->query["user_password"];
			$password_min =  $app->getSetting("users.password_min", 8);
			$password_min_letters = $app->getSetting("users.password_min_letters", 2);
			$password_min_numbers = $app->getSetting("users.password_min_numbers", 2);
			$password_min_special = $app->getSetting("users.password_min_special", 1);

			if (strlen($password) < $password_min)
				$app->redirectWithMessages("/user_profile", array("type" => "error", "content" => "Password must be $password_min or more characters"));

			if (preg_match_all("/[a-z]/i", $password) < $password_min_letters)
				$app->redirectWithMessages("/user_profile", array("type" => "error", "content" => "Password must have $password_min_letters or more letters"));

			if (preg_match_all("/[0-9]/", $password) < $password_min_numbers)
				$app->redirectWithMessages("/user_profile", array("type" => "error", "content" => "Password must have $password_min_numbers or more numbers"));

			if (preg_match_all("/[^a-z0-9]/i", $password) < $password_min_special)
				$app->redirectWithMessages("/user_profile", array("type" => "error", "content" => "Password must have $password_min_special or more special characters"));

			$app->auth->setPassword($user->id, $password);
		}

		$app->users->setUser($user->id, $user);
		$app->page->alerts["edit_success"] = array("class" => "success", "content" => "Changes saved.");
	}

	$app->page->setTitle("User Profile");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<form method="post">
				<label>User ID:</label>
				<input type="text" class="form-control" value="<?php echo $app->vars["user_id"]; ?>" disabled />
				<label for="user_name">Name:</label>
				<input type="text" class="form-control" name="user_name" value="<?php echo htmlentities($app->vars["user_name"]); ?>" required />
				<label for="user_email">Email:</label>
				<input type="email" class="form-control" name="user_email" value="<?php echo htmlentities($app->vars["user_email"]); ?>" required />
				<label for="user_password">New Password:</label>
				<input type="password" class="form-control" name="user_password" />
				<label for="user_password_current">Current Password:</label>
				<input type="password" class="form-control" name="user_password_current" required />
				<button type="submit" class="btn btn-primary mt-3">Save changes</button>
			</form>
		</div>
<?php
	});

	$app->renderPage();
?>
