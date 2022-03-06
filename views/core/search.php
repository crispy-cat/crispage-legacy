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
				$nkeys += preg_match_all("/$key/", $article->title);
				$nkeys += preg_match_all("/$key/", $article->content);
				$nkeys += preg_match_all("/$key/", $article->summary);
				$nkeys += preg_match_all("/$key/", $article->tags);
				$nkeys += preg_match_all("/$key/", $article->meta_keys);
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
				$nkeys += preg_match_all("/$key/", $category->title);
				$nkeys += preg_match_all("/$key/", $category->content);
				$nkeys += preg_match_all("/$key/", $category->tags);
				$nkeys += preg_match_all("/$key/", $category->meta_keys);
			}

			if (!$nkeys) continue;
			$app->vars["results"][] = array(
				"nkeys" => $nkeys,
				"type" => "Article",
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

	$app->page->setTitle("Search results for '$query'");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<form class="d-flex">
				<input type="text" class="form-control" name="q" placeholder="Enter search term..." required />
				<button type="submit" class="btn btn-primary ms-1">Search</button>
			</form>
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
?>
			<hr />
		</div>
<?php
	});

	$app->renderPage();
?>
