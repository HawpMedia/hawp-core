<?php
/**
 * The post meta template file.
 */
?>

<div class="entry-meta">
	<span class="entry-date">Posted on <time class="entry-date published updated" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time></span> <span class="entry-by">by <span class="author"><?php echo esc_html(get_the_author_meta('display_name', $post->post_author)); ?></span></span>
</div>
