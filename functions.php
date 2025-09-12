<?php
// ------------------------------------------
// Get the theme rolling!
// ------------------------------------------

// if ( ! function_exists( 'hawp_core_freemius' ) ) {
//     // Create a helper function for easy SDK access.
//     function hawp_core_freemius() {
//         global $hawp_core_freemius;

//         if ( ! isset( $hawp_core_freemius ) ) {
//             // Include Freemius SDK.
//             require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
//             $hawp_core_freemius = fs_dynamic_init( array(
//                 'id'                  => '19173',
//                 'slug'                => 'hawp-core',
//                 'premium_slug'        => 'hawp-core',
//                 'type'                => 'theme',
//                 'public_key'          => 'pk_423a8945ae306d33f0fccfb529530',
//                 'is_premium'          => true,
//                 'premium_suffix'      => '',
//                 // If your theme is a serviceware, set this option to false.
//                 'has_premium_version' => true,
//                 'has_addons'          => false,
//                 'has_paid_plans'      => true,
//                 'is_org_compliant'    => false,
//                 'menu'                => array(
//                     'slug'           => 'hm-theme-options',
//                     'first-path'     => 'admin.php?page=hm-theme-options',
//                     'support'        => false,
//                 ),
//             ) );
//         }

//         return $hawp_core_freemius;
//     }

//     // Init Freemius.
//     hawp_core_freemius();
//     // Signal that SDK was initiated.
//     do_action( 'hawp_core_freemius_loaded' );
// }

// Define constants
define('HM_PATH', get_template_directory());
define('HM_URL', get_template_directory_uri());
define('HMC_PATH', get_stylesheet_directory());
define('HMC_URL', get_stylesheet_directory_uri());
define('HM_THEME_VERSION', wp_get_theme(get_template())->get('Version'));

// Include the theme
require_once(HM_PATH.'/core/theme.php');

// GitHub updater
require_once(HM_PATH.'/core/updater.php');

// Optional: Configure updater via constants. Override or use the filter 'hawp_github_updater_config'.
// define('HAWP_GITHUB_REPO', 'OWNER/REPO');
// define('HAWP_GITHUB_BRANCH', 'main');
// define('HAWP_GITHUB_ASSET', 'hawp-core.zip'); // if using a release asset
// define('HAWP_GITHUB_TOKEN', ''); // if private repo

// Set the content width, this doesnt matter for WP 5.0+ sites
if (!isset($content_width)) {
	$content_width = 1400;
}
