<?php
	/*
		Crispage - A lightweight CMS for developers
		header.php - Standard header

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	Session::refreshCurrentSession();

	$app->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
	$app->page->metas["robots"] = array("name" => "robots", "content" => $this->getSetting("meta_robots", "index, follow"));

	$app->page->scripts["getcookie"] = array("content" => "function getCookie(name) {var a=document.cookie.split(\";\");for(var i=0;i<a.length;i++){var p=a[i].split(\"=\");if(name==p[0].trim())return decodeURIComponent(p[1]);}return null;}function setCookie(name, value, exp) {var d=new Date();d.setTime(d.getTime()+(exp*86400000));var e=\"expires=\"+d.toUTCString();document.cookie=name+\"=\"+value+\"; \"+e+\"; path=/\";}");

	if (isset($app->request->cookies["msg_success"]))	$app->page->alerts["success"] =	array("class" => "success", "content" => $app->request->cookies["msg_success"]);
	if (isset($app->request->cookies["msg_info"]))		$app->page->alerts["info"] =	array("class" => "info", "content" => $app->request->cookies["msg_info"]);
	if (isset($app->request->cookies["msg_warning"]))	$app->page->alerts["warning"] =	array("class" => "warning", "content" => $app->request->cookies["msg_warning"]);
	if (isset($app->request->cookies["msg_error"]))		$app->page->alerts["error"] =	array("class" => "danger", "content" => $app->request->cookies["msg_error"]);

	$app->page->deleteCookie("msg_success");
	$app->page->deleteCookie("msg_info");
	$app->page->deleteCookie("msg_warning");
	$app->page->deleteCookie("msg_error");
?>
