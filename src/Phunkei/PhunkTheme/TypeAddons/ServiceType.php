<?php
namespace Phunkei\PhunkTheme\TypeAddons;
use Phunkei\PhunkTheme\TypeAddons\TypeAddon;

class ServiceType extends TypeAddon {
	function registerType() {
		register_post_type( 'service', [
			'labels' => array(
				'name'               => 'Leistung',
				'singular_name'      => 'Leistungen',
				'add_new'            => 'Neue Leistung',
				'add_new_item'       => 'Neue Leistung',
				'edit_item'          => 'Leistung bearbeiten',
				'new_item'           => 'Neue Leistung',
				'all_items'          => 'Alle Leistungen',
				'view_item'          => 'Leistung anzeigen',
				'search_items'       => 'Leistung suchen',
				'not_found'          => 'Leistung nicht gefunden',
				'not_found_in_trash' => 'Keine Leistung im Papierkorb',
				'parent_item_colon'  => '',
				'menu_name'          => 'Leistungen'
			),
			'taxonomies' => [],
			'public' => true,
			'hierarchical' => false,
			'has_archive' => true,
			'rewrite' => [
				'slug' => 'leistungen',
				'with_front' => true
			],
			'query_var' => true,
			//'show_in_rest' => true,
			'supports' => ['title', 'editor', 'thumbnail']
		]);
		add_theme_support( 'post-thumbnails', ['service'] );
	}
}