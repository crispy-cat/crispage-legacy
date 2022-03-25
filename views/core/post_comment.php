<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/post_comment.php - Frontend comment poster

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/core/header.php";

	$ploc = preg_replace("/\/\//", "/", "/" . ($app->request->query["ploc"] ?? "/"));

	if (!isset($app->request->query["article_id"]))
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => "Article ID not specified"));

	$session = $app->session->getCurrentSession();
	if (!$session)
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => "You must be logged in to post comments"));

	$user = $app->users->getUser($session->user);
	if (!$app->users->userHasPermissions($user->id, UserPermissions::POST_COMMENTS))
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => "You do not have permission to post comments"));

	$content = $app->request->query["comment"] ?? "";

	if (!strlen($content))
		$app->redirectWithMessages($ploc, array("type" => "warning", "content" => "Post is empty"));

	$id = Randomizer::randomString(16, 62);
	$comment = new Comment(array(
		"id" => $id,
		"article" => $app->request->query["article_id"],
		"created" => time(),
		"modified" => time(),
		"author" => $user->id,
		"content" => $content
	));

	$app->comments->setComment($id, $comment);

	$app->events->trigger("comments.post_comment", $id);

	$app->redirectWithMessages($ploc, array("type" => "success", "content" => "Comment posted."));
?>
