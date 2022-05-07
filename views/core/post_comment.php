<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/post_comment.php - Frontend comment poster

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/header.php";

	$ploc = preg_replace("/\/\//", "/", "/" . ($app->request->query["ploc"] ?? "/"));

	if (!isset($app->request->query["article_id"]))
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => $app("i18n")->getString("no_article_id")));

	$app->events->trigger("frontend.view.post_comment.submit");

	$session = Session::getCurrentSession();
	if (!$session)
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => $app("i18n")->getString("login_post_comments")));

	$user = $app("users")->get($session->user);
	if (!User::userHasPermissions($user->id, UserPermissions::POST_COMMENTS))
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => $app("i18n")->getString("no_permission_comments")));

	$content = $app->request->query["comment"] ?? "";

	if (!strlen($content))
		$app->redirectWithMessages($ploc, array("type" => "warning", "content" => $app("i18n")->getString("post_is_empty")));

	$id = Randomizer::randomString(16, 62);
	$comment = new Comment(array(
		"id" => $id,
		"article" => $app->request->query["article_id"],
		"created" => time(),
		"modified" => time(),
		"author" => $user->id,
		"content" => $content
	));

	$app("comments")->set($id, $comment);

	$app->events->trigger("comments.post_comment", $id);

	$app->redirectWithMessages($ploc, array("type" => "success", "content" => $app("i18n")->getString("comment_posted")));
?>
