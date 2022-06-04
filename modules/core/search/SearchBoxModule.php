<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/search/SearchBoxModule.php - Search box module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Modules;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class SearchBoxModule extends \Crispage\Assets\Module {
		public function render() {
			global $app;
?>
			<form class="module SearchBoxModule module-<?php echo $this->id . " " . $this->options["classes"]; ?>" action=" <?php echo \Config::WEBROOT . "/search"; ?>">
				<div class="input-group">
					<input class="form-control" type="search" name="q" placeholder="<?php $app("i18n")("search_ddd"); ?>" />
					<button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
				</div>
			</form>
<?php
		}
	}
?>
