<?php
	/*
		Crispage - A lightweight CMS for developers
		core/Page.php - Page class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Render;

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
		public array $options = array();

		public function getTitle() : string {
			if (isset($this->title)) return $this->title;
			else return null;
		}

		public function getBrowserTitle() : string {
			global $app;
			return $this->getTitle() . $app->getSetting("title_sep", " &mdash; ") . $app->getSetting("sitename", "");
		}

		public function setTitle($title) {
			$this->title = $title;
		}

		public function renderContent() : void {
			global $app;
			$app->events->trigger("page.pre_render.content");
			try {
				if (!isset($this->content)) throw new Exception("Page->content is null");
				if (is_string($this->content)) echo $this->content;
				elseif (is_callable($this->content)) ($this->content)($app);
				else throw new \Exception("Page->content not callable|string");
			} catch (\Throwable $e) {
				$app->error(new \Crispage\ApplicationException(500, $app("i18n")->getString("render_error"), $app("i18n")->getString("render_error_ex2"), null, $e, true));
			}
		}

		public function setContent($content) : void {
			$this->content = $content;
		}

		public function renderMetas() : void {
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

		public function renderLinks() : void {
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

		public function renderStyles() : void {
			global $app;
			$app->events->trigger("page.pre_render.styles");
			foreach ($this->styles as $style)
				echo "<style>{$style["content"]}</style>\n";
		}

		public function renderScripts() : void {
			global $app;
			$app->events->trigger("page.pre_render.scripts");
			foreach ($this->scripts as $script) {
				$s = "<script";
				$defer = (isset($script["defer"])) ? " defer" : "";
				if (isset($script["src"])) $s .= " src=\"{$script["src"]}\"$defer></script>\n";
				else $s .= "$defer>{$script["content"]}</script>\n";
				echo $s;
			}
		}

		public function renderAlerts() : void {
			global $app;
			$app->events->trigger("page.pre_render.alerts");
			foreach ($this->alerts as $alert)
				echo "<div class=\"alert alert-{$alert["class"]}\">{$alert["content"]}</div>\n";
		}

		public function loadModule(\Crispage\Assets\Module $module) : void {
			global $app;
			$app->events->trigger("page.modules.pre_load", $module);
			try {
				$classname = \Crispage\Helpers\ExtensionHelper::loadClass(\Config::APPROOT . "/modules/$module->class.php", "\\Crispage\\Modules\\");
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
			} catch (\Throwable $e) {
				$app->error(new \Crispage\ApplicationException(500, $app("i18n")->getString("module_error"), $app("i18n")->getString("module_error_ex", null, $module->id), null, $e, false));
			}
		}

		public function loadModules() : void {
			global $app;
			if (count($this->modules)) return;
			foreach ($app("modules")->getAll(array("scope" => "frontend")) as $module) {
				$this->loadModule($module);
				usort($this->modules[$module->pos], function($a, $b) {
					if ($a->ord == $b->ord) return 0;
					return ($a->ord < $b->ord) ? -1 : 1;
				});
			}
		}

		public function clearModules() : void {
			$this->modules = array();
		}

		public function countModules(string $pos) : int {
			if (!isset($this->modules[$pos])) return 0;
			return count($this->modules[$pos]);
		}

		public function renderModules(string $pos) : void {
			global $app;
			$app->events->trigger("page.modules.pre_render", $pos);
			if (!isset($this->modules[$pos])) return;
			foreach ($this->modules[$pos] as $module) {
				try {
					$module->render();
				} catch (\Throwable $e) {
					$app->error(new \Crispage\ApplicationException(500, $app("i18n")->getString("module_error"), $app("i18n")->getString("module_error_ex2", null, $module->id), null, $e, false));
				}
			}
		}

		public function setCookie(string $id, string $content, int $expires = 0, string $path = \Config::WEBROOT, string $domain = null) : bool {
			return setcookie($id, $content, $expires, $path, $domain);
		}

		public function deleteCookie(string $id) : void {
			setcookie($id, "_", time());
			unset($_COOKIE[$id]);
		}

		public function renderFooter() : void {
			global $app;
			$version = CRISPAGE;
			$lu = $app("i18n")->getString("software_licensed_under");
			$rendered = $app("i18n")->getString("rendered_in", null, microtime(true) - STARTTIME);
			$mem = $app("i18n")->getString("memory_used", null, floor(memory_get_peak_usage(false) / 1000000), ini_get("memory_limit"));
			echo "Crispage v$version &bull; $lu &bull; $rendered &bull; $mem";
		}
	}
?>
