<?php
// ------------------------------------------
// ACF (Advanced Custom Fields) functionality.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_ACF')):

class Hawp_Theme_ACF {

	/**
	 * Constructor.
	 */
	public function setup() {
		add_action('acf/input/admin_footer', [$this, 'add_acf_color_palette']);
		add_action('acf/input/admin_footer', [$this, 'add_theme_colors_to_acf_color_picker']);
		add_filter('acf/settings/save_json', [$this, 'acf_json_save_point']);
		add_filter('acf/settings/load_json', [$this, 'acf_json_load_point']);
	}

	/**
	 * Get the editor color palette.
	 */
	public function get_editor_color_palette() {
		$color_palette = current((array) get_theme_support('editor-color-palette'));

		if (!$color_palette) {
			return '';
		}

		$colors = array_map(function($color) {
			return "'" . $color['color'] . "'";
		}, $color_palette);

		return '[' . implode(', ', $colors) . ']';
	}

	/**
	 * Add the editor color palette to acf color field.
	 */
	public function add_acf_color_palette() {
		$color_palette = $this->get_editor_color_palette();
		if (!$color_palette) {
			return;
		}

		printf(
			'<script type="text/javascript">
				(function($) {
					acf.add_filter("color_picker_args", function(args, $field) {
						args.palettes = %s;
						return args;
					});
				})(jQuery);
			</script>',
			$color_palette
		);
	}

	/**
	 * Add theme.json colors to acf color picker.
	 */
	public function add_theme_colors_to_acf_color_picker() {
		echo '<script type="text/javascript">
			(function($) {
				if (typeof wp !== "undefined" && typeof wp.data !== "undefined") {
					acf.add_filter("color_picker_args", function($args, $field) {
						const $settings = wp.data.select("core/editor").getEditorSettings();
						$args.palettes = $settings.colors.map(x => x.color);
						return $args;
					});
				}
			})(jQuery);
		</script>';
	}

	/**
	 * Save ACF field groups to JSON based on site URL.
	 */
	public function acf_json_save_point($path) {
		$subsite_slug = get_subsite_slug_from_url(get_site_url());
		$path = get_stylesheet_directory() . '/acf-json/' . $subsite_slug;

		if (!is_dir($path)) {
			wp_mkdir_p($path);
		}

		return $path;
	}

	/**
	 * Load ACF field groups from JSON based on site URL.
	 */
	public function acf_json_load_point($paths) {
		$subsite_slug = get_subsite_slug_from_url(get_site_url());
		$load_path = get_stylesheet_directory() . '/acf-json/' . $subsite_slug;

		if (!is_dir($load_path)) {
			wp_mkdir_p($load_path);
		}

		unset($paths[0]);
		$paths[] = $load_path;

		return $paths;
	}
}

/**
 * Initialize Hawp_Theme_ACF class with a function.
 */
function hawp_theme_acf() {
	global $hawp_theme_acf;

	if (!isset($hawp_theme_acf)) {
		$hawp_theme_acf = new Hawp_Theme_ACF();
		$hawp_theme_acf->setup();
	}
	return $hawp_theme_acf;
}
hawp_theme_acf();

endif; // class_exists check 