<?php
/**
 * The search content file.
 */

get_search_form(); ?>

<?php if (have_posts()) : ?>

	<?php while (have_posts()) : the_post(); ?>

		<?php get_template_part('parts/components/listing', get_post_type()); ?>

	<?php endwhile; ?>

	<?php get_template_part('parts/components/pagination', 'search'); ?>

<?php else : ?>

	<div class="no-results not-found">
		<p>Sorry, but nothing matched your search terms. Please try again with some different keywords.</p>
	</div>

<?php endif; ?>
