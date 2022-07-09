<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/content/LatestArticlesModule.php - Latest articles module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Modules;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class LatestArticlesModule extends \Crispage\Assets\Module {
		public function render() {
			global $app;

			if ($this->options["filter"]) $articles = $app("articles")->getAllArr(array("category" => $this->options["filtercat"]));
			else $articles = $app("articles")->getAllArr();

			foreach ($articles as $key => $article)
				if ($article->state != "published")
					array_splice($articles, $key, 1);

			usort($articles, function($a, $b) {
				return ($a->modified - $b->modified) <=> 0;
			});

			echo "<ul class=\"module LatestArticlesModule module-$this->id {$this->options["classes"]}\">\n";
			echo "<h3>$this->title</h3>";

			for ($i = 0; $i < $this->options["numarticles"]; $i++) {
				if (!isset($articles[$i])) break;
				echo "<li class=\"nav-item\">";
				echo "<a class=\"nav-link\" href=\"" . \Config::WEBROOT . "/" . \Crispage\Routing\Router::getArticleRoute($articles[$i]->id) . "\">{$articles[$i]->title}</a>";
				echo "</li>\n";
			}

			echo "</ul>\n";
		}
	}
?>
