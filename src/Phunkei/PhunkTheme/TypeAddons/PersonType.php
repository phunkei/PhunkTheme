<?php
namespace Phunkei\PhunkTheme\TypeAddons;
use Phunkei\PhunkTheme\TypeAddons\TypeAddon;

class PersonType extends TypeAddon {
	function registerType() {
		register_post_type( 'person', [
			'labels' => array(
				'name'               => 'Person',
				'singular_name'      => 'Personen',
				'add_new'            => 'Neue Person',
				'add_new_item'       => 'Neue Person',
				'edit_item'          => 'Person bearbeiten',
				'new_item'           => 'Neue Person',
				'all_items'          => 'Alle Personen',
				'view_item'          => 'Person anzeigen',
				'search_items'       => 'Person suchen',
				'not_found'          => 'Person nicht gefunden',
				'not_found_in_trash' => 'Keine Person im Papierkorb',
				'parent_item_colon'  => '',
				'menu_name'          => 'Team'
			),
			'taxonomies' => [],
			'public' => true,
			'hierarchical' => false,
			'has_archive' => true,
			'rewrite' => [
				'slug' => 'persons',
				'with_front' => false
			],
			'query_var' => true,
			//'show_in_rest' => true,
			'supports' => ['title', 'editor', 'thumbnail']
		]);
		add_theme_support( 'post-thumbnails', ['person'] );
	}
}