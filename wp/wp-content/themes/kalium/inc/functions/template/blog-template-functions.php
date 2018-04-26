<?php
/**
 *	Kalium WordPress Theme
 *
 *	Blog Template Functions
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Get current blog posts template
 */
if ( ! function_exists( 'kalium_blog_get_template' ) ) {
	
	// Get blog template
	function kalium_blog_get_template() {
		return kalium_blog_get_option( 'blog_template' );
	}
	
}

/**
 * Blog page heading title and description
 */
if ( ! function_exists( 'kalium_blog_page_header' ) ) {
	
	function kalium_blog_page_header() {
		// Args
		$args = array();
		
		if ( kalium_blog_get_option( 'loop/header/show' ) ) {
			
			$args['heading_tag'] = 'h1';
			
			$title = kalium_blog_get_option( 'loop/header/title' );
			$description = kalium_blog_get_option( 'loop/header/description' );
			
			// Category, tag and author pages show custom title
			if ( apply_filters( 'kalium_blog_page_heading_replace_for_taxonomies', true ) ) {
				$queried_object = get_queried_object();
				
				$separator = apply_filters( 'kalium_blog_page_heading_replace_for_taxonomies_separator', ' / ' );
				$parents = apply_filters( 'kalium_blog_page_heading_replace_for_taxonomies_parents', 'multiple' );
				
				// Category
				if ( is_category() ) {
					
					if ( apply_filters( 'kalium_blog_page_header_last_category_title_only', false ) ) {
						$categories = single_cat_title( $separator, false );
					} else {
						$categories  = strip_tags( get_the_category_list( $separator, $parents ) );
					}
					
					$title       = sprintf( '%s %s <span>%s</span>', __( 'Category', 'kalium' ), $separator, $categories );
					$description = category_description( $queried_object->object_id );
				}
				// Tag
				else if ( is_tag() ) {
					$tag  		 = single_term_title( '', false );
					$title       = sprintf( '%s %s <span>%s</span>', __( 'Tag', 'kalium' ), $separator, $tag );
					$description = tag_description( $queried_object->object_id );
				}
				// Author
				else if ( is_author() ) {
					$title 		 = sprintf( '%s %s <span>%s</span>', __( 'Author', 'kalium' ), $separator, get_the_author() );
					$description = get_the_author_meta( 'description' );
				}
				// Year
				else if ( is_year() ) {
					$title 		 = sprintf( '%s %s <span>%s</span>', __( 'Year', 'kalium' ), $separator, get_the_date( 'Y' ) );
					$description = '';
				}
				// Month
				else if ( is_month() ) {
					$title 		 = sprintf( '%s %s <span>%s</span>', __( 'Month', 'kalium' ), $separator, get_the_date( 'F Y' ) );
					$description = '';
				}
				// Day
				else if ( is_day() ) {
					$title 		 = sprintf( '%s %s <span>%s</span>', __( 'Month', 'kalium' ), $separator, get_the_date( 'F j, Y' ) );
					$description = '';
				}
			}
			
			// Title and description
			$args['title']       = $title;
			$args['description'] = $description;
			
			kalium_get_template( 'global/page-heading.php', $args );
		}
	}
}

/**
 * Posts loop
 */
if ( ! function_exists( 'kalium_blog_posts_loop' ) ) {
	
	function kalium_blog_posts_loop() {
		// Args
		$args = array(
			'id' => kalium_blog_get_option( 'id' ),
			'classes' => kalium_blog_get_option( 'loop/container_classes' )
		);
		
		if ( 'fit-rows' == kalium_blog_get_option( 'loop/row_layout_mode' ) ) {
			$args['classes'][] = 'fit-rows';
		}
		
		// Gap
		$columns_gap = kalium_blog_get_option( 'loop/other/columns_gap' );
		
		if ( 'standard' == kalium_blog_get_template() && '' !== $columns_gap ) {
			$columns_gap = intval( $columns_gap );
			$args['classes'][] = sprintf( 'columns-gap-%s', $columns_gap >= 0 ? $columns_gap : 'none' );
		}
		
		
		kalium_get_template( 'blog/posts.php', $args );
	}
}

/**
 * Blog loop post render
 */
if ( ! function_exists( 'kalium_blog_loop_post_template' ) ) {
	
	function kalium_blog_loop_post_template() {
		
		// Current blog template
		$blog_template = kalium_blog_get_template();
		
		// Post classes
		$classes = array( 'post-item', 'template-' . $blog_template );
		
		if ( in_array( $blog_template, array( 'square', 'rounded' ) ) ) {
			$classes[] = 'columned';
		}
		
		// Args
		$args = array(
			'classes' => $classes
		);

		kalium_get_template( 'blog/loop/post-item.php', $args );
	}
}

/**
 * Blog archive classes
 */
if ( ! function_exists( 'kalium_blog_container_classes' ) ) {
	
	function kalium_blog_container_classes( $classes ) {
		// Sidebar
		
		if ( kalium_blog_get_option( 'loop/sidebar/visible' ) ) {
			$sidebar_alignment = kalium_blog_get_option( 'loop/sidebar/alignment' );
			
			$classes[] = 'blog--has-sidebar';
			$classes[] = sprintf( 'blog--sidebar-alignment-%s', $sidebar_alignment );
		}
		
		if ( ! empty( $_classes ) && is_array( $_classes ) ) {
			$classes = array_merge( $classes, $_classes );
		}
		
		return $classes;
	}
}

/**
 * No posts to show message
 */
if ( ! function_exists( 'kalium_blog_no_posts_found_message' ) ) {
	
	function kalium_blog_no_posts_found_message() {
		
		?>
		<h3 class="no-posts-found"><?php _e( 'There are no posts to show', 'kalium' ); ?></h3>
		<?php
	}
}

/**
 * Blog archive pagination
 */
if ( ! function_exists( 'kalium_blog_archive_posts_pagination' ) ) {
	
	function kalium_blog_archive_posts_pagination() {
		// Args
		$args = array();
		
		// Blog instance ID
		$blog_instance_id = kalium_blog_instance_id();
		$args['id'] = $blog_instance_id;
		
		// Pagination Type
		$pagination_type = kalium_blog_get_option( 'loop/pagination/type' );
		$pagination_alignment = kalium_blog_get_option( 'loop/pagination/alignment' );
		$pagination_style = kalium_blog_get_option( 'loop/pagination/style' );
		
		// Num pages
		$query = $GLOBALS['wp_query'];
		$max_num_pages = $query->max_num_pages;
		$posts_per_page = $query->query_vars['posts_per_page'];
		$found_posts = absint( $query->found_posts );
				
		// Classes
		$classes = array();
		$classes[] = sprintf( 'pagination--align-%s', $pagination_alignment );
		
		$args['extra_classes'] = $classes;
		
		// If there is more than one page
		if ( $max_num_pages > 1 ) {
		
			// Normal pagination
			if ( 'normal' == $pagination_type ) {
				$pagination_args = array(
					'total' => $max_num_pages
				);
				
				$args['pagination_args'] = $pagination_args;
				
				kalium_get_template( 'global/pagination-normal.php', $args );
			}
			// Endless pagination
			else if ( in_array( $pagination_type, array( 'endless', 'endless-reveal' ) ) ) {
				
				$args['show_more_text'] = __( 'Show more', 'kalium' );
				$args['all_items_shown_text'] = __( 'All posts are shown', 'kalium' );
				$args['loading_style'] = $pagination_style;
				
				kalium_get_template( 'global/pagination-infinite-scroll.php', $args );
				
				// Endless pagination instance (JS)
				$infinite_scroll_pagination_args = array(
					// Base query
					'base_query'	 => $query->query,
					
					// Pagination
					'total_items'	 => $found_posts,
					'posts_per_page' => $posts_per_page,
					'fetched_items'	 => kalium_get_post_ids_from_query( $query ),
					
					// Auto reveal
					'auto_reveal' 	 => 'endless-reveal' == $pagination_type,
					
					// Loop template function
					'loop_template'	 => 'kalium_blog_loop_post_template',
					
					// Action and callback
					'callback'		 => 'kaliumBlogEndlessPaginationHandler',
					
					// Extra arguments (passed on Ajax Request)
					'args' 			 => array(
						'blogInfiniteScroll' => 1
					)
				);
				
				kalium_infinite_scroll_pagination_js_object( $blog_instance_id, $infinite_scroll_pagination_args );
				
				// Enqueue some scripts that are required
				if ( kalium_blog_get_option( 'loop/post_formats' ) ) {
					kalium_enqueue_media_library();
				}
			}
		}
	}
}

/**
 * Blog archive, posts column wrapper open
 */
if ( ! function_exists( 'kalium_blog_archive_posts_column_open' ) ) {
	 
	function kalium_blog_archive_posts_column_open() {
		
		echo '<div class="column column--posts">';
	}
}

/**
 * Blog archive, posts column wrapper close
 */
if ( ! function_exists( 'kalium_blog_archive_posts_column_close' ) ) {
	
	function kalium_blog_archive_posts_column_close() {
		echo '</div>';
	}
}

/**
 * Blog archive sidebar
 */
if ( ! function_exists( 'kalium_blog_sidebar_loop' ) ) {
	
	function kalium_blog_sidebar_loop() {
		
		if ( kalium_blog_get_option( 'loop/sidebar/visible' ) ) :
				
			?>
			<div class="column column--sidebar">
				
				<?php
					// Show widgets
					kalium_get_widgets( 'blog_sidebar', 'blog-archive--widgets' );
				?>
				
			</div>
			<?php
			
		endif;
	}
}

/**
 * Loop post thumbnail
 */
if ( ! function_exists( 'kalium_blog_post_thumbnail' ) ) {
	
	function kalium_blog_post_thumbnail() {
		global $post;
		
		// Args
		$args = array();
		
		// Thumbnail size
		$args['thumbnail_size'] = kalium_blog_get_option( 'loop/post_thumbnail/size' );
		
		// Supported post formats
		if ( kalium_blog_get_option( 'loop/post_formats' ) ) {
			$args['post_format_content'] = kalium_extract_post_format_content( $post );
		}
		
		// Show post thumbnails only if they are set to be visible
		if ( kalium_blog_get_option( 'loop/post_thumbnail/visible' ) ) {
			
			if ( kalium_blog_get_option( 'loop/post_thumbnail/placeholder' ) || has_post_thumbnail( $post ) || ! empty( $args['post_format_content'] ) ) {
				kalium_get_template( 'blog/loop/post-thumbnail.php', $args );
			}
		}
	}
}

/**
 * Loop post thumbnail hover layer
 */
if ( ! function_exists( 'kalium_blog_post_hover_layer' ) ) {
	
	function kalium_blog_post_hover_layer() {
		global $post;
		
		$post_format = get_post_format( $post );
		$blog_template = kalium_blog_get_template();
		
		// Args
		$args = array();
		
		// Show hover layer or not
		$show_post_hover_layer = kalium_blog_get_option( 'loop/post_thumbnail/hover/type' );
		
		if ( kalium_blog_get_option( 'loop/post_formats' ) && in_array( $blog_template, array( 'square', 'standard' ) ) && in_array( $post_format, array( 'quote', 'gallery', 'video', 'audio' ) ) ) {
			$show_post_hover_layer = false;
		}
		
		// Hover layer is shown
		if ( $show_post_hover_layer ) {
			
			// Hover layer vars
			$hover_options = kalium_blog_get_option( 'loop/post_thumbnail/hover' );
			
			$hover_type = $hover_options['type'];
			$hover_icon = $hover_options['icon'];
			
			$args['hover_icon'] = $hover_icon;
			
			// Custom Hover Icon
			if ( 'custom' == $hover_icon ) {				
				$atts = array();
				$custom_hover = $hover_options['custom'];
				$attachment_id = $custom_hover['image_id'];
				
				// Icon width
				$hover_icon_custom_width = $custom_hover['width'];
				
				if ( is_numeric( $hover_icon_custom_width ) ) {
					$attachment = wp_get_attachment_image_src( $attachment_id, 'original' );
					$hover_icon_custom_height = absint( $attachment[2] * ( $hover_icon_custom_width / $attachment[1] ) );
					
					$atts['style'] = "width: {$hover_icon_custom_width}px; height: {$hover_icon_custom_height}px;";
				}
				
				// Custom hover icon
				$hover_icon_custom = wp_get_attachment_image( $attachment_id, 'original', null, $atts );
				
				$args['hover_icon_custom'] = $hover_icon_custom;
			}
			
			// Hover layer classes
			$classes = array( 'post-hover' );
			
			// Hover layer with no opacity
			if ( in_array( $hover_type, array( 'full-cover-no-opacity', 'distanced-no-opacity' ) ) ) {
				$classes[] = 'post-hover--no-opacity';
			}
			
			// Hover layer with spacing
			if ( in_array( $hover_type, array( 'distanced', 'distanced-no-opacity' ) ) ) {
				$classes[] = 'post-hover--distanced';
			}
			
			$args['classes'] = $classes;
			
			kalium_get_template( 'blog/loop/post-thumbnail-hover.php', $args );
		}
	}
}

/**
 * Blog post format icon
 */
if ( ! function_exists( 'kalium_blog_post_format_icon' ) ) {
	
	function kalium_blog_post_format_icon() {
		global $post;
			
		if ( kalium_blog_get_option( 'loop/post_format_icon' ) ) {
			$post_format = get_post_format( $post );
			
			// Args
			$args = array();
			
			// Default post icon
			$icon = 'icon icon-basic-sheet-txt';
			
			// Available icons
			$post_format_icons = array(
				'quote'   => 'fa fa-quote-left',
				'video'   => 'icon icon-basic-video',
				'audio'   => 'icon icon-music-note-multiple',
				'link'    => 'icon icon-basic-link',
				'image'   => 'icon icon-basic-photo',
				'gallery' => 'icon icon-basic-picture-multiple',
			);
			
			if ( $post_format && isset( $post_format_icons[ $post_format ] ) ) {
				$icon = $post_format_icons[ $post_format ];
			}
			
			$args['icon'] = $icon;
			
			kalium_get_template( 'blog/loop/post-icon.php', $args );
		}
	}
}

/**
 * Blog post title
 */
if ( ! function_exists( 'kalium_blog_post_title' ) ) {
	
	function kalium_blog_post_title() {
		$heading_tag = is_single() ? 'h1' : 'h3';
		
		// Args
		$args = array(
			'heading_tag_open' => sprintf( '<%s class="post-title entry-title">', $heading_tag ),
			'heading_tag_close' => sprintf( '</%s>', $heading_tag ),
		);
		
		if ( is_single() ) {
			$show_post_title = kalium_blog_get_option( 'single/post_title' );
		} else {
			$show_post_title = kalium_blog_get_option( 'loop/post_title' );
		}
		
		// Show title
		if ( $show_post_title ) {
		
			kalium_get_template( 'blog/post-title.php', $args );
		}
	}
}

/**
 * Blog post excerpt
 */
if ( ! function_exists( 'kalium_blog_post_excerpt' ) ) {
	
	function kalium_blog_post_excerpt() {
		
		if ( kalium_blog_get_option( 'loop/post_excerpt' ) ) :
			
			?>
			<div class="post-excerpt entry-summary">
				<?php the_excerpt(); ?>
			</div>
			<?php
			
		endif;
	}
}

/**
 * Blog post content
 */
if ( ! function_exists( 'kalium_blog_post_content' ) ) {
	
	function kalium_blog_post_content() {
		
		if ( is_single() ) {
			$show_post_content = kalium_blog_get_option( 'single/post_content' );
		} else {
			$show_post_content = kalium_blog_get_option( 'loop/post_excerpt' );
		}
		
		if ( $show_post_content ) :
			
			?>
			<section class="post-content post-formatting">
				<?php 
					// Post content
					echo apply_filters( 'the_content', apply_filters( 'kalium_blog_post_content', get_the_content() ) );
					
					// Post content pagination
					if ( is_single() ) {
						wp_link_pages( array(
							'before'              => '<div class="pagination pagination--post-pagination">',
							'after'               => '</div>',
							'next_or_number'      => 'next',
							'previouspagelink'    => sprintf( '%2$s %1$s', __( 'Previous page', 'kalium' ), '&laquo;' ),
							'nextpagelink'        => sprintf( '%1$s %2$s', __( 'Next page', 'kalium' ), '&raquo;' ),
						) );
					}
				?>
			</section>
			<?php
			
		endif;
	}
}

/**
 * Blog post date
 */
if ( ! function_exists( 'kalium_blog_post_date' ) ) {
	
	function kalium_blog_post_date() {
		
		if ( is_single() ) {
			$show_post_date = kalium_blog_get_option( 'single/post_date' );
		} else {
			$show_post_date = kalium_blog_get_option( 'loop/post_date' );
		}
		
		if ( $show_post_date ) :
		
			?>
			<div class="post-meta date updated published">
				<i class="icon icon-basic-calendar"></i>
				<?php the_time( apply_filters( 'kalium_post_date_format', get_option( 'date_format' ) ) ); ?>
			</div>
			<?php
			
		endif;
	}
}
	
/**
 * Blog post category
 */
if ( ! function_exists( 'kalium_blog_post_category' ) ) {

	function kalium_blog_post_category() {
		if ( is_single() ) {
			$show_post_category = kalium_blog_get_option( 'single/post_category' );
		} else {
			$show_post_category = kalium_blog_get_option( 'loop/post_category' );
		}
		
		if ( $show_post_category && has_category() ) :
		
			?>
			<div class="post-meta category">
				<i class="icon icon-basic-folder-multiple"></i>
				<?php the_category( ', ' ); ?>
			</div>
			<?php
				
		endif;
	}
}
	
/**
 * Blog post tags
 */
if ( ! function_exists( 'kalium_blog_post_tags' ) ) {

	function kalium_blog_post_tags() {
		$show_post_tags = kalium_blog_get_option( 'loop/post_tags' );
		
		if ( $show_post_tags ) :
		
			?>
			<div class="post-meta tags">
				<i class="icon icon-basic-folder-multiple"></i>
				<?php the_tags( ', ' ); ?>
			</div>
			<?php
				
		endif;
	}
}

/**
 * Loading indicator for columned standard
 */
if ( ! function_exists( 'kalium_blog_loop_loading_posts_indicator' ) ) {
	
	function kalium_blog_loop_loading_posts_indicator() {
		?>
		<div class="loading-posts">
			<?php _e( 'Loading posts...', 'kalium' ); ?>
		</div>
		<?php
	}
}

/**
 * Archive page container class
 */
if ( ! function_exists( 'kalium_blog_container_class' ) ) {
	
	function kalium_blog_container_class( $_classes = array() ) {
		$classes = array( 'blog' );
		
		// Blog template
		$classes[] = sprintf( 'blog--%s', kalium_blog_get_template() );
		
		// Extra classes
		if ( ! empty( $_classes ) && is_array( $_classes ) ) {
			$classes = array_merge( $classes, $_classes );
		}
		
		echo sprintf( 'class="%s"', kalium()->helpers->showClasses( apply_filters( 'kalium_blog_container_class', $classes ) ) );
	}
}

/**
 * Single post container class
 */
if ( ! function_exists( 'kalium_blog_single_container_class' ) ) {
	
	function kalium_blog_single_container_class( $_classes = array() ) {
		$classes = array( 'single-post' );
		
		// Extra classes
		if ( ! empty( $_classes ) && is_array( $_classes ) ) {
			$classes = array_merge( $classes, $_classes );
		}
		
		echo sprintf( 'class="%s"', kalium()->helpers->showClasses( apply_filters( 'kalium_blog_single_container_class', $classes ) ) );
	}
}

/**
 * Single post layout
 */
if ( ! function_exists( 'kalium_blog_single_post_layout' ) ) {
	
	function kalium_blog_single_post_layout() {
		// Args
		$args = array();
		
		kalium_get_template( 'blog/single.php', $args );
	}
}
 
/**
 * Single post classes filter
 */
if ( ! function_exists( 'kalium_blog_single_container_classes' ) ) {
	
	function kalium_blog_single_container_classes( $classes ) {
			
		// Post author placement
		if ( kalium_blog_get_option( 'single/author/visible' ) ) {
			$author_placement = kalium_blog_get_option( 'single/author/placement' );
			
			$classes[] = 'single-post--has-author-info';
			
			if ( in_array( $author_placement, array( 'left', 'right' ) ) ) {
				$classes[] = 'author-info--alignment-horizontal';
			}
			
			$classes[] = sprintf( 'author-info--alignment-%s', $author_placement );
		}
		
		// Sidebar
		if ( kalium_blog_get_option( 'single/sidebar/visible' ) ) {
			$sidebar_alignment = kalium_blog_get_option( 'single/sidebar/alignment' );
			
			$classes[] = 'single-post--has-sidebar';
			$classes[] = sprintf( 'single-post--sidebar-alignment-%s', $sidebar_alignment );
		}
		
		return $classes;
	}
}

/**
 * Single post tags list
 */
if ( ! function_exists( 'kalium_blog_single_post_tags_list' ) ) {
	
	function kalium_blog_single_post_tags_list() {
		
		if ( kalium_blog_get_option( 'single/post_tags' ) && has_tag() ) :
		
			?>
			<section class="post-tags">
				
				<?php the_tags( '', ' ', '' ); ?>
				
			</section>
			<?php
			
		endif;
	}
}

/**
 * Single post share networks
 */
if ( ! function_exists( 'kalium_blog_single_post_share_networks' ) ) {
	
	function kalium_blog_single_post_share_networks() {
		
		if ( kalium_blog_get_option( 'single/share/visible' ) ) :
		
			$share_networks = kalium_get_enabled_options( get_data( 'blog_share_story_networks' ) );
			$share_style = kalium_blog_get_option( 'single/share/style' );
			
			?>
			<section class="<?php printf( 'post-share-networks post-share-networks--style-%s', $share_style ); ?>">
				
				<div class="share-title">
					<?php _e( 'Share:', 'kalium' ); ?>
				</div>
				
				<div class="networks-list">
				<?php
					foreach ( $share_networks as $network_id => $network_name ) {
						share_story_network_link( $network_id, null, '', 'icons' == $share_style );
					}
					
				?>
				</div>
				
			</section>
			<?php
			
		endif;
	}
}

/**
 * Post author info
 */
if ( ! function_exists( 'kalium_blog_single_post_author_info' ) ) {
	
	function kalium_blog_single_post_author_info() {
		global $wp_roles;
	
		$author_id           = get_the_author_meta( 'ID' );
		$userdata            = get_userdata( $author_id );
		
		$author_description  = get_the_author_meta( 'description' );
		$author_url          = get_author_posts_url( $author_id );
		$author_avatar		 = get_avatar_url( $author_id, array( 'size' => 192 ) );
		
		$role_name           = $wp_roles->roles[ current( $userdata->roles ) ]['name'];
		
		$link_target 		 = '_self';
		
		if ( $_author_url = get_the_author_meta( 'url' ) ) {
			$author_url = $_author_url;
			$link_target = '_blank';
		}
		
		$author_url			 = apply_filters( 'kalium_blog_single_author_url', $author_url );
		
		?>
		<div class="author-info">
			
			<?php if ( apply_filters( 'kalium_blog_single_post_author_info_show_image', true ) ) : ?>
			<div class="author-info--image">
				<a href="<?php echo esc_url( $author_url ); ?>" target="<?php echo $link_target; ?>">
					<?php echo kalium_get_attachment_image( $author_avatar ); ?>
				</a>
			</div>
			<?php endif; ?>
			
			<div class="author-info--details">
				<a href="<?php echo esc_url( $author_url ); ?>" class="vcard author author-name" target="<?php echo $link_target; ?>">
					<span class="fn"><?php the_author() ?></span>
					
					<?php if ( apply_filters( 'kalium_blog_single_post_author_info_show_subtitle', true ) ) : ?>
						<em><?php echo apply_filters( 'kalium_blog_single_post_author_info_subtitle', $role_name ); ?></em>
					<?php endif; ?>
				</a>
			
				<?php
					/**
					 * Other author info details
					 *
					 * @hooked none
					 */
					do_action( 'kalium_blog_single_post_author_info_details', $author_id, $userdata );
				?>
			</div>
			
		</div>
		<?php
	}
}

/**
 * Single post author and meta aside
 */
if ( ! function_exists( 'kalium_blog_single_post_author_and_meta_aside' ) ) {
	
	function kalium_blog_single_post_author_and_meta_aside() {
		
		if ( kalium_blog_get_option( 'single/author/visible' ) && in_array( kalium_blog_get_option( 'single/author/placement' ), array( 'left', 'right' ) ) ) :
		
			?>
			<aside class="post--column post-author-meta">
				
				<?php
					/**
					 * Author post info
					 */
					kalium_blog_single_post_author_info();
				?>
				
				<?php
					/**
					 * Post meta (date, category and other stuff)
					 *
					 * @hooked kalium_blog_post_date - 10
					 * @hooked kalium_blog_post_category - 20
					 */
					do_action( 'kalium_blog_single_post_meta' );
				?>
				
			</aside>
			<?php
				
		endif;
	}
}

/**
 * Post meta below the title
 */
if ( ! function_exists( 'kalium_blog_single_post_meta_below_title' ) ) {
	
	function kalium_blog_single_post_meta_below_title() {
		
		if ( ! kalium_blog_get_option( 'single/author/visible' ) || 'bottom' == kalium_blog_get_option( 'single/author/placement' ) ) :
		
			?>
			<section class="post-meta-only">
								
				<?php
					/**
					 * Post meta (date, category and other stuff)
					 *
					 * @hooked kalium_blog_post_date - 10
					 * @hooked kalium_blog_post_category - 20
					 */
					do_action( 'kalium_blog_single_post_meta' );
				?>
					
			</section>
			<?php
			
		endif;
	}
}

/**
 * Single post author below the article
 */
if ( ! function_exists( 'kalium_blog_single_post_author_info_below' ) ) {
	
	function kalium_blog_single_post_author_info_below() {
		
		if ( kalium_blog_get_option( 'single/author/visible' ) && 'bottom' == kalium_blog_get_option( 'single/author/placement' ) ) :
		
			?>
			<section class="post-author">
				
				<?php
					/**
					 * Author post info
					 */
					kalium_blog_single_post_author_info();
				?>
				
			</section>
			<?php
				
		endif;
	}
}
	
/**
 * Single post sidebar
 */
if ( ! function_exists( 'kalium_blog_single_post_sidebar' ) ) {
	
	function kalium_blog_single_post_sidebar() {
		
		if ( kalium_blog_get_option( 'single/sidebar/visible' ) ) :
		
			?>
			<aside class="post-sidebar">
				
				<?php
					// Post sidebar
					$sidebar_id = 'blog_sidebar_single';
					
					if ( ! is_active_sidebar( $sidebar_id ) ) {
						$sidebar_id = 'blog_sidebar';
					}
					
					kalium_get_widgets( $sidebar_id, 'single-post--widgets' );
				?>
				
			</aside>
			<?php
				
		endif;
	}
}

/**
 * Single post author description when its shown below
 */
if ( ! function_exists( 'kalium_blog_single_post_author_info_description' ) ) {
	
	function kalium_blog_single_post_author_info_description( $author_id, $userdata ) {
		
		if ( 'bottom' == kalium_blog_get_option( 'single/author/placement' ) ) :
			$description = get_the_author_meta( 'description', $author_id );
			
			if ( $description ) :
			
				?>
				<div class="author-info--description">
					
					<?php echo wpautop( $description ); ?>
					
				</div>
				<?php
				
			endif;
			
		endif;
	}
}

/**
 * Single post image or post format content
 */
if ( ! function_exists( 'kalium_blog_single_post_image' ) ) {
	
	function kalium_blog_single_post_image() {
		global $post;
		
		$show_post_image = kalium_blog_get_option( 'single/post_image/visible' );
		
		if ( $show_post_image ) {
		
			// Args
			$args = array();
			
			$args['post'] = get_post();
			$args['thumbnail_size'] = kalium_blog_get_option( 'single/post_image/size' );
					
			// Supported post formats
			if ( kalium_blog_get_option( 'post_formats' ) ) {
				$args['post_format_content'] = kalium_extract_post_format_content( $post );
			}
			
			// Enqueue slider for post image
			if ( apply_filters( 'kalium_blog_single_post_image_lightbox', true ) ) {
				kalium_enqueue_lightbox_library();
			}
			
			// Show only if there is post image or post format content
			if ( has_post_thumbnail() || ! empty( $args['post_format_content'] ) ) {			
				kalium_get_template( 'blog/single/post-image.php', $args );
			}
		}
	}
}

/**
 * Single post image in full-width format
 */
if ( ! function_exists( 'kalium_blog_single_post_image_full_width' ) ) {
	
	function kalium_blog_single_post_image_full_width() {
		
		$show_post_image = kalium_blog_get_option( 'single/post_image/visible' );
		
		if ( $show_post_image && 'full-width' == kalium_blog_get_option( 'single/post_image/placement' ) ) :
			
			?>
			<section <?php post_class( array( 'post--full-width-image' ) ); ?>>
				<?php
					kalium_blog_single_post_image();
				?>
			</section>
			<?php
				
		endif;
	}
}

/**
 * Single post image in boxed format
 */
if ( ! function_exists( 'kalium_blog_single_post_image_boxed' ) ) {
	
	function kalium_blog_single_post_image_boxed() {
		
		if ( 'boxed' == kalium_blog_get_option( 'single/post_image/placement' ) ) {
			kalium_blog_single_post_image();
		}
	}
}

/**
 * Single post prev and next navigation
 */
if ( ! function_exists( 'kalium_blog_single_post_prev_next_navigation' ) ) {
	
	function kalium_blog_single_post_prev_next_navigation() {
		
		if ( kalium_blog_get_option( 'single/prev_next' ) ) {
				
			// Args
			$args = array();
			
			$adjacent_post_args = apply_filters( 'kalium_blog_single_post_prev_next_navigation', array(
				'return' => 'id',
				'loop' => true
			) );
			
			$prev_id = previous_post_link_plus( $adjacent_post_args );
			$next_id = next_post_link_plus( $adjacent_post_args );
			
			// Previous link
			if ( $prev_id ) {
				$prev = get_post( $prev_id );
				$args['prev'] = $prev;
				$args['prev_title'] = __( 'Older Post', 'kalium' );
			}
			
			// Next link
			if ( $next_id ) {
				$next = get_post( $next_id );
				$args['next'] = $next;
				$args['next_title'] = __( 'Newer Post', 'kalium' );
			}
			
			kalium_get_template( 'global/post-navigation.php', $args );
		}
	}
}

/**
 * Single post comments
 */
if ( ! function_exists( 'kalium_blog_single_post_comments' ) ) {
	
	function kalium_blog_single_post_comments() {
		
		if ( apply_filters( 'kalium_blog_comments', true ) && false == post_password_required() ) {
		
			comments_template();
		}
	}
}

/**
 * Comment entry callback (open)
 */
if ( ! function_exists( 'kalium_blog_post_comment_open' ) ) {
	
	function kalium_blog_post_comment_open( $comment, $args, $depth ) {
		// User avatar
		$comment_avatar = get_avatar( $comment );
		
		// Date format
		$date_format = get_option( 'date_format', 'F d, Y' );
		
		// Time format
		$time_format = get_option( 'time_format', 'h:m A' );
		
		$comment_date = apply_filters( 'kalium_blog_post_comment_date', sprintf( _x( '%s at %s', 'comment submit date', 'kalium' ), get_comment_date( $date_format ), get_comment_date( $time_format ) ), $comment );
		
		// Parent comment
		$parent_comment_id = $comment->comment_parent;
	
		// In reply to
		$parent_comment = $parent_comment_id ? get_comment( $parent_comment_id ) : null;
		
		// Commenter image
		$commenter_image = get_comment_author_url() ? sprintf( '<a href="%s">%s</a>', get_comment_author_url(), $comment_avatar ) : $comment_avatar;
		
		if ( $parent_comment ) {
			$commenter_image .= '<div class="comment-connector"></div>';
		}
	
		?>
		<div <?php comment_class(); ?> id="comment-<?php comment_id(); ?>"<?php when_match( null !== $parent_comment, sprintf( 'data-replied-to="comment-%d"', $parent_comment_id ) ); ?>>
				
			<div class="commenter-image">
				
				<?php 
					// Comment avatar
					echo $commenter_image;
				?>
				
			</div>
			
			<div class="commenter-details">
				
				<div class="name">
					
					<?php
						// Comment Author
						comment_author();
		
						// Reply Link
						comment_reply_link( array_merge( $args, array(
							'reply_text'   => __( 'reply', 'kalium' ),
							'depth'        => $depth,
							'max_depth'    => $args['max_depth'],
							'before'       => ''
						) ) );
					?>
					
				</div>
	
				<div class="date">
					<?php 
						// Comment date
						echo $comment_date;
					?>
	
					<?php 
						// Parent comment (in reply to)
						if ( $parent_comment ) : 
						
							?>
							<div class="in-reply-to">
								&ndash; <?php echo sprintf( __( 'In reply to: %s', 'kalium' ), '<span class="replied-to">' . get_comment_author( $parent_comment_id ) . '</span>' ); ?>
							</div>
							<?php
							endif; 
					?>
				</div>
	
				<div class="comment-text post-formatting">
					
					<?php
						// Comment text
						comment_text(); 
					?>
					
				</div>
				
			</div>
			
		</div>
		<?php
	}
}

/**
 * Comment entry callback (close)
 */
if ( ! function_exists( 'kalium_blog_post_comment_close' ) ) {
	
	function kalium_blog_post_comment_close() {
		// Nothing to do
	}
}

/**
 * Add labeled input group class for comment form fields
 */
if ( ! function_exists( 'kalium_blog_comment_form_defaults' ) ) {
	
	function kalium_blog_comment_form_defaults( $defaults ) {

		$defaults['comment_field'] = preg_replace( '/(<p.*?)class="(.*?)"/', '\1class="labeled-textarea-row \2"', $defaults['comment_field'] );

		// Comment attributes
		$total_fields = count( $defaults['fields'] );
		
		foreach ( $defaults['fields'] as & $field ) {
			$field = preg_replace( '/(<p.*?)class="(.*?)"/', '\1class="labeled-input-row \2"', $field );
		}
		
		// Fields class
		$defaults['class_form'] .= sprintf( ' fields-%s', 0 == $total_fields % 2 ? 'odd' : 'even' );
		
		return $defaults;
	}
}

/**
 * Excerpt length when sidebar is present or is single columned
 */
if ( ! function_exists( 'kalium_blog_custom_excerpt_length' ) ) {
	
	function kalium_blog_custom_excerpt_length( $length ) {
		
		if ( kalium_blog_is_in_the_loop() ) {

			// Masonry mode with single column
			if ( 'standard' == kalium_blog_get_template() && 1 === kalium_blog_get_option( 'loop/columns' ) ) {
				return 70;
			}
			
			// Sidebar is present
			if ( kalium_blog_get_option( 'loop/sidebar/visible' ) ) {
				return 32;
			}
		}
		
		return $length;
	}
}
