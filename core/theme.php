<?php
// ------------------------------------------
// This file loads the theme.
// ------------------------------------------

if (!class_exists('Hawp_Theme')):

class Hawp_Theme {

	public static $theme = [
		'name'                => 'Hawp Theme',
		'version'             => HM_THEME_VERSION,
		'file'                => __FILE__,
		'textdomain'          => 'hawp',
		'option_prefix'       => 'hawp_theme_',
	];

	/**
	 * Whitelabel settings that can be customized in child themes
	 */
	public static $whitelabel = [
		'brand_name'          => 'Hawp Media',
		'brand_url'           => 'https://hawpmedia.com',
		'admin_footer_text'   => 'This website was developed by ',
		'admin_footer_logo'   => '<svg viewBox="0 0 50 43.5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><clipPath id="a"><path d="m0 0h50v43.5h-50z"/></clipPath><g clip-path="url(#a)" fill="#48bdee"><path d="m35.8 20.09c6.45.74 3.61 4.89-1.78 6.66 3.57-5.3.77-3.93-7.05-6.21 13.25-4.4 13.2-9.92 12.5-14.12-1.24-2.44-3.2-4.43-1.79-6.43.97.5 2.66 2.17 3.12 3.02 2.83.45 4.69 3.11 9.17 5.41.07.24-.1 1.4-1.25 1.86-1.48.59-3.21-.51-4.83 1.12-1.05 1.06-1.24 3.08-2.19 4.36-1.54 2.13-3.97 3.45-5.91 4.32z"/><path d="m36.84 9.38c0 .57-.91 5.01-7.13 7-2.65.72-5.41.69-8.63-.6 8.12 8.68-.13 11.51-2.81 17.5-.66 1.79.86 5.04.88 6.74-.06 1.81-1.44 3.3-3.24 3.49.07-.54.52-3.26.32-4.33-.23-1.21-1.38-2.99-1.61-4.62-.4-2.9 2.79-6.01 3.24-8.17.17-.79-.36-1.8-1.11-3.02-.79-1.27-1.92-2.32-2.04-2.28-.59.31-1.5 2.31-1.81 3.17-1.02 2.79-2.68 8.68-4.57 10.97-1.38 1.67-4.87 3.33-8.31 2.34 3.09 0 6.05-2.33 6.84-4.17 1.99-4.69.94-12.6 6.58-19.31 6.5-6.99 13.05-3.86 18.6-3.67 2.82.09 4.77-1 4.82-1.03z"/></g></svg>',
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('after_setup_theme', [$this, 'includes'], 1);
		// Make whitelabel settings filterable
		self::$whitelabel = apply_filters('hawp_whitelabel_settings', self::$whitelabel);
	}

	/**
	 * Include the required files in the correct order.
	 */
	public function includes() {
		$includes = [
			'utilities',    // Core utility functions
			'setup',        // Basic theme setup
			'acf',          // ACF integration
			'admin',        // Admin functionality
			'shortcodes',   // Shortcode functionality
			'rankmath',     // RankMath integration
			'theme-options' // Theme options
		];

		foreach ($includes as $include) {
			require_once HM_PATH."/core/{$include}.php";
		}
	}

}

/**
 * Initialize Hawp_Theme class with a function.
 */
function hawp_theme() {
	global $hawp_theme;

	// Instantiate only once.
	if (!isset($hawp_theme)) {
		$hawp_theme = new Hawp_Theme();
	}
	return $hawp_theme;
}
hawp_theme();

endif; // class_exists check