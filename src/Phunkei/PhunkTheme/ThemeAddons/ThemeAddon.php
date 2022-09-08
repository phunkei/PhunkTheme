<?php
namespace Phunkei\PhunkTheme\ThemeAddons;

class ThemeAddon {
	public function __construct() {
		$this->register();
	}

	public function register() {}
	public function draw() {}
	public function loadTemplate($path, $args = null) {
		$path = explode("/", $path);
		$path = implode(DIRECTORY_SEPARATOR, $path);
		ob_start();
		load_template(get_template_directory().DIRECTORY_SEPARATOR.$path, false, $args);
		return ob_get_clean();
	}
}