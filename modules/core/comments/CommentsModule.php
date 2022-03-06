<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/comments/CommentsModule.php - Comments module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class CommentsModule extends Module {
		public function render() {
			global $app;

			if (!isset($app->request->route["view"]) || $app->request->route["view"] != "core/article") return;

			$article = $app->content->getArticle($app->request->route["item_id"]);
			if (@$article->options["show_comments"] != "yes") return;

			$comments = $app->comments->getComments($app->request->route["item_id"]);

			usort($comments, function($a, $b) {
				if ($a->created == $b->created) return 0;
				return ($a->created < $b->created) ? 1 : -1;
			});
?>
			<div class="module CommentsModule module-<?php echo $this->id . " " . $this->settings["classes"]; ?>">
				<hr />
				<h3><?php echo $this->title; ?></h3>
<?php
				$session = $app->session->getCurrentSession();
				if ($session && !$app->users->userHasPermissions($session->user, UserPermissions::VIEW_COMMENTS)) {
					echo "</div>";
					return;
				}
				if ($session && $app->users->userHasPermissions($session->user, UserPermissions::POST_COMMENTS)) {
?>
					<small>Posting as '<?php echo $app->users->getUser($session->user)->name; ?>'. HTML will be filtered.</small>
					<form method="post" action="<?php echo Config::WEBROOT; ?>/post_comment">
						<input type="hidden" name="ploc" value="<?php echo $app->request->slug; ?>" />
						<input type="hidden" name="article_id" value="<?php echo $article->id; ?>" />
						<label for="comment">Comment:</label>
						<textarea class="form-control" name="comment" required></textarea>
						<input type="submit" class="btn btn-primary mt-2" value="Post Comment" />
					</form>
					<hr />
<?php
				} else {
					echo "<p>You are not logged in or do not have permission to post comments.</p>";
				}

				for ($i = 0; $i < $this->settings["numcomments"]; $i++) {
					if (!isset($comments[$i])) break;
?>
					<div class="card mb-2">
						<div class="card-header">
							<span><?php echo $app->users->getUser($comments[$i]->author)->name; ?> on <?php echo date($app->getSetting("date_format", "Y-m-d") . " " . $app->getSetting("time_format", "H:i"), $comments[$i]->created); ?></span>
						</div>
						<div class="card-body">
							<p><?php echo htmlentities($comments[$i]->content); ?></p>
						</div>
					</div>
<?php
				}
?>
			</div>
<?php
		}
	}
?>
