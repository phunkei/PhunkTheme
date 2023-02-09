<?php
namespace Phunkei\PhunkTheme\ThemeAddons;
use Phunkei\PhunkTheme\ThemeAddons\ThemeAddon;

class CaptchaAddon extends ThemeAddon {

	const CHARS = '23459abcdefghijkmnpqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWYYZ';
	const ACTION = 'getCaptcha';
	public $options;

	public function __construct($options = null) {
		$this->options = $this->getDefaults();
		if($options) {
			$this->setOptions($options);
		}

		add_action( 'wp_ajax_getCaptcha', [$this, 'ajaxGetCaptcha'] );
		add_action( 'wp_ajax_nopriv_getCaptcha', [$this, 'ajaxGetCaptcha'] );
	}

	public function setOptions($options) {
		if($options && is_array($options)) {
			foreach($options as $key => $option) {
				if(array_key_exists($key, $this->options)) {
					if($key == 'colorBackground' || $key == 'colorForeground') {
						if(is_string($option)) {
							$this->options[$key] = $this->parseColorFromString($option);
							continue;
						}
					}
					$this->options[$key] = $option;
				}
			}
		}
	}

	private function parseColorFromString($str) {
		if(is_array($str) && count($str) == 3) {
			return $str;
		}
		if($str[0] == '#') {
			$ret = sscanf($str, "#%02x%02x%02x");
			if(count($ret) == 3) {
				return $ret;
			}
		}
		if(preg_match('/^rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)$/', $str, $res)) {
			if(count($res) == 4) {
				return [$res[1], $res[2], $res[3]];
			}
		}
		throw new \Exception('Invalid color option.');
	}

	private function getDefaults() {
		return [
			'font' => null,
			'colorBackground' => [255, 255, 255],
			'colorForeground' => [0, 0, 0],
			'fontSize' => 16
		];
	}

	public function setFont($font) {
		$this->options['font'] = $font;
	}

	public function getCaptcha($identifier, $length = 8, $w = 128, $h = 32) {
		$code = $this->generateCaptchaCode($length);
		$this->persistCaptchaCode($identifier, $code);
		$img = $this->createCaptchaImage($code, $w, $h);
		return $img;
	}

	public function ajaxGetCaptcha() {
		$identifier = $_POST['identifier'];
		$length = $_POST['length'] ?? 8;
		$w = $_POST['w'] ?? 128;
		$h = $_POST['h'] ?? 32;

		$code = $this->generateCaptchaCode($length);
		$this->persistCaptchaCode($identifier, $code);
		$img = $this->createCaptchaImage($code, $w, $h);
		echo "data:image/png;base64,".$img;
		exit;
	}

	public function getAjaxPath() {
		return admin_url( 'admin-ajax.php' ) . '?action=' . self::ACTION;
	}

	public function createCaptchaImage($code, $w, $h) {
		$img = imagecreatetruecolor($w, $h);
		$bg = imagecolorallocate($img, $this->options['colorBackground'][0], $this->options['colorBackground'][1], $this->options['colorBackground'][2]);
		$fg = imagecolorallocate($img, $this->options['colorForeground'][0], $this->options['colorForeground'][1], $this->options['colorForeground'][2]);
		imagefill($img, 0, 0, $bg);
		$rotation = 0;
		$innerPadding = 16;
		for($i = 0; $i < strlen($code); $i++) {
			$x = ($w - $innerPadding) / strlen($code) * $i + $innerPadding / 2;
			$y = 0;
			$rotation = rand(-30, 30);
			
			//$trans = imagecolorallocate($imgTemp, 100, 100, 100);
			if(!$this->options['font']) {
				$imgTemp = imagecreatetruecolor($this->options['fontSize'], $this->options['fontSize']);
				imagefill($imgTemp, 0, 0, $bg);
				imagestring($imgTemp, 6, 0, 0, $code[$i], $fg);
				$imgTemp = imagerotate($imgTemp, $rotation, $bg);
				imagecopy($img, $imgTemp, $x, $y, 0, 0, $this->options['fontSize'], $this->options['fontSize']);
				imagedestroy($imgTemp);
			}
			else {
				imagefttext($img, $this->options['fontSize'], $rotation, $x, $this->options['fontSize'] + ($h - $this->options['fontSize']) / 2, $fg, $this->options['font'], $code[$i]);
			}
		}
		ob_start();
		imagepng($img);
		$stringData = ob_get_clean();
		//$zdata = gzdeflate($stringData);
		return base64_encode($stringData);
	}

	public function persistCaptchaCode($identifier, $code) {
		$_SESSION['captcha_'.$identifier] = $code;
	}

	public function generateCaptchaCode($length) {
		$str = "";
		$maxIndex = strlen(self::CHARS) - 1;
		for($i = 0; $i < $length; $i++) {
			$index = rand(0, $maxIndex);
			$str = $str . self::CHARS[$index];
		}
		return $str;
	}

	public function isValidCaptchaCode($identifier, $code) {
		if(!isset($_SESSION['captcha_'.$identifier])) {
			return false;
		}
		if($_SESSION['captcha_'.$identifier] != $code) {
			return false;
		}
		return true;
	}
}