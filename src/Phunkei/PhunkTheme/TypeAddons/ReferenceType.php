<?php
namespace Phunkei\PhunkTheme\TypeAddons;
use Phunkei\PhunkTheme\TypeAddons\TypeAddon;

class ReferenceType extends TypeAddon {
	function registerType() {
		register_post_type( 'reference', [
			'labels' => array(
				'name'               => 'Referenz',
				'singular_name'      => 'Referenzen',
				'add_new'            => 'Neue Referenz',
				'add_new_item'       => 'Neue Referenz',
				'edit_item'          => 'Referenz bearbeiten',
				'new_item'           => 'Neue Referenz',
				'all_items'          => 'Alle Referenzen',
				'view_item'          => 'Referenz anzeigen',
				'search_items'       => 'Referenz suchen',
				'not_found'          => 'Referenz nicht gefunden',
				'not_found_in_trash' => 'Keine Referenz im Papierkorb',
				'parent_item_colon'  => '',
				'menu_name'          => 'Referenzen'
			),
			'taxonomies' => [],
			'public' => true,
			'hierarchical' => false,
			'has_archive' => true,
			'rewrite' => [
				'slug' => 'portfolio',
				'with_front' => true
			],
			'query_var' => true,
			//'show_in_rest' => true,
			'supports' => ['title', 'editor', 'thumbnail']
		]);
		add_theme_support( 'post-thumbnails', ['reference'] );
	}
}