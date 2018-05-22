<?php
/**
 *	Kalium WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$comments_number = get_comments_number();

if ( have_comments() ) :

	?>
	<section class="post-comments">
		
	    <div class="container">
		    
			<div class="post-comments--section-title">
				<h2><?php echo sprintf( _n( '1 Comment', '%d Comments', $comments_number, 'kalium' ), $comments_number ); ?></h2>
				<p><?php $comments_number > 0 ? _e( 'Join the discussion and tell us your opinion.', 'kalium' ) : _e( 'Be the first to comment on this article.', 'kalium' ); ?></p>
			</div>
	
			<div class="post-comments--list">
			<?php
				
				// Comments List
				wp_list_comments( array(
					'style'        => 'div',
					'callback'     => 'kalium_blog_post_comment_open',
					'end-callback' => 'kalium_blog_post_comment_close'
				) );
			
			?>
			</div>
			<?php
		
				// Comments pagination
				if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
		
					echo '<div class="pagination-container">' . paginate_comments_links( array(
						'prev_text' => sprintf( __( '%s Previous', 'kalium' ), '<i class="flaticon-arrow427"></i>' ),
						'next_text' => sprintf( __( 'Next %s', 'kalium' ), '<i class="flaticon-arrow413"></i>' ),
						'type'      => 'list',
						'echo'		=> false
					) ) . '</div>';
		
				endif;
			?>
	
	
	    </div>
	    
	</section>
	<?php
	
endif;


/*
// Closed Comments Notification
if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :

	// To do..

endif;
*/

// Post comment form
if ( is_single() && comments_open() ) :

	// Form arguments
	$form_args = array(
		'format' => 'html5',
	
		'title_reply' 			=> have_comments() ? __( 'Leave a reply', 'kalium' ) : __( 'Share your thoughts', 'kalium' ),
		'title_reply_to' 		=> __( 'Reply to %s', 'kalium' ),
	
		'comment_notes_before' 	=> '',
		'comment_notes_after' 	=> '',
	
		'label_submit'			=> __( 'Comment', 'kalium' ),
		'class_submit'			=> 'button',
	);

	?>
	<section class="post-comment-form">
		
	    <div class="container">
		    
			<?php comment_form( $form_args ); ?>
			
		</div>
		
	</section>
	<?php

endif;