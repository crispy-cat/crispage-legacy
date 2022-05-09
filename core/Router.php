<?php
	/*
		Crispage - A lightweight CMS for developers
		core/Router.php - Router class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Router {
		public static function getSlug(string $basepath = "", string $default = "index") : string {
			$slug = substr($_SERVER["REQUEST_URI"], strlen(Config::WEBROOT . $basepath) + 1);
			$slug = explode("?", $slug)[0] ?? "";
			if (substr($slug, -1) == "/") $slug = substr($slug, 0, -1);
			if (!strlen($slug)) return $default;
			return $slug;
		}

		public static function routeRequest(string $basepath = "", string $default = "index") {
			global $app;

			$app->events->trigger("router.route");

			$slug = Router::getSlug($basepath, $default);
			if ($basepath != "") {
				$app->request(new Request(array(
					"route" => null,
					"slug" => $slug
				)));
			} else {
				$route = $app->database->readRow("routes", $slug);
				if ($route && $route["view"]) {
					$app->request(new Request(array(
						"route" => $route,
						"slug" => $slug
					)));
				} else {
					$app->loadLanguages();
					$app->error(new ApplicationException(404, $app("i18n")->getString("page_not_found"), $app("i18n")->getString("page_not_found_ex")));
				}
			}
		}

		public static function getCategoryRoute(?string $route) : ?string {
			global $app;
			$cat = $app("categories")->get($route);
			if (!$cat) return null;
			if ($cat->parent) $route = Router::getCategoryRoute($cat->parent) . "/" . $route;
			return $route;
		}

		public static function getArticleRoute(?string $route) : ?string {
			global $app;
			$art = $app("articles")->get($route);
			if (!$art) return null;
			if ($art->category) $route = Router::getCategoryRoute($art->category) . "/" . $route;
			return $route;
		}
	}
?>
