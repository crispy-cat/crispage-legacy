<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/search.php - Frontend password search page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.5
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/core/header.php";

	$query = $app->request->query["q"] ?? "";
	$app->vars["results"] = array();

	if (strlen($query)) {
		$keywords = explode(" ", preg_replace("/[^0-9a-z]/", " ", strtolower($query)));

		foreach ($app->content->getArticles() as $article) {
			$nkeys = 0;
			foreach ($keywords as $key) {
				$nkeys += preg_match_all("/$key/", strtolower($article->title));
				$nkeys += preg_match_all("/$key/", strtolower($article->content));
				$nkeys += preg_match_all("/$key/", strtolower($article->summary));
				$nkeys += preg_match_all("/$key/", strtolower($article->tags));
				$nkeys += preg_match_all("/$key/", strtolower($article->meta_keys));
			}

			if (!$nkeys) continue;
			$app->vars["results"][] = array(
				"nkeys" => $nkeys,
				"type" => "Article",
				"route" => Router::getArticleRoute($article->id),
				"title" => htmlentities($article->title),
				"body" => htmlentities($article->summary)
			);
		}

		foreach ($app->content->getCategories() as $category) {
			$nkeys = 0;
			foreach ($keywords as $key) {
				$nkeys += preg_match_all("/$key/", strtolower($category->title));
				$nkeys += preg_match_all("/$key/", strtolower($category->content));
				$nkeys += preg_match_all("/$key/", strtolower($category->tags));
				$nkeys += preg_match_all("/$key/", strtolower($category->meta_keys));
			}

			if (!$nkeys) continue;
			$app->vars["results"][] = array(
				"nkeys" => $nkeys,
				"type" => "Category",
				"route" => Router::getArticleRoute($category->id),
				"title" => htmlentities($category->title),
				"body" => htmlentities($category->content)
			);
		}

		usort($app->vars["results"], function($a, $b) {
			if ($a["nkeys"] == $b["nkeys"]) return 0;
			return ($a["nkeys"] < $b["nkeys"]) ? -1 : 1;
		});
	}

	if (strlen($query)) $app->page->setTitle("Search results for '$query'");
	else $app->page->setTitle("Search");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<form class="d-flex">
				<input type="text" class="form-control" name="q" placeholder="Enter search term..." required />
				<button type="submit" class="btn btn-primary ms-1">Search</button>
			</form>

			<hr />
<?php
			foreach ($app->vars["results"] as $result) {
?>
				<div class="card mt-2">
					<div class="card-body">
						<a href="<?php echo Config::WEBROOT . "/" . $result["route"]; ?>">
							<h3 class="mb-0"><?php echo $result["title"]; ?></h3>
						</a>
						<small><?php echo $result["type"]; ?></small><br />
						<span><?php echo $result["body"]; ?></span>
					</div>
				</div>
<?php
			}

			if (!count($app->vars["results"])) echo "<b>No items match your search.</b>";
?>
		</div>
<?php
	});

	$app->renderPage();
?>
