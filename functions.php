<?php
// ------------------------------------------
// Get the theme rolling!
// ------------------------------------------

// Create a helper function for easy SDK access.
if ( ! function_exists( 'hawp_freemius' ) ) {

	function hawp_freemius() {
		global $hawp_freemius;

		if ( ! isset( $hawp_freemius ) ) {
			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/vendor/freemius/start.php';

			$hawp_freemius = fs_dynamic_init( array(
				'id'                  => '16114',
				'slug'                => 'hawp',
				'premium_slug'        => 'hawp',
				'type'                => 'theme',
				'public_key'          => 'pk_d2a774ac3e7a7585ec079e4c91e84',
				'is_premium'          => true,
				'is_premium_only'     => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'is_org_compliant'    => false,
				'menu'                => array(
					'slug'           => 'hm-theme-options',
					'first-path'     => 'admin.php?page=hm-theme-options',
					'support'        => false,
				),
			) );
		}

		return $hawp_freemius;
	}

	// Init Freemius.
	hawp_freemius();
	// Signal that SDK was initiated.
	do_action( 'hawp_freemius_loaded' );
}

// Define constants
define('HM_PATH', get_template_directory());
define('HM_URL', get_template_directory_uri());
define('HMC_PATH', get_stylesheet_directory());
define('HMC_URL', get_stylesheet_directory_uri());
define('HM_THEME_VERSION', wp_get_theme(get_template())->get('Version'));

// Include the theme
require_once(HM_PATH.'/core/theme.php');

// Set the content width, this doesnt matter for WP 5.0+ sites
if (!isset($content_width)) {
	$content_width = 1400;
}
