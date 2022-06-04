<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/content/LoginModule.php - Login form module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.1.0
	*/

	namespace Crispage\Modules;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class LoginModule extends \Crispage\Assets\Module {
		public function render() {
			global $app;

			$session = \Crispage\Assets\Session::getCurrentSession();
?>
			<div class="module CustomModule module-<?php echo $this->id; ?> <?php echo $this->options["classes"]; ?>">
				<h3><?php echo $this->title; ?></h3>
<?php
			if ($session) {
?>
				<p><?php $app("i18n")("logged_in_as", null, $app("users")->get($session->user)->name); ?></p>
				<a class="btn btn-primary" href="<?php echo \Config::WEBROOT; ?>/logout?ploc=<?php echo $app->request->slug; ?>"><?php $app("i18n")("log_out"); ?></a>
<?php
			} else {
?>
				<form method="post" action="<?php echo \Config::WEBROOT; ?>/login">
					<input type="hidden" name="ploc" value="<?php echo $app->request->slug; ?>" />
					<label for="user_id"><?php $app("i18n")("user_id_c"); ?></label>
					<input type="text" class="form-control" name="user_id" />
					<label for="user_password"><?php $app("i18n")("password_c"); ?></label>
					<input type="password" class="form-control" name="user_password" />
					<input type="submit" class="btn btn-primary mt-2" value="<?php $app("i18n")("log_in"); ?>" />
				</form>
<?php
			}
?>
			</div>
<?php
		}
	}
?>
