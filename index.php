<?php
/**
 * The main template file.
 */

$type = '';
if (is_404()) {
	$type = '404';
} elseif (is_home()) {
	$type = 'blog';
} elseif (is_singular()) {
	$type = get_post_type();
} elseif (is_search()) {
	$type = 'search';
} elseif (is_archive()) {
	$type = 'archive';
}

get_header(); ?>

<?php if (is_singular()) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class('main-content main-sidebar main-'.$type); ?>>
<?php else : ?>
	<div class="main-content main-sidebar main-<?php echo $type; ?>">
<?php endif; ?>

	<header class="contain main-header">
		<?php get_template_part('parts/components/title', $type); ?>
		<?php if(is_singular()){ get_template_part('parts/components/meta', $type); } ?>
	</header>

	<div class="contain entry-wrapper entry-sidebar">

		<div class="clear entry-content entry-<?php echo $type; ?>">

			<?php get_template_part('parts/content/content', $type); ?>

		</div>

		<?php get_sidebar(); ?>

	</div>

<?php if (is_singular()) : ?>

	<?php if (comments_open() || get_comments_number()): ?>
		<div class="contain comments-wrapper comments-sidebar">
			<?php comments_template(); ?>
		</div>
	<?php endif; ?>

	</article>
<?php else : ?>
	</div>
<?php endif; ?>

<?php get_footer(); ?>
