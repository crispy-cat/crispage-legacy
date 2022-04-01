<?php
	/*
		Crispage - A lightweight CMS for developers
		core/ApplicationException.php - Application Exception

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.7.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class ApplicationException extends Exception {
		protected int $httpStatus;
		protected string $pageTitle;
		protected bool $loadPlugins;
		
		public function __construct(int $http, string $title, string $message, ?int $code = 0, Throwable $previous = null, bool $lp = true) {
			parent::__construct($message, $code, $previous);
			$this->httpStatus = $http;
			$this->pageTitle = $title;
			$this->loadPlugins = $lp;
		}
		
		public final function getHttpStatus() : int {
			return $this->httpStatus;
		}
		
		public final function getPageTitle() : string {
			return $this->pageTitle;
		}
		
		public final function loadPlugins() : bool {
			return $this->loadPlugins;
		}
	}
?>