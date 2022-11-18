<?php
namespace Phunkei\PhunkTheme\TypeAddons;

class TypeAddon {
	public function __construct() {
		$this->registerType();
		if( is_callable(self::class, 'registerMeta') ) {
			call_user_func([$this, 'registerMeta']);
		}
	}

	function registerType() {}
}
