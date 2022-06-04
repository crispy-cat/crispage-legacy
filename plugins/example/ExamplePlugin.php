<?php
	namespace Crispage\Plugins;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class ExamplePlugin extends \Crispage\Assets\Plugin {
		public function execute() {
			global $app;

			$app->vars["my_test_string"] = $this->options["test_string"];

			$app->events->registerAction(new \Crispage\Events\EventAction(array(
				"id" => "ExamplePlugin_EchoTest",
				"event" => "page.pre_render.metas",
				"priority" => 127,
				"action" => function($app) {
						echo "<!-- Example plugin loaded. Test string: " .
							$app->vars["my_test_string"] . " -->\n";
					}
			)));
		}
	}
