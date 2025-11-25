<?php
/**
 * Displays the header mobile menu tray.
 *
 * If this is being used, remove the nav-controls-default
 * template part call from the header.php file and replace
 * it with the name of this file. And dont forget to comment
 * out the sass for nav-mobile-burger in the style.scss files
 * located in assets > sass
 */
?>

<nav class="menu-buttons">
	<a class="menu-button menu-home" href="<?php echo esc_url(home_url()); ?>"><span class="menu-button-icon fa fa-home"></span><span class="menu-button-label">Home</span></a>
	<a class="menu-button menu-contact" href="tel:<?php echo do_shortcode('[phone format="raw" dial_code="1"]'); ?>"><span class="menu-button-icon fa fa-phone"></span><span class="menu-button-label">Call</span></a>
	<a class="menu-button menu-open js-turnon" href="#menu" data-target="#menu"><span class="menu-button-icon fa fa-bars"></span><span class="menu-button-label">Menu</span></a>
	<a class="menu-button menu-close js-turnoff" href="#main" data-target="#menu"><span class="menu-button-icon fa fa-times"></span><span class="menu-button-label">Close Menu</span></a>
</nav>
