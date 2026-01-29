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
	 * Add theme.json or editor color palette to ACF color picker.
	 */
	public function add_acf_color_palette() {
		$color_palette = $this->get_editor_color_palette();
		$color_palette = $color_palette ? $color_palette : '[]';

		printf(
			'<script type="text/javascript">
				(function($) {
					const fallbackPalette = %s;
					function getEditorSettings() {
						if (typeof wp === "undefined" || !wp.data || !wp.data.select) {
							return null;
						}
						const stores = ["core/editor", "core/edit-widgets", "core/block-editor"];
						for (let i = 0; i < stores.length; i++) {
							const selector = wp.data.select(stores[i]);
							if (!selector) {
								continue;
							}
							if (typeof selector.getEditorSettings === "function") {
								return selector.getEditorSettings();
							}
							if (typeof selector.getSettings === "function") {
								return selector.getSettings();
							}
						}
						return null;
					}
					acf.add_filter("color_picker_args", function(args, $field) {
						const settings = getEditorSettings();
						if (settings && Array.isArray(settings.colors) && settings.colors.length) {
							args.palettes = settings.colors.map(x => x.color);
						} else if (Array.isArray(fallbackPalette) && fallbackPalette.length) {
							args.palettes = fallbackPalette;
						}
						return args;
					});
				})(jQuery);
			</script>',
			$color_palette
		);
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