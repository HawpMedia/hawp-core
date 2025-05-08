<?php
/**
 * The blog content file.
 */

if (have_posts()) : ?>

	<?php while (have_posts()) : the_post(); ?>

		<?php get_template_part('parts/components/listing', get_post_type()); ?>

	<?php endwhile; ?>

	<?php get_template_part('parts/components/pagination', 'blog'); ?>

<?php else : ?>

	<div class="no-results not-found">
		<p>Sorry, we couldn't find any results.</p>
	</div>

<?php endif; ?>
