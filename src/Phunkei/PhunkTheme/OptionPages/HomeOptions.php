<?php
namespace Phunkei\PhunkTheme\OptionPages;

class HomeOptions {

	private $key = 'home_options';
	private $metabox_id = 'home_option_metabox';
	protected $title = '';
	protected $options_page = '';


	public function __construct() {
		$this->title = "Startseite";
		add_action( 'admin_init', [$this, 'init'] );
		add_action( 'admin_menu', [$this, 'add_options_page'] );
		add_action( 'cmb2_init', [$this, 'add_options_page_metabox'] );
	}

	public function init() {
		register_setting( $this->key, $this->key );
	}

	public function add_options_page() {
		$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, [$this, 'admin_page_display'], 'dashicons-admin-home', 2);
		add_action( "admin_print_styles-{$this->options_page}", ['CMB2_hookup', 'enqueue_cmb_css']);
	}

	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key, array( 'cmb_styles' => false ) ); ?>
		</div>
		<?php
	}

	function add_options_page_metabox() {
		$prefix = 'home_';

		$cmb = new_cmb2_box([
			'id'      => $this->metabox_id,
			'hookup'  => false,
			'show_on' => [
				'key'   => 'options-page',
				'value' => [$this->key]
			],
		]);

		$cmb->add_field([
			'id' => $prefix.'_media',
			'type' => 'file',
			'name' => 'Hero',
			'description' => 'Bild oder Video'
		]);

		$cmb->add_field([
			'id' => $prefix.'_content1',
			'type' => 'wysiwyg',
			'name' => 'Erster Textblock'
		]);

		$cmb->add_field([
			'id' => $prefix.'_content2',
			'type' => 'wysiwyg',
			'name' => 'Das sind wir'
		]);
	}

	public function getOptions() {
		return get_option('home_options');
	}
}