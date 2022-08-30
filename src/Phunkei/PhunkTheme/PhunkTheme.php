<?php
namespace Phunkei\PhunkTheme;
use JasonGrimes\Paginator;
class PhunkTheme {

	protected $vars;

	protected $themeAddons;
	protected $options;

	public function __construct() {
		$this->registerThemeAddons();
	}

	public function init() {
		remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

		$actions = [
			[
				'event' => 'init',
				'callable' => 'modifyPostType'
			],
			[
				'event' => 'init',
				'callable' => 'registerMenus'
			],
			[
				'event' => 'init',
				'callable' => 'registerSidebars'
			],
			[
				'event' => 'cmb2_init',
				'callable' => 'registerTypeAddons'
			],
			[
				'event' => 'cmb2_init',
				'callable' => 'registerMetaAddons'
			],
			[
				'event' => 'init',
				'callable' => 'registerOptionPages'
			],
			[
				'event' => 'wp_enqueue_scripts',
				'callable' => 'enqueueScripts',
				'priority' => 99
			],
		];

		$this->addActions($actions);
		
		add_theme_support('html5', [
			'comment-list', 
			'comment-form',
			'search-form',
			'gallery',
			'caption',
		]);
		add_theme_support('menus');
		add_theme_support( 'post-thumbnails', ['post', 'page']);
		add_filter( 'use_block_editor_for_post', '__return_false');
		add_filter( 'use_widgets_block_editor', '__return_false' );
		add_filter( 'use_default_gallery_style', '__return_false' );
	}

	public function addShortCode($code, $action) {
		add_shortcode($code, $action);
	}

	public function addActions($actions) {
		foreach($actions as $action) {
			if(method_exists($this, $action['callable'])) {
				add_action( $action['event'], [$this, $action['callable']], $action['priority'] ?? 10);	
			}
		}
	}

	public function loadVariables() {
		$src = [];
		$parentThemeIni = get_template_directory() . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'theme.ini';
		if(file_exists($parentThemeIni)) {
			$src[] = $parentThemeIni;
		}
		if(get_stylesheet_directory() != get_template_directory()) {
			$childThemeIni = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'theme.ini';
			if(file_exists($childThemeIni)) {
				$src[] = $childThemeIni;
			}
		}
		$this->loadVariablesFromIni($src);
	}

	public function loadVariablesFromIni($src) {
		$files = [];
		if(!is_array($src)) {
			$files[] = $src;
		}
		else {
			$files = $src;
		}
		foreach($files as $file) {
			if(!is_file($file)) {
				throw new \Exception("File not found in $file");
			}
			$vars = parse_ini_file($file);
			foreach($vars as $key => $value) {
				$this->vars[$key] = $value;
			}
		}
	}

	public function getVar($key) {
		return !empty($this->vars[$key]) ? $this->vars[$key] : null;
	}

	public function options($key) {
		if(isset($this->options[$key])) {
			return $this->options[$key];
		}
		throw new \Exception("Option $key not found.");
	}

	public function addons($key) {
		if(isset($this->themeAddons[$key])) {
			return $this->themeAddons[$key];
		}
		throw new \Exception("ThemeAddon $key not found.");
	}

	public function registerThemeAddons() {
	}

	public function registerMenus() {}

	public function registerSidebars() {}

	public function getVersion($dir, $filename) {
		$v = "";
		$filepath = get_template_directory();
		if($dir) {
			$filepath .= DIRECTORY_SEPARATOR . $dir;
		}
		$filepath .= DIRECTORY_SEPARATOR . $filename;
		if( file_exists($filepath) ) {
			$v = file_get_contents($filepath);
		}
		return $v;
	}

	public function enqueueScripts() {}

	public function showTemplate() {
		global $template;
		return str_replace('.php', '', basename($template));
	}

	public function registerTypeAddons() {}
	public function registerMetaAddons() {}
	public function registerOptionPages() {}
	public function registerCustomPostTypes() {}
	public function registerCategoryMeta() {}

	public function getPagination($archive_link, $filters = [], $max_num_pages = null) {
		global $wp_query;
		$itemsPerPage = get_option('posts_per_page');
		$totalItems = $max_num_pages * $itemsPerPage ?: $wp_query->max_num_pages * $itemsPerPage;
		$currentPage = get_query_var( 'paged' );
		$urlPattern = $archive_link.'page/(:num)/';
	
		if(count($filters)) {
			$urlPattern .= '?'.http_build_query($filters);
		}
	
		$paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
		if(!is_page_template( 'partials/pagination.php' )) {
			throw new \Exception('Pagination Template "partials/pagination.php" does not exist.');
		}
		get_template_part('template-parts/pagination', null, ['paginator' => $paginator]);
	}

	public function getCategory() {
		global $post;
		$category = get_the_category();
		return $category[0]->cat_name;
	}

	public function getMainClass() {
		if( is_front_page() ) {
			return "home";
		}
		elseif( is_archive() ) {
			$post_type = get_query_var( 'post_type', 'default' );
			return "archive archive-".$post_type;
		}
		global $post;
		if($post->post_type == "page") {
			$tpl = basename(get_page_template(), '.php');
			return 'page '.$tpl;
		}
		return $post->post_type;
	}

	public function getDynamicSidebar($id) {
		ob_start();
		dynamic_sidebar($id);
		return ob_get_clean();
	}
	

	public function disableComments() {
		add_action('admin_init', function () {
			// Redirect any user trying to access comments page
			global $pagenow;
			
			if ($pagenow === 'edit-comments.php') {
				wp_redirect(admin_url());
				exit;
			}
		
			// Remove comments metabox from dashboard
			remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
		
			// Disable support for comments and trackbacks in post types
			foreach (get_post_types() as $post_type) {
				if (post_type_supports($post_type, 'comments')) {
					remove_post_type_support($post_type, 'comments');
					remove_post_type_support($post_type, 'trackbacks');
				}
			}
		});
		
		// Close comments on the front-end
		add_filter('comments_open', '__return_false', 20, 2);
		add_filter('pings_open', '__return_false', 20, 2);
		
		// Hide existing comments
		add_filter('comments_array', '__return_empty_array', 10, 2);
		
		// Remove comments page in menu
		add_action('admin_menu', function () {
			remove_menu_page('edit-comments.php');
		});
		
		// Remove comments links from admin bar
		add_action('init', function () {
			if (is_admin_bar_showing()) {
				remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
			}
		});
	}
}