<?php
	/*
		Crispage - A lightweight CMS for developers
		core/i18n/I18n.php - Internationalization class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.10.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class I18n {
		private array $langs = array();
		private string $currentLang = "en-US";

		public function __invoke(string $string, string $lang = null, ...$vars) : void {
			echo $this->getString($string, $lang, ...$vars);
		}

		public function getLanguage() : string {
			return $this->currentLang;
		}

		public function setLanguage(string $lang) : void {
			global $app;
			if (preg_match("/([a-z]{2})[-_]([a-z]{2})/i", $lang, $match)) {
				$this->currentLang = strtolower($match[1]) . "-" . strtoupper($match[2]);
				$app->events->trigger("language.set", $this->currentLang);
			}
		}

		public function getLoadedLanguages() : array {
			return array_keys($this->langs);
		}

		public function loadLanguageFile(string $path) : void {
			if (!file_exists($path)) return;
			$langs = parse_ini_file($path, true);
			if (!$langs) return;
			foreach ($langs as $code => $lang) {
				if (!isset($this->langs[$code])) $this->langs[$code] = array();
				foreach ($lang as $string => $translated)
					$this->langs[$code][$string] = $translated;
			}
		}

		public function getString(string $string, string $lang = null, ...$vars) : string {
			if (!$lang) $lang = $this->currentLang;
			if (isset($this->langs[$lang])) {
				$tstrings = array();
				foreach (explode(" ", $string) as $part) {
					if (isset($this->langs[$lang][$string]))
						$tstrings[] = $this->langs[$lang][$string];
					else
						$tstrings[] = "{" . $lang . ":" . $part . "[" . implode(", ", $vars) . "]}";
				}
				$tstring = stripcslashes(implode(" ", $tstrings));
				if ($vars) return sprintf($tstring, ...$vars);
				else return $tstring;
			}
			return "{" . $lang . ":'" . $string . "'[" . implode(", ", $vars) . "]}";
		}
	}
?>
