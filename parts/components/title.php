<?php
/**
 * Displays the post header.
 */

$title = '';
if (is_404()) {
	$title = 'Page not found.';
} elseif (is_home() && get_option('page_for_posts')) {
	$title = get_the_title(get_option('page_for_posts'));
} elseif (is_home()) {
	$title = get_bloginfo('name');
} elseif (is_singular()) {
	$title = get_the_title();
} elseif (is_search()) {
	$title = 'Search Results for: <span class="search-query">'.get_search_query().'</span>';
} elseif (is_archive()) {
	$title = get_the_archive_title();
}
?>

<h1 class="main-title entry-title"><?php echo $title; ?></h1>

<?php if (function_exists('yoast_breadcrumb') && !is_front_page()) yoast_breadcrumb('<nav class="main-breadcrumbs">', '</nav>'); ?>

<?php if (function_exists('rank_math_the_breadcrumbs') && !is_front_page()) rank_math_the_breadcrumbs(); ?>
