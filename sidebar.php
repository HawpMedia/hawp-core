<?php
/**
 * The sidebar template file.
 */

if (get_post_type()=='post') {
	$sidebar = 'blog';
} else {
	$sidebar = 'site';
}

if (is_active_sidebar($sidebar)): ?>

	<aside id="secondary" class="widget-area" role="complementary">
		<?php dynamic_sidebar($sidebar); ?>
	</aside>

<?php endif; ?>
