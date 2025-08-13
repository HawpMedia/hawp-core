<?php
/**
 * The archive listing template file.
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('entry-listing'); ?>>

	<header class="entry-header">
		<h2 class="listing-title entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		<?php get_template_part('parts/components/meta', get_post_type()); ?>
	</header>

	<div class="entry-summary clear">
		<?php echo has_post_thumbnail() ? the_post_thumbnail('thumbnail', array('class'=>'alignleft')) : '';
		the_excerpt(); ?>
		<div class="excerpt-read-more"><a href="<?php echo get_permalink(); ?>" class="button-read-more">Read More</a></div>
	</div>

</article>
