<?php
/**
 * The template for displaying comments
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password,
 * return early without loading the comments.
 */
if (post_password_required()) {
	return;
}

?>

<?php if (get_option('default_comment_status') === 'open') : ?>

	<div id="comments" class="clear comments-area <?php echo get_option( 'show_avatars' ) ? 'show-avatars' : ''; ?>">

		<?php if (have_comments()): ?>
			<h2 class="comments-title">
				<?php if ( '1' === get_comments_number() ) : ?>
					<?php esc_html_e( '1 comment', 'hawp' ); ?>
				<?php else : ?>
					<?php
					printf(
						/* translators: %s: comment count number. */
						esc_html( _nx( '%s comment', '%s comments', get_comments_number(), 'Comments title', 'hawp' ) ),
						esc_html( number_format_i18n( get_comments_number() ) )
					);
					?>
				<?php endif; ?>
			</h2>

			<ol class="comment-list">
				<?php
				wp_list_comments(
					array(
						'avatar_size' => 60,
						'style'       => 'ol',
						'short_ping'  => true,
					)
				);
				?>
			</ol>

			<?php
			the_comments_pagination(
				array(
					/* translators: There is a space after page. */
					'before_page_number' => esc_html__( 'Page ', 'hawp' ),
					'mid_size'           => 0,
					'prev_text'          => sprintf(
						'%s <span class="nav-prev-text">%s</span>',
						'<-',
						esc_html__( 'Older comments', 'hawp' )
					),
					'next_text'          => sprintf(
						'<span class="nav-next-text">%s</span> %s',
						esc_html__( 'Newer comments', 'hawp' ),
						'->'
					),
				)
			);
			?>

		<?php endif; ?>

		<?php
		comment_form(
			array(
				'logged_in_as' => null,
				'title_reply' => esc_html__( 'Leave a comment', 'hawp' ),
				'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
				'title_reply_after' => '</h2>',
			)
		);
		?>

	</div>
<?php endif; ?>
