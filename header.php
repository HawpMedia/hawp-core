<?php
/**
 * The header for our theme.
 */

get_template_part('parts/layout/head');
?>

<header id="header" class="header contain" role="banner">

	<?php echo do_shortcode('[logo class="logo"]'); ?>

	<?php get_template_part('parts/layout/header-menu-buttons'); ?>

</header>

<?php get_template_part('parts/layout/header-menu'); ?>

<main id="main" role="main">

<span class="sentry"></span>
