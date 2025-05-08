<?php
/**
 * Displays the footer widget area.
 */

if (is_active_sidebar('footer')): ?>

	<aside class="contain widget-area">
		<?php dynamic_sidebar('footer'); ?>
	</aside>

<?php endif; ?>
