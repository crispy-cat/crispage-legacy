<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/content/PopularArticlesModule.php - Popular articles module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class PopularArticlesModule extends Module {
		public function render() {
			global $app;

			if ($this->options["filter"]) $articles = $app->content->getArticles($this->options["filtercat"]);
			else $articles = $app->content->getArticles();

			foreach ($articles as $key => $article)
				if ($article->state != "published")
					array_splice($articles, $key, 1);

			usort($articles, function($a, $b) {
				if ($a->hits == $b->hits) return 0;
				return ($a->hits < $b->hits) ? 1 : -1;
			});

			echo "<ul class=\"module PopularArticlesModule module-$this->id {$this->options["classes"]}\">\n";
			echo "<h3>$this->title</h3>";

			for ($i = 0; $i < $this->options["numarticles"]; $i++) {
				if (!isset($articles[$i])) break;
				echo "<li class=\"nav-item\">";
				echo "<a class=\"nav-link\" href=\"" . Config::WEBROOT . "/" . Router::getArticleRoute($articles[$i]->id) . "\">{$articles[$i]->title}</a>";
				echo "</li>\n";
			}

			echo "</ul>\n";
		}
	}
?>
