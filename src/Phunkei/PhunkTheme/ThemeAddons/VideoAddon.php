<?php
namespace Phunkei\PhunkTheme\ThemeAddons;
use Phunkei\PhunkTheme\ThemeAddons\ThemeAddon;

class VideoAddon extends ThemeAddon {

	private $tpl;

	public function __construct($tpl = null) {
		if($tpl) {
			$this->tpl = $tpl;
		}
		else {
			$tpl = 'template-parts/youtube.php';
		}
		add_shortcode('yt', [$this, 'addVideo']);
	}

	public function draw() {
		echo $this->loadTemplate($this->tpl);
	}

	public function addVideo($atts, $content) {
		if(isset($atts['id'])) {
			$id = $atts['id'];
			return $this->loadTemplate($this->tpl, ['id' => $id]);
		}
		return;
	}

	public function YTToNoCookie($url) {
		//https://www.youtube-nocookie.com/embed/SC52pr45ep8
		//https://www.youtube.com/watch?v=tsa8biyxGPQ
		return $url;
		return preg_replace('/https:\/\/www\.youtube\.com\/watch\?v=/', 'https://www.youtube-nocookie.com/embed/', $url);
	}

	public function getYTURLById($id) {
		return "https://www.youtube-nocookie.com/embed/$id";
	}
}