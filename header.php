<?php
/**
 * The header for our theme.
 */

get_template_part('parts/structure/head');
?>

<header id="header" class="header contain" role="banner">

	<?php echo do_shortcode('[logo class="logo"]'); ?>

	<?php get_template_part('parts/components/nav-controls-default'); ?>

</header>

<?php get_template_part('parts/structure/nav-primary'); ?>

<main id="main" role="main">

<span class="sentry"></span>
