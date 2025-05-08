<?php
/**
 * Template Name: Fullwidth No Header
 * Template Post Type: post, page
 */

get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

	<article id="page-<?php the_ID(); ?>" <?php post_class('main-content main-fullwidth main-'.get_post_type()); ?>>

		<div class="entry-wrapper entry-fullwidth">

			<div class="clear entry-content entry-<?php echo get_post_type(); ?>">

				<?php get_template_part('parts/content/content', get_post_type()); ?>

			</div>

		</div>

	</article>

<?php endwhile; ?>

<?php get_footer(); ?> 
