<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/content/LoginModule.php - Login form module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.1.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class LoginModule extends Module {
		public function render() {
			global $app;

			$session = Session::getCurrentSession();
?>
			<div class="module CustomModule module-<?php echo $this->id; ?> <?php echo $this->options["classes"]; ?>">
				<h3><?php echo $this->title; ?></h3>
<?php
			if ($session) {
?>
				<p>Welcome, <?php echo $app("users")->get($session->user)->name; ?></p>
				<a class="btn btn-primary" href="<?php echo Config::WEBROOT; ?>/logout?ploc=<?php echo $app->request->slug; ?>">Log out</a>
<?php
			} else {
?>
				<form method="post" action="<?php echo Config::WEBROOT; ?>/login">
					<input type="hidden" name="ploc" value="<?php echo $app->request->slug; ?>" />
					<label for="user_id">User ID:</label>
					<input type="text" class="form-control" name="user_id" />
					<label for="user_password">User Password:</label>
					<input type="password" class="form-control" name="user_password" />
					<input type="submit" class="btn btn-primary mt-2" value="Log in" />
				</form>
<?php
			}
?>
			</div>
<?php
		}
	}
?>
