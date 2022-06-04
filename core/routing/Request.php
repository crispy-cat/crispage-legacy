<?php
	/*
		Crispage - A lightweight CMS for developers
		core/Request.php - Request class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Routing;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Request {
		public ?array $route;
		public string $slug;
		public array $query;
		public array $files;
		public array $cookies;

		public function __construct(array $data) {
			$this->route = $data["route"];
			$this->slug = $data["slug"];
			$this->query = ($_SERVER["REQUEST_METHOD"] == "POST") ? $_POST : $_GET;
			$this->files = $_FILES;
			$this->cookies = $_COOKIE;
		}
	}
?>
