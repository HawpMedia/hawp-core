<?php
// ------------------------------------------
// Theme admin functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_Admin')):

class Hawp_Theme_Admin {

	/**
	 * Constructor.
	 */
	public function setup() {
		// Core admin functionality
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'], 999);
		add_action('admin_notices', [$this, 'admin_notices']);
		add_action('admin_bar_menu', [$this, 'add_admin_bar_menu'], 100);
		add_action('admin_bar_menu', [$this, 'add_admin_bar_notice'], 0);
		add_filter('admin_footer_text', [$this, 'change_admin_footer']);

		// ACF specific functionality
		add_action('acf/input/admin_footer', [$this, 'add_acf_color_palette']);
		add_action('acf/input/admin_footer', [$this, 'add_theme_colors_to_acf_color_picker']);
		add_filter('acf/settings/save_json', [$this, 'acf_json_save_point']);
		add_filter('acf/settings/load_json', [$this, 'acf_json_load_point']);

		// Setup conditional admin features
		$this->setup_admin_options();
	}

	/**
	 * Setup admin options based on theme settings
	 */
	private function setup_admin_options() {
		// Media handling
		if (get_theme_option('allow_svg_upload') === true && current_user_can('upload_files')) {
			add_filter('upload_mimes', [$this, 'allow_svg_upload']);
		}

		// WordPress admin customization
		if (get_theme_option('wordpress_admin_item') === false) {
			$this->setup_wordpress_admin_customization();
		}

		// Comments handling
		if (get_theme_option('comments_admin_menu_item') === false) {
			$this->setup_comments_removal();
		}

		// Login customization
		if (get_theme_option('login_logo')) {
			$this->setup_login_customization();
		}
	}

	/**
	 * Setup WordPress admin customization
	 */
	private function setup_wordpress_admin_customization() {
		add_action('admin_bar_menu', [$this, 'remove_wp_logo'], 999);
		add_action('wp_dashboard_setup', [$this, 'disable_default_dashboard_widgets'], 999);
		add_filter('admin_head', [$this, 'disable_help_tabs']);
		add_filter('admin_footer_text', '__return_false');
	}

	/**
	 * Setup comments removal
	 */
	private function setup_comments_removal() {
		add_action('admin_menu', [$this, 'remove_comments_menu_item']);
		add_action('init', [$this, 'remove_comment_support'], 100);
		add_action('wp_before_admin_bar_render', [$this, 'admin_bar_render']);
	}

	/**
	 * Setup login customization
	 */
	private function setup_login_customization() {
		add_action('login_head', [$this, 'custom_login_logo']);
		add_filter('login_headerurl', [$this, 'custom_login_logo_url']);
	}

	/**
	 * Add admin styles and scripts.
	 */
	public function admin_enqueue_scripts() {
		// Font Awesome
		wp_register_style('hm_admin_fontawesome_5', HM_URL.'/assets/lib/fontawesome/5.15.4/css/all.min.css');
		wp_enqueue_style('hm_admin_fontawesome_5');
		wp_register_style('hm_admin_fontawesome_6', HM_URL.'/assets/lib/fontawesome/6.5.1/css/all.min.css');
		wp_enqueue_style('hm_admin_fontawesome_6');

		// Admin styles
		wp_register_style('hm_admin_style', HM_URL.'/assets/css/admin.css');
		wp_enqueue_style('hm_admin_style');

		// Google Fonts
		$google_fonts = get_theme_option('google_fonts');
		if ($google_fonts) {
			wp_enqueue_style('hm_admin_google_fonts', $google_fonts);
		}

		// Theme options styles
		if (strpos($_SERVER['REQUEST_URI'], 'theme-options') !== false) {
			wp_register_style(
				'hm_admin_options_style',
				HM_URL.'/assets/css/admin-options.css',
				[],
				filemtime(HM_PATH.'/assets/css/admin-options.css')
			);
			wp_enqueue_style('hm_admin_options_style');
		}
	}

	/**
	 * Add admin notices.
	 */
	public function admin_notices() {
		if (current_user_can('administrator') && !get_option('blogname')) {
			printf(
				'<div class="notice notice-error"><p>Warning: Site Title is NOT set. Please go to the <a href="%s">Settings page</a> and add the site title.</p></div>',
				esc_url(admin_url('options-general.php'))
			);
		}
	}

	/**
	 * Add menu node to admin bar for development sites
	 */
	public function add_admin_bar_notice() {
		global $wp_admin_bar;
		$url = $_SERVER['HTTP_HOST'];

		if (!current_user_can('administrator') || (strpos($url, '.local') === false && strpos($url, '.dev') === false)) {
			return;
		}

		$wp_admin_bar->add_node([
			'id'    => 'dev-site-notice',
			'title' => '&lt;/&gt;',
			'meta'  => ['class' => 'dev-site-notice'],
			'position' => -99999,
		]);

		$wp_admin_bar->add_node([
			'parent'=> 'dev-site-notice',
			'id'    => 'dev-site-notice-text',
			'title' => sprintf('This website is in development by %s', Hawp_Theme::$whitelabel['brand_name']),
			'meta'  => ['class' => 'dev-site-notice-item'],
			'position' => -99999,
		]);
	}

	/**
	 * Add menu item to admin menu bar
	 */
	public function add_admin_bar_menu() {
		if (!current_user_can('manage_options')) {
			return;
		}

		global $wp_admin_bar;
		$wp_admin_bar->add_menu([
			'id' => 'contact_hawp',
			'title' => sprintf(__('Need help? Contact %s'), Hawp_Theme::$whitelabel['brand_name']),
			'href' => Hawp_Theme::$whitelabel['brand_url'],
			'meta' => ['target' => '_blank']
		]);
	}

	/**
	 * Change admin footer text
	 */
	public function change_admin_footer() {
		printf(
			'<span id="hm-footer-note">%s <a href="%s" target="_blank">%s</a>. <span style="width: 50px; height: 50px; position: absolute; right: 27px; top: -36px;">%s</span>',
			esc_html(Hawp_Theme::$whitelabel['admin_footer_text']),
			esc_url(Hawp_Theme::$whitelabel['brand_url']),
			esc_html(Hawp_Theme::$whitelabel['brand_name']),
			Hawp_Theme::$whitelabel['admin_footer_logo']
		);
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

	/**
	 * Allow SVG uploads in media library.
	 */
	public function allow_svg_upload($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	/**
	 * Remove WordPress admin logo.
	 */
	public function remove_wp_logo($wp_admin_bar) {
		$wp_admin_bar->remove_node('wp-logo');
	}

	/**
	 * Remove comment menu item and theme support.
	 */
	public function admin_bar_render() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('comments');
	}

	public function remove_comments_menu_item() {
		remove_menu_page('edit-comments.php');
	}

	public function remove_comment_support() {
		remove_post_type_support('post', 'comments');
		remove_post_type_support('page', 'comments');
	}

	/**
	 * Disable default dashboard widgets.
	 */
	public function disable_default_dashboard_widgets() {
		global $wp_meta_boxes;
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		remove_action('welcome_panel', 'wp_welcome_panel');
	}

	/**
	 * Disable help tabs.
	 */
	public function disable_help_tabs() {
		$screen = get_current_screen();
		$screen->remove_help_tabs();
	}

	/**
	 * Custom login logo.
	 */
	public function custom_login_logo() {
		$logo = wp_get_attachment_image_url(get_theme_option('login_logo'), 'full');
		if (!$logo) {
			return;
		}

		printf(
			'<style>h1 a { background-image: url(%s) !important; }</style>',
			esc_url($logo)
		);
	}

	/**
	 * Custom login logo url.
	 */
	public function custom_login_logo_url() {
		return get_home_url();
	}
}

/**
 * Initialize Hawp_Theme_Admin class with a function.
 */
function hawp_theme_admin() {
	global $hawp_theme_admin;

	if (!isset($hawp_theme_admin)) {
		$hawp_theme_admin = new Hawp_Theme_Admin();
		$hawp_theme_admin->setup();
	}
	return $hawp_theme_admin;
}
hawp_theme_admin();

endif; // class_exists check
