<?php
	/*
		Crispage - A lightweight CMS for developers
		core/Page.php - Page class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Page {
		private string $title;
		private $content;
		private array $modules = array();
		public array $metas = array();
		public array $links = array();
		public array $styles = array();
		public array $scripts = array();
		public array $alerts = array();

		public function getTitle() : string {
			if (isset($this->title)) return $this->title;
			else return null;
		}

		public function getBrowserTitle() : string{
			global $app;
			return $this->getTitle() . $app->getSetting("title_sep", " &mdash; ") . $app->getSetting("sitename", "");
		}

		public function setTitle($title) {
			$this->title = $title;
		}

		public function renderContent() {
			global $app;
			$app->events->trigger("page.pre_render");
			try {
				if (!isset($this->content)) throw new Exception("Page has no content associated with it");
				if (is_string($this->content)) echo $this->content;
				elseif (is_callable($this->content)) ($this->content)($app);
				else throw new Exception("Page is misconfigured! (Not string or callable)");
			} catch (Throwable $e) {
				$app->error(500, "An error occurred", "This page could not be rendered by the server. Please try again later.", $e);
			}
		}

		public function setContent($content) {
			$this->content = $content;
		}

		public function renderMetas() {
			global $app;
			$app->events->trigger("page.pre_render.metas");
			foreach ($this->metas as $meta) {
				$s = "<meta";
				foreach ($meta as $key => $val)
					$s .= " $key=\"$val\"";
				$s .= ">\n";
				echo $s;
			}
		}

		public function renderLinks() {
			global $app;
			$app->events->trigger("page.pre_render.links");
			foreach ($this->links as $link) {
				$s = "<link";
				foreach ($link as $key => $val)
					$s .= " $key=\"$val\"";
				$s .= ">\n";
				echo $s;
			}
		}

		public function renderStyles() {
			global $app;
			$app->events->trigger("page.pre_render.styles");
			foreach ($this->styles as $style)
				echo "<style>{$style["content"]}</style>\n";
		}

		public function renderScripts() {
			global $app;
			$app->events->trigger("page.pre_render.scripts");
			foreach ($this->scripts as $script) {
				$s = "<script";
				$defer = (isset($script["defer"])) ? " defer" : "";
				if (isset($script["src"])) $s .= " src=\"{$script["src"]}\"></script>\n";
				else $s .= "$defer>{$script["content"]}</script>\n";
				echo $s;
			}
		}

		public function renderAlerts() {
			global $app;
			$app->events->trigger("page.pre_render.alerts");
			foreach ($this->alerts as $alert)
				echo "<div class=\"alert alert-{$alert["class"]}\">{$alert["content"]}</div>\n";
		}

		public function loadModule(Module $module) {
			global $app;
			$app->events->trigger("page.modules.pre_load", $module);
			try {
				if (!file_exists(Config::APPROOT . "/modules/$module->class.php")) return;
				include_once Config::APPROOT . "/modules/$module->class.php";
				@$classname = array_pop(explode("/", $module->class));
				if (!class_exists($classname)) return;
				if (!isset($this->modules[$module->pos])) $this->modules[$module->pos] = array();
				$this->modules[$module->pos][] = new $classname(array(
					"id"	=> $module->id,
					"title" => $module->title,
					"class"	=> $module->class,
					"pos"	=> $module->pos,
					"ord"	=> $module->ord,
					"created" => $module->created,
					"modified" => $module->modified,
					"options" => $module->options
				));
				$app->events->trigger("page.modules.post_load", $module);
			} catch (Throwable $e) {
				$app->error(500, "An error occurred", "Module <code>$module->id</code> could not be loaded: ", $e, false);
			}
		}

		public function loadModules() {
			global $app;
			if (count($this->modules)) return;
			foreach ($app->modules->getModules() as $module)
				$this->loadModule($module);
			usort($this->modules[$module->pos], function($a, $b) {
				if ($a->ord == $b->ord) return 0;
				return ($a->ord < $b->ord) ? -1 : 1;
			});
		}

		public function countModules(string $pos) : int {
			if (!isset($this->modules[$pos])) return 0;
			return count($this->modules[$pos]);
		}

		public function renderModules(string $pos) {
			global $app;
			$app->events->trigger("page.modules.pre_render");
			if (!isset($this->modules[$pos])) return;
			foreach ($this->modules[$pos] as $module)
				$module->render();
		}
	}
?>
