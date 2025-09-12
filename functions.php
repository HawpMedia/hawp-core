<?php
// ------------------------------------------
// Get the theme rolling!
// ------------------------------------------

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
