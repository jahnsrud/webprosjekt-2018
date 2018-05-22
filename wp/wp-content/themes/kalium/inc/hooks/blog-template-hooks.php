<?php
/**
 *	Kalium WordPress Theme
 *
 *	Blog Template Hooks
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

/**
 *	Initialize blog options for each blog-posts instance
 */
add_action( 'kalium_blog_archive_before_content', 'kalium_blog_initialize_options', 10 );
add_action( 'kalium_blog_archive_before_content', 'kalium_blog_page_header', 20 );

/**
 *	Blog archive page
 */
add_action( 'kalium_blog_archive_content', 'kalium_blog_archive_posts_column_open', 10 );
add_action( 'kalium_blog_archive_content', 'kalium_blog_posts_loop', 20 );
add_action( 'kalium_blog_archive_content', 'kalium_blog_archive_posts_pagination', 30 );
add_action( 'kalium_blog_archive_content', 'kalium_blog_archive_posts_column_close', 40 );
add_action( 'kalium_blog_archive_content', 'kalium_blog_sidebar_loop', 50 );

/**
 * No posts to show
 */
add_action( 'kalium_blog_no_posts_found', 'kalium_blog_no_posts_found_message', 10 );

/**
 *	Blog loop posts before
 */
add_action( 'kalium_blog_loop_before', 'kalium_blog_loop_loading_posts_indicator', 10 );

/**
 * Blog loop before post item
 */
add_action( 'kalium_blog_loop_post_before', 'kalium_blog_post_thumbnail', 10 );

/**
 * Blog loop post details
 */
add_action( 'kalium_blog_loop_post_details', 'kalium_blog_post_title', 10 );
add_action( 'kalium_blog_loop_post_details', 'kalium_blog_post_excerpt', 20 );
add_action( 'kalium_blog_loop_post_details', 'kalium_blog_post_date', 30 );
add_action( 'kalium_blog_loop_post_details', 'kalium_blog_post_category', 40 );

/**
 *	Loop thumbnail hover layer and post format icon
 */
add_action( 'kalium_blog_loop_after_post_thumbnail', 'kalium_blog_post_hover_layer', 10 );
add_action( 'kalium_blog_loop_after_post_thumbnail', 'kalium_blog_post_format_icon', 20 );

/**
 * Reset blog options global
 */
add_action( 'kalium_blog_archive_after_content', 'kalium_blog_reset_options', 1000 );

/**
 *	External post redirect
 */
add_action( 'template_redirect', 'kalium_blog_external_post_format_redirect' );

/**
 *	Link post formats will have "href" attribute replaced by their custom link
 */
add_action( 'post_link', 'kalium_blog_post_format_link_url', 10, 2 );

/**
 *	Blog post, video format post use featured image as video poster
 */
add_filter( 'kalium_video_shortcode_container_atts', 'kalium_blog_post_video_poster_replace', 10 );
add_filter( 'kalium_audio_shortcode_container_atts', 'kalium_blog_post_video_poster_replace', 10 );

/**
 * Single blog content
 */
add_action( 'kalium_blog_single_content', 'kalium_blog_single_post_image_boxed', 10 );
add_action( 'kalium_blog_single_content', 'kalium_blog_single_post_layout', 20 );
add_action( 'kalium_blog_single_content', 'kalium_blog_single_post_sidebar', 30 );

/**
 * Excerpt length for loop posts
 */
add_filter( 'excerpt_length', 'kalium_blog_custom_excerpt_length', 100 );

/**
 * Single post before
 */
add_action( 'kalium_blog_single_before_content', 'kalium_blog_single_post_image_full_width', 10 );

/**
 * Single post after
 */
add_filter( 'kalium_blog_single_after_content', 'kalium_blog_single_post_comments', 10 );

/**
 * Single post details
 */
add_action( 'kalium_blog_single_post_details', 'kalium_blog_post_title', 10 );
add_action( 'kalium_blog_single_post_details', 'kalium_blog_post_content', 20 );
add_action( 'kalium_blog_single_post_details', 'kalium_blog_single_post_tags_list', 30 );
add_action( 'kalium_blog_single_post_details', 'kalium_blog_single_post_share_networks', 40 );
add_action( 'kalium_blog_single_post_details', 'kalium_blog_single_post_author_info_below', 50 ); // [conditional]

/**
 * Single post author, date and category aside
 */
add_action( 'kalium_blog_single_post_details_after', 'kalium_blog_single_post_author_and_meta_aside', 10 ); // [conditional]
add_action( 'kalium_blog_single_post_details_after', 'kalium_blog_single_post_prev_next_navigation', 20 );

/**
 * Post meta entries
 */
add_action( 'kalium_blog_single_post_meta', 'kalium_blog_post_date', 10 );
add_action( 'kalium_blog_single_post_meta', 'kalium_blog_post_category', 20 );

/**
 * Display post meta under the title when the author info is shown below the article
 */
add_action( 'kalium_blog_single_post_details', 'kalium_blog_single_post_meta_below_title', 15 ); // [conditional]

/**
 * Post author description
 */
add_action( 'kalium_blog_single_post_author_info_details', 'kalium_blog_single_post_author_info_description', 10, 2 );

/**
 * Archive post classes
 */
add_filter( 'kalium_blog_container_class', 'kalium_blog_container_classes', 10 );

/**
 * Single post classes
 */
add_filter( 'kalium_blog_single_container_class', 'kalium_blog_single_container_classes', 10 );

/**
 * Clear post format contents
 */
add_filter( 'kalium_blog_post_content', 'kalium_blog_clear_post_format_from_the_content', 10 );

/**
 * Single post comments enable or disable
 */
add_filter( 'kalium_blog_comments', 'kalium_blog_comments_visibility', 10 );

/**
 * Single post comments hooks
 */
add_filter( 'comment_form_defaults', 'kalium_blog_comment_form_defaults', 10 );

/**
 * Check for debug mode
 */
add_filter( 'body_class', 'kalium_check_debug_bode_body_class', 10 );