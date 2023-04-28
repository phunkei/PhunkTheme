<?php
namespace Phunkei\PhunkTheme\ThemeAddons;
use Phunkei\PhunkTheme\ThemeAddons\ThemeAddon;

class VideoAddon extends ThemeAddon {

	private $tpls;

	public function __construct($tpls = null) {
		if(is_array($tpls)) {
			$this->tpls = $tpls;
		}
		else {
			throw new \Exception('No Video templates set.');
		}
		add_shortcode('yt', [$this, 'addYoutubeShortCode']);
		add_shortcode('vimeo', [$this, 'addVimeoShortCode']);
		add_shortcode('video', [$this, 'addExternalVideoShortCode']);
	}

	public function draw() {
	}

	public function addExternalVideo($str) {
		if(filter_var($str, FILTER_VALIDATE_URL)) {
			return $this->addExternalVideoShortCode([], $str);
		}
		else {
			return $this->addYoutubeById($str);
		}
	}

	public function addExternalVideoShortCode($atts, $content) {
		if(isset($atts['id'])) {
			$id = $atts['id'];
			return $this->addYoutubeById($id);
		}
		if(filter_var($content, FILTER_VALIDATE_URL)) {
			$parsedURL = parse_url( $content, PHP_URL_HOST );
			if($parsedURL == "www.youtube.com") {
				$id = $this->getYoutubeIdByUrl($content);
				return $this->addYoutubeById($id);
			}
			else if($parsedURL == "youtu.be") {
				$id = $this->getYoutubeBeIdByUrl($content);
				return $this->addYoutubeById($id);
			}
			else if($parsedURL == "vimeo.com") {
				$id = $this->getVimeoIdByUrl($content);
				return $this->addVimeoById($id);
			}
		}
		return null;
	}

	public function addYoutubeShortCode($atts, $content) {
		if(isset($atts['id'])) {
			$id = $atts['id'];
			return $this->addYoutubeById($id);
		}
		else if (filter_var($content, FILTER_VALIDATE_URL)) {
			$id = $this->getYoutubeIdByUrl($content);
			if($id) {
				$this->addYoutubeById($id);
			}
		}
		return null;
	}

	public function addVimeoShortCode($atts, $content) {
		if(isset($atts['id'])) {
			$id = $atts['id'];
			return $this->addVimeoById($id);
		}
		else if (filter_var($content, FILTER_VALIDATE_URL)) {
			$id = $this->getVimeoIdByUrl($content);
			if($id) {
				$this->addVimeoById($id);
			}
		}
		return null;
	}

	public function getYoutubeIdByUrl($url) {
		$parsedYT = parse_url( $url, PHP_URL_QUERY );
		parse_str( $parsedYT, $videoVars );
		if(count($videoVars)) {
			if(!empty($videoVars['v'])) {
				return $videoVars['v'];
			}
		}
		return null;
	}

	public function getYoutubeBeIdByUrl($url) {
		$id = parse_url( $url, PHP_URL_PATH );
		$id = str_replace("/", "", $id);
		return $id;
	}

	public function getVimeoIdByUrl($url) {
		$parsedVimeo = parse_url( $url, PHP_URL_PATH );
		$parsedVimeo = preg_replace('/\\/([0-9a-z]+)\\/([0-9a-z]+)/i', '$1?h=$2', $parsedVimeo);
		if($parsedVimeo) {
			return $parsedVimeo;
		}
		return null;
	}

	public function addYoutubeById($id) {
		return $this->loadTemplate($this->tpls['yt'], ['id' => $id]);
	}

	public function addVimeoById($id) {
		return $this->loadTemplate($this->tpls['vimeo'], ['id' => $id]);
	}
}