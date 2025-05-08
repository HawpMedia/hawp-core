<?php
/**
 * Displays the header menu.
 */
?>

<nav id="menu" data-target="#menu" class="menu-tab" role="navigation">

	<?php wp_nav_menu(array(
		'theme_location' => 'primary',
		'menu_id' => 'primary-menu',
		'menu_class' => 'menu',
		'container' => false,
	)); ?>

	<ul class="sub-menu menu-search-form">
		<li><?php get_search_form(); ?></li>
	</ul>

</nav>
