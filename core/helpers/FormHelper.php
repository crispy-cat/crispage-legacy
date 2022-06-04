<?php
	/*
		Crispage - A lightweight CMS for developers
		core/helpers/FormHelper.php - Form helper class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.10.2
	*/

	namespace Crispage\Helpers;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class FormHelper {
		public static function formFieldsFilled(...$keys) : bool {
			global $app;
			foreach ($keys as $key) {
				if (!isset($app->request->query[$key])) return false;
			}
			return true;
		}
	}
?>
