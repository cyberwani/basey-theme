<article class="comment">
	<?php if ($comment->comment_approved == '0') : ?>
		<div data-alert class="alert-box">
			<?php _e('Your comment is awaiting moderation.', 'basey'); ?>
		</div>
	<?php endif; ?>
	<header class="comment-header">
		<?php echo get_avatar($comment, $size = '64'); ?>
		<h4 class="comment-title"><?php echo get_comment_author_link(); ?></h4>
		<p class="comment-meta">
			Written on <time datetime="<?php echo comment_date('c'); ?>"><a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>"><?php printf(__('%1$s', 'basey'), get_comment_date(),  get_comment_time()); ?></a></time>
			<?php edit_comment_link(__('(Edit)', 'basey'), '', ''); ?>
		</p>
	</header>
	<div class="comment-body">
		<?php comment_text(); ?>
	</div>
	<?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>