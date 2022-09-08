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
		add_shortcode('yt', [$this, 'addVideo']);
	}

	public function draw() {
		echo $this->loadTemplate($this->tpl);
	}

	public function addVideo($atts, $content) {
		if(isset($atts['id'])) {
			$id = $atts['id'];
			return $this->loadTemplate($this->tpls['yt'], ['id' => $id]);
		}
		else if (filter_var($content, FILTER_VALIDATE_URL)) {
			$parsedURL = parse_url( $content, PHP_URL_HOST );
			if($parsedURL == "www.youtube.com") {
				$parsedYT = parse_url( $content, PHP_URL_QUERY );
				parse_str( $parsedYT, $videoVars );
				if(count($videoVars)) {
					if(!empty($videoVars['v'])) {
						return $this->loadTemplate($this->tpls['yt'], ['id' => $videoVars['v']]);
					}
				}
			}
			elseif($parsedURL == "vimeo.com") {
				$parsedVimeo = parse_url( $content, PHP_URL_PATH );
				$parsedVimeo = preg_replace('/\\/([0-9a-z]+)\\/([0-9a-z]+)/i', '$1?h=$2', $parsedVimeo);
				return $this->loadTemplate($this->tpls['vimeo'], ['id' => $parsedVimeo]);
			}
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