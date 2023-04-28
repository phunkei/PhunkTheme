<?php
use Symfony\Component\Yaml\Yaml;
use Phunkei\PhunkTheme\ThemeAddons\ThemeAddon;

class CustomPostTypeGenerator extends ThemeAddon {

    private $yaml;

    public function __construct($yaml_file_path) {
        $this->yaml = Yaml::parseFile($yaml_file_path);
        add_action('init', [$this, 'generateCustomPostTypes']);
        add_action('add_meta_boxes', [$this, 'addMeta']);
    }

    public function generateCustomPostTypes() {
        foreach ($this->yaml as $post_type_name => $post_type_args) {
            register_post_type($post_type_name, $post_type_args);
        }
    }

    public function addMeta() {
        foreach ($this->yaml as $post_type_name => $post_type_args) {
            if (isset($post_type_args['meta_fields'])) {
                $meta_fields = $post_type_args['meta_fields'];
                if (isset($meta_fields)) {
                    $this->addCustomMetaFields($post_type_name, $meta_fields);
                }
            }
        }
    }

    private function addCustomMetaFields($post_type_name, $meta_fields) {
        foreach ($meta_fields as $meta_field_name => $meta_field_args) {
            add_post_meta($post_type_name, $meta_field_name, $meta_field_args['default_value'], true);
            add_meta_box(
                $meta_field_name,
                $meta_field_args['label'],
                function($post) use ($meta_field_name, $meta_field_args) {
                    $value = get_post_meta($post->ID, $meta_field_name, true);
                    echo '<label for="' . $meta_field_name . '">' . $meta_field_args['label'] . '</label>';
                    echo '<input type="' . $meta_field_args['type'] . '" id="' . $meta_field_name . '" name="' . $meta_field_name . '" value="' . $value . '">';
                },
                $post_type_name,
                'normal',
                'default'
            );
        }
    }
}
