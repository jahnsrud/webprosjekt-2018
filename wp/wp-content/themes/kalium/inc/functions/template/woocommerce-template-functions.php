<?php
/**
 *	Kalium WordPress Theme
 *
 *	WooCommerce Template Functions
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 *  WooCommerce Init
 */
if ( ! function_exists( 'kalium_woocommerce_init' ) ) {
	
	function kalium_woocommerce_init() {
		
		// Product classes
		add_filter( 'post_class', 'kalium_woocommerce_product_classes', 25, 3 );
		
		// Page title and results count hide
		if ( false == get_data( 'shop_title_show' ) ) {
			add_filter( 'woocommerce_show_page_title', '__return_false' );
			add_filter( 'kalium_woocommerce_show_results_count', '__return_false' );
		}
		
		// Hide sorting
		if ( false == get_data( 'shop_sorting_show' ) ) {
			add_filter( 'kalium_woocommerce_show_product_sorting', '__return_false' );
		}
		
		// Product info (loop)		
		if ( 'default' == kalium_woocommerce_get_catalog_layout() ) {
			add_action( 'woocommerce_after_shop_loop_item', 'kalium_woocommerce_product_loop_item_info', 25 );
		}		

		// Catalog mode
		if ( kalium_woocommerce_is_catalog_mode() ) {
			add_filter( 'get_data_shop_add_to_cart_listing', '__return_false' );
			
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			add_action( 'woocommerce_single_product_summary', 'kalium_woocommerce_catalog_mode_add_to_cart_options', 30 );
			
			if ( get_data( 'shop_catalog_mode_hide_prices' ) ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 29 );
				add_filter( 'get_data_shop_product_price_listing', '__return_false' );
			}
		}
		
		// Single product Kalium image gallery
		if ( kalium_woocommerce_use_custom_product_gallery_layout() ) {
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			add_action( 'kalium_woocommerce_single_product_images', 'kalium_woocommerce_show_product_images_custom_layout', 20 );
			add_filter( 'woocommerce_available_variation', 'kalium_woocommerce_variation_image_handler', 10, 3 );
		}
		
		// Social network share links
		if ( get_data( 'shop_single_share_product' ) ) {
			add_action( 'woocommerce_single_product_summary', 'kalium_woocommerce_share_product', 50 );
		}
		
		// Hide Related Products
		if ( 0 == get_data( 'shop_related_products_per_page' ) ) {
			remove_filter( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}
		
		// Category image size
		add_filter( 'subcategory_archive_thumbnail_size', 'kalium_woocommerce_subcategory_archive_thumbnail_size' );
		
		if ( ( $category_image_size = get_data( 'shop_category_image_size' ) ) && preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $category_image_size ) ) {
			add_filter( 'subcategory_archive_thumbnail_size', kalium_hook_return_value( $category_image_size ), 100 );
		}
		
		// Custom image size for single product images
		if ( get_data( 'shop_single_product_custom_image_size' ) ) {
			add_filter( 'woocommerce_get_image_size_single', 'kalium_woocommerce_get_custom_image_size_single' );
		}
	}
}

/**
 *  Archive wrapper before
 */
if ( ! function_exists( 'kalium_woocommerce_archive_wrapper_start' ) ) {
	
	function kalium_woocommerce_archive_wrapper_start() {
		
		// Show on archive and product taxonomy page
		if ( ! ( is_shop() || is_product_taxonomy() ) ) {
			return;
		}
		
		$shop_sidebar = kalium_woocommerce_get_sidebar_position();
		
		$products_archive_classes = array( 'products-archive' );
		
		// Shop sidebar
		if ( in_array( $shop_sidebar, array( 'left', 'right' ) ) ) {
			$products_archive_classes[] = 'products-archive--has-sidebar';
			
			if ( 'left' == $shop_sidebar ) {
				$products_archive_classes[] = 'products-archive--sidebar-left';
			}
			
			// Sidebar order or mobile devices
			if ( get_data( 'shop_sidebar_before_products_mobile' ) ) {
				$products_archive_classes[] = 'products-archive--sidebar-first';
			}
		}
		
		// Masonry layout
		$layout_mode = get_data( 'shop_loop_masonry_layout_mode' );
		$products_archive_classes[] = 'products-archive--masonry';
		
		if ( 'fitRows' == $layout_mode ) {
			$products_archive_classes[] = 'products-archive--fitrows';
		}
		
		// Normal pagination
		$pagination_alignment = get_data( 'shop_pagination_position' );
		$products_archive_classes[] = 'products-archive--pagination-align-' . $pagination_alignment;
		
		?>
		<div class="<?php echo implode( ' ', $products_archive_classes ); ?>">
			
			<div class="products-archive--products">
		<?php
	}
}
	
/**
 *  Archive wrapper after
 */
if ( ! function_exists( 'kalium_woocommerce_archive_wrapper_end' ) ) {
	
	function kalium_woocommerce_archive_wrapper_end() {
		
		// Show on archive and product taxonomy page
		if ( ! ( is_shop() || is_product_taxonomy() ) ) {
			return;
		}
		
		?>
			</div>
			
			<?php if ( kalium_woocommerce_get_sidebar_position() ) : ?>
			
				<div class="products-archive--sidebar">
					
					<?php
						// Shop Widgets
						kalium_get_widgets( 'shop_sidebar', 'products-archive--widgets' );
					?>
					
				</div>
				
			<?php endif; ?>
		
		</div>
			
		<?php
	}
}

/**
 * Single product images wrapper  
 */
if ( ! function_exists( 'kalium_woocommerce_single_product_images_wrapper_start' ) ) {

	function kalium_woocommerce_single_product_images_wrapper_start() {
		
		// Gallery wrapper start
		echo '<div class="single-product-images">';
			
		// Kalium's default product image gallery
		do_action( 'kalium_woocommerce_single_product_images' );
	}
}

if ( ! function_exists( 'kalium_woocommerce_single_product_images_wrapper_end' ) ) {
	
	function kalium_woocommerce_single_product_images_wrapper_end() {
		
		// Gallery wrapper end
		echo '</div>';
	}
}

/**
 * Get product image for Kalium image gallery
 */
if ( ! function_exists( 'kalium_woocommerce_get_product_image' ) ) {
	
	function kalium_woocommerce_get_single_product_image( $attachment_id, $image_size, $lightbox_link = false ) {
		$image_post = get_post( $attachment_id );
		
		if ( is_null( $image_post ) ) {
			return '';
		}
		
		$image_title = $image_post->post_content;
		
		$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
		
		$attributes = array(
			'title'                   => get_post_field( 'post_title', $attachment_id ),
			'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
			'data-src'                => $full_size_image[0],
			'data-large_image'        => $full_size_image[0],
			'data-large_image_width'  => $full_size_image[1],
			'data-large_image_height' => $full_size_image[2],
		);
		
		// Thumbnail
		$image = kalium_get_attachment_image( $attachment_id, $image_size, $attributes );
		
		// Product link image classes
		$product_link_image_classes = implode( ' ', apply_filters( 'kalium_woocommerce_single_product_link_image_classes', array( 'wow' ) ) );
		
		// HTML image object
		$html  = '<div class="woocommerce-product-gallery__image">';
		$html .= sprintf( '<a href="%s" class="%s">', esc_url( $full_size_image[0] ), esc_attr( $product_link_image_classes ) );
		$html .= $image;
		$html .= '</a>';
		
		// Add image lightbox open link
		$html .= $lightbox_link ? kalium_woocommerce_get_lightbox_trigger_button( $attachment_id ) : '';
		
		$html .= '</div>';
		
		return $html;
	}
}

/**
 *  Shop Archive before main content
 */
if ( ! function_exists( 'kalium_woocommerce_archive_header' ) ) {

	function kalium_woocommerce_archive_header() {
		// Show on archive and product taxonomy page
		if ( ! ( is_shop() || is_product_taxonomy() ) ) {
			return;
		}
		
		$show_page_title = apply_filters( 'woocommerce_show_page_title', true );
		$show_ordering = apply_filters( 'kalium_woocommerce_show_product_sorting', true );
		$show_shop_header = $show_page_title || $show_ordering;
		
		if ( $show_shop_header ) {
			do_action( 'kalium_woocommerce_archive_header', $show_page_title, $show_ordering );
		}
	}
}

/**
 *  WooCommerce Archive Header
 */
if ( ! function_exists( 'kalium_woocommerce_archive_header_display' ) ) {
		
	function kalium_woocommerce_archive_header_display( $show_title, $show_ordering ) {
		
		// Classes
		$classes = array( 'woocommerce-shop-header', 'woocommerce-shop-header--columned' );
		?>	
		<header class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			
			<?php if ( $show_title ) : ?>
			<div class="woocommerce-shop-header--title woocommerce-products-header">
	
				<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		
					<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
		
				<?php endif; ?>
		
				<?php
					/**
					 * Archive description below title
					 */
					do_action( 'kalium_woocommerce_archive_description' );
				?>
				
			</div>
			<?php endif; ?>
			
			<?php if ( $show_ordering ) : ?>
			<div class="woocommerce-shop-header--sorting">
				
				<?php
					/**
					 * Shop archive product sorting
					 */
					woocommerce_catalog_ordering();
					
			   ?>
			   
			</div>
			<?php endif; ?>
	
		</header>
		<?php
	}
}

/**
 * Ordering dropdown for products loop
 */
if ( ! function_exists( 'kalium_woocommerce_shop_loop_ordering_dropdown' ) ) {
	
	function kalium_woocommerce_shop_loop_ordering_dropdown( $catalog_orderby_options, $orderby ) {
		
		$selected = '';
		$options = '';
		
		foreach ( $catalog_orderby_options as $id => $name ) {
			$atts = '';
			
			if ( $orderby == $id ) {
				$selected = $name;
				$atts = ' class="active"';
			}
			
			$options .= sprintf( '<li role="presentation"%3$s><a href="#%1$s">%2$s</a></li>', $id, esc_html( $name ), $atts );
		}
		
		?>
		<div class="woocommerce-ordering--dropdown form-group sort">
			
			<div class="dropdown">
				
				<button class="dropdown-toggle" type="button" data-toggle="dropdown">
					<span><?php echo esc_html( $selected ); ?></span>
					<i class="flaticon-bottom4"></i>
				</button>
	
				<ul class="dropdown-menu fade" role="menu">
				
					<?php
						/**
						 * Ordering options
						 */
						echo $options;
					?>	
				
				</ul>
				
			</div>
		</div>
		<?php
	}
}

/**
 * Product classes
 */
if ( ! function_exists( 'kalium_woocommerce_product_classes' ) ) {
	
	function kalium_woocommerce_product_classes( $classes, $class = '', $post_id = '' ) {
		
		if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ), true ) ) {
			return $classes;
		}
		
		$product = wc_get_product( $post_id );
	
		if ( $product ) {
			$is_single_product = is_product() && $post_id === get_the_id();
			
			// Product class
			$classes[] = 'product';
			
			// Product layout type
			$classes[] = 'catalog-layout-' . kalium_woocommerce_get_catalog_layout();
			
			// Products per row small width devices
			if ( ! $is_single_product ) {
				$classes[] = 'columns-xs-' . kalium_woocommerce_products_per_row_on_mobile();
			}
			
			// Single product classes
			if ( $is_single_product ) {
				$classes[] = 'product-images-columns-' . kalium_woocommerce_get_product_gallery_container_width();
				$classes[] = 'product-images-align-' . kalium_woocommerce_get_product_gallery_container_alignment();
			}
		}
		
		return $classes;
	}
}

/**
 * Display WooCommerce archive description
 */
if ( ! function_exists( 'kalium_display_woocommerce_archive_description' ) ) {
	
	function kalium_display_woocommerce_archive_description() {
		do_action( 'woocommerce_archive_description' );
	}
}

/**
 * Infinite pagination
 */
if ( ! function_exists( 'kalium_woocommerce_infinite_scroll_pagination' ) ) {
	
	function kalium_woocommerce_infinite_scroll_pagination() {
		global $wp_query;
		
		if ( $wp_query->is_main_query() && 0 == $wp_query->post_count ) {
			return;
		}
		
		// Disable infinite scroll pagination when WC_Prdctfltr pagination is used
		if ( class_exists( 'WC_Prdctfltr' ) && 'yes' == get_option( 'wc_settings_prdctfltr_use_ajax', 'no' ) && 'default' != get_option( 'wc_settings_prdctfltr_pagination_type', 'default' ) ) {
			return;
		}
		
		$pagination_type = get_data( 'shop_pagination_type' );
		$pagination_style = get_data( 'shop_endless_pagination_style' );
		$pagination_position = get_data( 'shop_pagination_position' );
		
		if ( in_array( $pagination_type, array( 'endless', 'endless-reveal' ) ) ) {
			
			$post_type = 'products';
			
			// Args
			$args = array();
					
			$args['id'] = $post_type;
			$args['show_more_text'] = __( 'Show more', 'kalium' );
			$args['all_items_shown_text'] = __( 'No more products to show', 'kalium' );
			$args['loading_style'] = '_2' == $pagination_style ? 'pulsating' : 'spinner';
					
			// Endless pagination instance (JS)
			$query = $GLOBALS['wp_query'];
			$max_num_pages = $query->max_num_pages;
			$posts_per_page = $query->query_vars['posts_per_page'];
			$found_posts = absint( $query->found_posts );
			
			// Pagination is not needed
			if ( $found_posts <= $posts_per_page ) {
				return;
			}
			
			// Infinite scroll button
			kalium_get_template( 'global/pagination-infinite-scroll.php', $args );
			
			// Infinite scroll JS data
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
				'loop_handler'	 => 'Kalium_WooCommerce::paginationHandler',
				
				// Action and callback
				'callback'		 => 'Kalium.WooCommerce.handleInfiniteScrollResponse',
				
				// Extra arguments (passed on Ajax Request)
				'args' 			 => array(
				)
			);
			
			kalium_infinite_scroll_pagination_js_object( $post_type, $infinite_scroll_pagination_args );
			
			// Remove pagination links
			remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
		}
	}
}

/**
 *  Category image size
 */
if ( ! function_exists( 'kalium_woocommerce_subcategory_archive_thumbnail_size' ) ) {
	
	function kalium_woocommerce_subcategory_archive_thumbnail_size() {
		return 'shop-category-thumb';
	}
}

/**
 *  Maybe show product categories
 */
if ( ! function_exists( 'kalium_woocommerce_maybe_show_product_categories' ) ) {
	
	function kalium_woocommerce_maybe_show_product_categories() {
		wc_set_loop_prop( 'loop', 0 );
		
		$categories = woocommerce_maybe_show_product_subcategories( '' );
		
		if ( trim( $categories ) ) {
			$classes = array( 'products', 'shop-categories' );
			$classes[] = 'columns-' . kalium_woocommerce_get_category_columns();
			
			printf( '<div class="%s">%s</div>', kalium()->helpers->showClasses( $classes, false ), $categories );
		}
	}
}

/**
 *  Loop product images
 */
if ( ! function_exists( 'kalium_woocommerce_catalog_loop_thumbnail' ) ) {

	function kalium_woocommerce_catalog_loop_thumbnail() {
		global $product;
		
		// Product images and settings
		$product_image_classes = array( 'product-images' );
		$attachment_id = get_post_thumbnail_id(); // featured image
		$attachment_ids = $product->get_gallery_image_ids();
		
		if ( ! empty( $attachment_ids ) ) {
			$attachment_ids = array_unique( $attachment_ids );
			
			// Remove featured image fom attachments array
			$remove_index = array_search( $attachment_id, $attachment_ids );
			
			if ( false !== $remove_index ) {
				unset( $attachment_ids[ $remove_index ] );
			}
		}
		
		// Use images from gallery if there is no featured image assigned
		if ( false == has_post_thumbnail() ) {
			// Use an image from gallery if possible
			if ( false == empty( $attachment_ids ) ) {
				$attachment_id = array_shift( $attachment_ids );
			} else {
				$attachment_id = wc_placeholder_img_src();
			}
		}
		
		// Catalog thumbnails layout
		$shop_catalog_layout = kalium_woocommerce_get_catalog_layout();
		
		// Product info on hover
		$product_info_hover = in_array( $shop_catalog_layout, array( 'full-bg', 'distanced-centered', 'transparent-bg' ) );
		
		if ( $product_info_hover ) {
			$product_image_classes[] = 'product-images--internal-details';
		}
		
		?>
		<div <?php kalium_class_attr( $product_image_classes ); ?>>
			
			<?php
				// Product featured image
				echo kalium_woocommerce_get_loop_product_image( $attachment_id );
				
				// Default product layout
				if ( 'default' == $shop_catalog_layout ) {
					
					if ( ! empty( $attachment_ids ) ) {
						
						$image_classes = array( 'gallery-image' );
						
						// Image gallery type
						$image_gallery_type = get_data( 'shop_item_preview_type' );
						
						// Second image on hover
						if ( 'fade' == $image_gallery_type ) {
							$second_image_id = array_shift( $attachment_ids );
							$image_classes[] = 'gallery-image--hoverable';
							
							echo kalium_woocommerce_get_loop_product_image( $second_image_id, $image_classes );
						}
						// Images gallery
						else if ( 'gallery' == $image_gallery_type ) {
							// Allowed gallery images
							if ( $max_gallery_images = apply_filters( 'kalium_woocommerce_catalog_default_gallery_images_length', 5 ) ) {
								if ( $max_gallery_images > 0 ) {
									$attachment_ids = array_slice( $attachment_ids, 0, $max_gallery_images - 1 );
								}
							}
							
							// Show images
							$image_classes[] = 'gallery-image--entry';
							
							foreach ( $attachment_ids as $gallery_image_id ) {
								echo kalium_woocommerce_get_loop_product_image( $gallery_image_id, $image_classes );
							}
							
							// Next and previous buttons
							echo '<a href="#" class="gallery-arrow gallery-prev"><i class="flaticon-arrow427"></i></a>';
							echo '<a href="#" class="gallery-arrow gallery-next"><i class="flaticon-arrow413"></i></a>';
						}
					}
				}
				
				// Full background, distanced background and transparent background
				else if ( $product_info_hover ) {

					echo '<div class="product-internal-info">';
					
					// Product info (hover layer)
					kalium_woocommerce_product_loop_item_info();
					
					echo '</div>';
				}
				?>
			
		</div>
		<?php
	}
}

/**
 * Add "shop-categories" class for products container ([product_categories])
 */
if ( ! function_exists( 'kalium_woocommerce_shortcode_product_categories_wrap' ) ) {
	
	function kalium_woocommerce_product_categories_shortcode_wrap( $output, $tag ) {
		if ( 'product_categories' == $tag ) {
			$output = preg_replace( '/(<ul.*?class=".*?)(".*?>)/', '${1} shop-categories${2}', $output );
		}
		
		return $output;
	}
}

/**
 * Get product image with product link
 */
if ( ! function_exists( 'kalium_woocommerce_get_loop_product_image' ) ) {
	
	function kalium_woocommerce_get_loop_product_image( $attachment_id, $classes = array() ) {
		
		// Image size
		$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
		
		// Get Image
		$image = kalium_get_attachment_image( $attachment_id, $image_size );
		
		// When there is no image
		if ( ! $image ) {
			return '';
		}
		
		ob_start();
						
		// Open link
		woocommerce_template_loop_product_link_open();
		
		// Show image
		echo $image;
		
		// Close link
		woocommerce_template_loop_product_link_close();
		
		$image_html = ob_get_clean();
		
		// Classes
		$classes = is_array( $classes ) || empty( $classes ) ? $classes : array( $classes );
		
		if ( $classes ) {
			$classes = kalium()->helpers->showClasses( $classes );
			$image_html = preg_replace( '/(woocommerce-LoopProduct-link.*?)\"/', '${1} ' . trim( $classes ) . '"', $image_html );
		}
		
		return $image_html;
	}
}

/**
 *  Loop product info
 */
if ( ! function_exists( 'kalium_woocommerce_product_loop_item_info' ) ) {

	function kalium_woocommerce_product_loop_item_info() {
		global $woocommerce, $product, $post;
		
		$shop_catalog_layout = kalium_woocommerce_get_catalog_layout();
		
		#$cart_url = $woocommerce->cart->get_cart_url();
		$cart_url = wc_get_page_permalink( 'cart' );
		$show_price = get_data( 'shop_product_price_listing' );
		
		$shop_product_category = get_data( 'shop_product_category_listing' );
		
		// Product URL
		$product_url = apply_filters( 'kalium_woocommerce_loop_product_link', get_permalink( $post ), $product );
		$link_new_tab = apply_filters( 'kalium_woocommerce_loop_product_link_new_tab', false, $product );
		
		// Full + Transparent Background Layout Type
		if ( in_array( $shop_catalog_layout, array( 'full-bg', 'transparent-bg' ) ) ) :
			?>
			<div class="item-info">
				
				<h3 <?php if ( $shop_catalog_layout == 'transparent-bg' && $shop_product_category == false ) : ?> class="no-category-present"<?php endif; ?>>
					<a href="<?php echo $product_url; ?>"<?php when_match( $link_new_tab, ' target="_blank"' ); ?>><?php the_title(); ?></a>
				</h3>
			
				<?php
					/**
					 * Filters after product title on loop view
					 */
					do_action( 'kalium_woocommerce_product_loop_after_title' ); 
				?>
				
				<?php if ( $shop_product_category ) : ?>
				<div class="product-category">
					<?php echo wc_get_product_category_list( $product->get_id() ); ?>
				</div>
				<?php endif; ?>
				
				
				<div class="product-bottom-details">
					
					<?php if ( $show_price ) : ?>
					<div class="price-column">
						<?php woocommerce_template_loop_price();  ?>
					</div>
					<?php endif; ?>
					
					<?php if ( false == kalium_woocommerce_is_catalog_mode() ) : ?>
					<div class="add-to-cart-column">
						<?php woocommerce_template_loop_add_to_cart(); ?>
					</div>
					<?php endif; ?>
					
				</div>
				
			</div>
			<?php
				
		// Centered – Distanced Background Layout Type
		elseif ( in_array( $shop_catalog_layout, array( 'distanced-centered' ) ) ) :
		
			?>		
			<div class="item-info">
				
				<div class="title-and-price">
					
					<h3>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
				
					<?php
						/**
						 * Filters after product title on loop view
						 */
						do_action( 'kalium_woocommerce_product_loop_after_title' ); 
					?>
					
					<?php if ( $show_price ) : woocommerce_template_loop_price(); endif; ?>
					
				</div>
				
				<?php woocommerce_template_loop_add_to_cart(); ?>
				
			</div>
			<?php
		
		else :
		
			?>
			<div class="item-info">
				
				<div class="item-info-row">
					<div class="title-column">
						<h3>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
					
						<?php
							/**
							 * Filters after product title on loop view
							 */
							do_action( 'kalium_woocommerce_product_loop_after_title' ); 
						?>
						
						<?php woocommerce_template_loop_add_to_cart(); ?>
					</div>
					
					<?php if ( $show_price ) : ?>
					<div class="price-column">
						<?php woocommerce_template_loop_price(); ?>
					</div>
					<?php endif; ?>
				</div>
				
			</div>
			
						
			<div class="added-to-cart-button">
				<a href="<?php echo $cart_url; ?>"><i class="icon icon-ecommerce-bag-check"></i></a>
			</div>
			<?php
			
		endif;
	}
}

/**
 *  Add to cart link replacement
 */
if ( ! function_exists( 'kalium_woocommerce_loop_add_to_cart_link' ) ) {
	
	function kalium_woocommerce_loop_add_to_cart_link( $html, $product, $args ) {
		
		$show_product_categories = get_data( 'shop_product_category_listing' );
		$show_add_to_cart = get_data( 'shop_add_to_cart_listing' );
		
		// Category
		$category = '';
		
		if ( $show_product_categories && 'default' == kalium_woocommerce_get_catalog_layout() ) {
			$category_classes = array( 'product-terms' );
			
			if ( $show_add_to_cart ) {
				$category_classes[] = 'category-hoverable';
			}
			
			$category = sprintf( '<div class="%s">%s</div>', kalium()->helpers->showClasses( $category_classes ), wc_get_product_category_list( $product->get_id() ) );
		}
		
		// Add to cart link
		$add_to_cart = '';
		
		if ( $show_add_to_cart ) {
			$add_to_cart_el = kalium()->helpers->parseAttributes( $html );
			
			// Modify classes
			$add_to_cart_el['attributes']['class'] = "add_to_cart_button cart-{$add_to_cart_el['attributes']['class']}";
			
			// Set added to cart attribute
			$add_to_cart_el['attributes']['data-added_to_cart_text'] = __( 'Added to cart', 'kalium' );
			
			// Build Add to Cart link			
			$add_to_cart = sprintf( '<div class="add-to-cart-link">%s</div>', kalium()->helpers->buildDOMElement( $add_to_cart_el ) );
			
		}
		
		// Add to cart block
		$add_to_cart_link = sprintf( '<div class="product-loop-add-to-cart-container">%1$s%2$s</div>', $category, $add_to_cart );
		
		return $add_to_cart_link;
	}
}

/**
 *  Pagination Next & Prev Labels
 */
if ( ! function_exists( 'kalium_woocommerce_pagination_args' ) ) {
	
	function kalium_woocommerce_pagination_args($args) {
		$args['prev_text'] = '<i class="flaticon-arrow427"></i> ';
		$args['prev_text'] .= __( 'Previous', 'kalium' );
		$args['next_text'] = __( 'Next', 'kalium' );
		$args['next_text'] .= ' <i class="flaticon-arrow413"></i>';
		return $args;
	}
}

/**
 *  Add Kalium style images for variations
 */
if ( ! function_exists( 'kalium_woocommerce_variation_image_handler' ) ) {
	
	function kalium_woocommerce_variation_image_handler( $variation_arr, $variable_product, $variation ) {
		$attachment_id = $variation->get_image_id();
		
		$variation_arr['kalium_image'] = array();
		
		// Product main and thumbmail image
		if ( $attachment_id ) {
			$variation_arr['kalium_image']['main'] = kalium_woocommerce_get_single_product_image( $attachment_id, kalium_woocommerce_get_product_image_size( 'single' ), kalium_woocommerce_is_product_gallery_lightbox_enabled() );
			$variation_arr['kalium_image']['thumb'] = kalium_woocommerce_get_single_product_image( $attachment_id, kalium_woocommerce_get_product_image_size( 'thumbnail' ) );
		}
		
		return $variation_arr;
	}
}

/**
 *  Product Images Layout
 */
if ( ! function_exists( 'kalium_woocommerce_show_product_images_custom_layout' ) ) {

	function kalium_woocommerce_show_product_images_custom_layout( $images_layout_type = 'carousel' ) {
		global $post, $product;
		
		// Attachments
		$attachment_ids = $product->get_gallery_image_ids();
		$shop_single_product_images_layout = get_data( 'shop_single_product_images_layout' );
		
		$images_container_classes = array( 'kalium-woocommerce-product-gallery' );
		$images_container_classes[] = "images-layout-type-{$shop_single_product_images_layout}";
		
		// Is Carousel Type
		$is_carousel = true;
		
		// Toggles
		$zoom_enabled = kalium_woocommerce_is_product_gallery_zoom_enabled();
		$lightbox_enabled = kalium_woocommerce_is_product_gallery_lightbox_enabled();
		
		// Product image setup options
		$single_product_params_js = array(
			'images' => array(),
			
			'zoom' => array(
				'enabled' => $zoom_enabled,
				'options' => array(
					'magnify' => 1
				)
			),
			
			'lightbox' => array(
				'enabled' => $lightbox_enabled,
				'options' => array(
					'shareEl'			   => false,
					'closeOnScroll'		 => false,
					'history'			   => false,
					'hideAnimationDuration' => 0,
					'showAnimationDuration' => 0
				)
			)
		);
		
		// Thumbnail columns
		$thumbnails_columns = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
		
		// Images Carousel
		$images_carousel_classes = array( 'main-product-images' );
		
		// Enqueue carousel library
		kalium_enqueue_slick_slider_library();
		
		// Maybe resize WooCommerce images
		if ( get_data( 'shop_single_product_custom_image_size' ) ) {
			kalium()->woocommerce->maybeResizeImages();
		}
		
		if ( in_array( $shop_single_product_images_layout, array( 'plain', 'plain-sticky' ) ) ) {
			$is_carousel = false; // not carousel product images
			
			$images_carousel_classes[] = 'plain';
			
			// Stretch images to browser edge	
			if ( 'yes' == get_data( 'shop_single_plain_image_stretch' ) ) {
				$images_carousel_classes[] = 'stretched-image';
				$images_carousel_classes[] = 'right' == get_data( 'shop_single_image_alignment' ) ? 'right-edge-sticked' : 'left-edge-sticked';
			}
			
			// Enable carousel on mobile
			if ( apply_filters( 'kalium_woocommerce_single_product_plain_images_carousel_mobile', true ) ) {
				$images_carousel_classes[] = 'plain-images-carousel-mobile';
			}
			
			// Add animation for plain type
			add_filter( 'kalium_woocommerce_single_product_link_image_classes', 'kalium_woocommerce_single_product_link_image_classes_plain' );
		} else {
			$images_carousel_classes[] = 'carousel';
			
			// Add animation for carousel type
			add_filter( 'kalium_woocommerce_single_product_link_image_classes', 'kalium_woocommerce_single_product_link_image_classes_carousel' );
		}
		
		// Product gallery is sticky
		if ( 'plain-sticky' == $shop_single_product_images_layout ) {
			$images_carousel_classes[] = 'sticky';
		}
		
		// When lightbox is enabled
		if ( $lightbox_enabled ) {
			$images_carousel_classes[] = 'has-lightbox';
		}
		
		
		// Populate Images Array
		$images = array();
		
		// Featured image first
		if ( has_post_thumbnail() ) {
			$images[] = get_post_thumbnail_id( $product->get_id() );
		}
		
		// Gallery images
		$images = array_merge( $images, $attachment_ids );
		
		// Carousel Skip Featured Image
		$carousel_skip_featured_image = true === apply_filters( 'kalium_woocommerce_skip_featured_image_in_carousel', false );
		
		if ( $is_carousel && $carousel_skip_featured_image ) {
			$images_carousel_classes[] = 'skip-featured-image';
		}
		
		// No Spacing for carousel images
		if ( apply_filters( 'kalium_woocommerce_single_product_images_carousel_no_spacing' , false ) ) {
			$images_carousel_classes[] = 'no-spacing';
		}
		
		// Show product images
		?>
		<div class="<?php kalium()->helpers->showClasses( $images_container_classes, true ); ?>">
		
			<div class="<?php kalium()->helpers->showClasses( $images_carousel_classes, true ); ?>">
				
				<?php
					// Image sizes
					$size_shop_single = kalium_woocommerce_get_product_image_size( 'single' );
					$size_shop_thumbnail = kalium_woocommerce_get_product_image_size( 'thumbnail' );
					
					// Show images
					if ( count ( $images ) ) :
					
						foreach ( $images as $i => $attachment_id ) :
						
							$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
							
							$html = kalium_woocommerce_get_single_product_image( $attachment_id, $size_shop_single, $lightbox_enabled );
							
							// Product lightbox image entry
							$single_product_params_js['images'][] = array(
								'index'	=> $i,
								'id'  	=> absint( $attachment_id ),
								'src' 	=> $full_size_image[0],
								'w'   	=> $full_size_image[1],
								'h'   	=> $full_size_image[2],
							);
							
							echo apply_filters( 'kalium_woocommerce_single_product_image_html', $html, $attachment_id );
							
						endforeach;
						
					
					// Show placeholder
					else : 
						
						$html = kalium_get_attachment_image( wc_placeholder_img_src() );
						
						echo apply_filters( 'kalium_woocommerce_single_product_image_placeholder_html', $html );
						
					endif;
				?>
				
			</div>
			
			<?php
			// Product thumbnails	
			if ( $is_carousel ) :
				
				// Thumbnail Coursel JS Options
				kalium_woocommerce_single_product_images_carousel_setup_options( $thumbnails_columns );
				
				// Skip featured image
				if ( $carousel_skip_featured_image ) {
					$images = array_slice( $images, 1, count( $images ) - 1 );
				}
			?>
			<div class="thumbnails" data-columns="<?php echo $thumbnails_columns; ?>">
				<?php
					
					foreach ( $images as $attachment_id ) :
						
						$html = kalium_woocommerce_get_single_product_image( $attachment_id, $size_shop_thumbnail );
						
						echo apply_filters( 'kalium_woocommerce_single_product_image_html', $html, $attachment_id );
						
					endforeach;
					
				?>
			</div>
			<?php endif; ?>
			
			<script> 
				var kalium_wc_single_product_params = <?php echo json_encode( apply_filters( 'kalium_woocommerce_single_product_params_js', $single_product_params_js ) ); ?>;
			</script>
		</div>
		<?php
	}
}

/**
 *  WooCommerce Single Product Thumbnails Carousel Setup
 */
if ( ! function_exists( 'kalium_woocommerce_single_product_images_carousel_setup_options' ) ) {
	
	function kalium_woocommerce_single_product_images_carousel_setup_options( $thumbnail_columns = 4 ) {
		$shop_single_product_images_layout = get_data( 'shop_single_product_images_layout' );
		$shop_single_auto_rotate_image = get_data( 'shop_single_auto_rotate_image' );
		$shop_product_image_columns = apply_filters( 'kalium_woocommerce_single_product_image_column_size', 'small' );
		
		if ( '' == $shop_single_auto_rotate_image ) {
			$shop_single_auto_rotate_image = 5;
		}
		
		$shop_single_auto_rotate_image = absint( $shop_single_auto_rotate_image );
		
		if ( ! in_array( $shop_single_product_images_layout, array( 'plain', 'plain-sticky' ) ) ) {
			$image_carousel_options = array();
			
			// Thumbnails to Show
			$image_carousel_options['thumbnailsToShow'] = $thumbnail_columns;
			
			// Auto Rotate Images
			$image_carousel_options['autoRotateImage'] = $shop_single_auto_rotate_image * 1000;
			
			// Image Transition Type
			$image_carousel_options['carouselFade'] = 'slide' == get_data( 'shop_single_image_carousel_transition' ) ? false : true;
			
			// Parse options to JSON
			?>
			<script type="text/javascript">
				window.singleShopProductCarouselOptions = <?php echo json_encode( $image_carousel_options ); ?>
			</script>
			<?php
		}
	}
}

/**
 *  Render Rating
 */
if ( ! function_exists( 'kalium_woocommerce_show_rating' ) ) {
	
	function kalium_woocommerce_show_rating( $average ) {
		$shop_single_rating_style = get_data( 'shop_single_rating_style' );
		?>
		<div class="star-rating-icons" data-toggle="tooltip" data-placement="right" title="<?php echo sprintf( __( '%s out of 5', 'kalium' ), $average ); ?>">
		<?php
		
		$average_int = intval( $average );	
		$average_floated = $average - $average_int;
		
		for ( $i = 1; $i <= 5; $i++ ) :
	
			if ( in_array( $shop_single_rating_style, array( 'circles', 'rectangles' ) ) ) :
				
				$fill = 100;
				
				if ( $i > $average ) {
					$fill = 0;
					
					if ( $average_int + 1 == $i ) {
						$fill = $average_floated * 100;
					}
				}
				?>
				<span class="circle<?php echo $shop_single_rating_style == 'circles' ? ' rounded' : ''; ?>">
					<i style="width: <?php echo esc_attr( $fill ); ?>%"></i>
				</span>
				<?php
			else:
				?>
				<i class="fa fa-star<?php echo round( $average ) >= $i ? ' filled' : ''; ?>"></i>
				<?php
			endif;
			
		endfor;
		
		?>
		</div>
		<?php
	}
}

/**
 *  My account wrapper (before)
 */
if ( ! function_exists( 'kalium_woocommerce_before_my_account' ) ) {
	
	function kalium_woocommerce_before_my_account() {
		?>
			<div class="my-account">
		<?php
	}
}

/**
 *  My account wrapper (after)
 */
if ( ! function_exists( 'kalium_woocommerce_after_my_account' ) ) {
	
	function kalium_woocommerce_after_my_account() {
		?>
			</div>
		<?php	
	}
}

/**
 *  Double variation image fix
 */
if ( ! function_exists( 'kalium_woocommerce_variation_remove_featured_image' ) ) {
	
	function kalium_woocommerce_variation_remove_featured_image( $variation, $variable ) {
		
		if ( kalium_woocommerce_use_custom_product_gallery_layout() ) {
			$product_id = $variable->get_id();
			
			if ( isset( $variation['image_id'] ) && $variation['image_id'] == get_post_thumbnail_id( $product_id ) ) {
				$variation['image_id'] = '';
				$variation['image'] = null;
			}
		}
		
		return $variation;
	}
}

/**
 *  Review rating
 */
if ( ! function_exists( 'kalium_woocommerce_product_get_rating_html' ) ) {

	function kalium_woocommerce_product_get_rating_html( $html, $rating, $count ) {
		
		ob_start();
		?>
		<div class="star-rating">
			<?php
				kalium_woocommerce_show_rating( $rating );	
			?>
		</div>
		<?php
			
		return ob_get_clean();
	}
}

/**
 *  Product rating
 */
if ( ! function_exists( 'kalium_woocommerce_single_product_rating_stars' ) ) {
	
	function kalium_woocommerce_single_product_rating_stars() {
		global $product;
		
		$average = $product->get_average_rating();
		
		?>
		<div class="star-rating" title="<?php printf( __( 'Rated %s out of 5', 'kalium' ), $average ); ?>">
			<?php kalium_woocommerce_show_rating( $average ); ?>
		</div>
		<?php
	}
}

/**
 *  Payment method title
 */
if ( ! function_exists( 'kalium_woocommerce_review_order_before_payment_title' ) ) {
	
	function kalium_woocommerce_review_order_before_payment_title() {		
		?>
		<h2 id="payment_method_heading"><?php _e( 'Payment method', 'kalium' ); ?></h2>
		<?php
	}
}

/**
 *  Review product form
 */
if ( ! function_exists( 'kalium_woocommerce_product_review_comment_form_args' ) ) {

	function kalium_woocommerce_product_review_comment_form_args( $args ) {
		$args['class_submit'] = 'button';
		
		// Comment textarea
		$args['comment_field'] = preg_replace( '/(<p.*?)class="(.*?)"/', '\1class="labeled-textarea-row \2"', $args['comment_field'] );
		
		// Comment fields
		if ( ! empty( $args['fields'] ) ) {
			foreach ( $args['fields'] as & $field ) {
				$field = preg_replace( '/(<p.*?)class="(.*?)"/', '\1class="labeled-input-row \2"', $field );
			}
			
			// Clear last field
			$field_keys = array_keys( $args['fields'] );
			
			$args['fields'][ end( $field_keys ) ] .= '<div class="clear"></div>';
		}
		
		return $args;
	}
}

/**
 *  Login page heading
 */
if ( ! function_exists( 'kalium_woocommerce_my_account_login_page_heading' ) ) {

	function kalium_woocommerce_my_account_login_page_heading() {
		
		?>
			<div class="section-title">
				
				<h1><?php
					if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
						esc_html_e( 'Login or register', 'kalium' );
					} else {
						esc_html_e( 'Login', 'kalium' );
					}
				?></h1>
				
				<p><?php _e( 'Manage your account and see your orders', 'kalium' ) ?></p>
			</div>
			
		<?php
	}
}

/**
 *  Show mini cart icon and contents in header
 */
if ( ! function_exists( 'kalium_woocommerce_header_mini_cart' ) ) {
	
	function kalium_woocommerce_header_mini_cart( $skin ) {
		if ( get_data( 'shop_cart_icon_menu' ) ) {
			kalium_woocommerce_cart_menu_icon( $skin ); 
		}
	}
}

/**
 *  Cart Menu Icon
 */
if ( ! function_exists( 'kalium_woocommerce_cart_menu_icon' ) ) {
	
	function kalium_woocommerce_cart_menu_icon( $skin ) {
		
		$icon				= get_data( 'shop_cart_icon' );
		$hide_empty			= get_data( 'shop_cart_icon_menu_hide_empty' );
		$show_cart_contents	= get_data( 'shop_cart_contents' );
		$cart_items_counter	= get_data( 'shop_cart_icon_menu_count' );
		
		$cart_items = WC()->cart->get_cart_contents_count();
		
		
		?>
		<div class="menu-cart-icon-container <?php 
			
			echo esc_attr( $skin ); 
			when_match( $hide_empty && $cart_items == 0, 'hidden' );
			when_match( $show_cart_contents == 'show-on-hover', 'hover-show' );
	
		?>">
		
			<a href="<?php echo wc_get_cart_url(); ?>" class="cart-icon-link icon-type-<?php echo esc_attr( $icon ); ?>">
				<i class="icon-<?php echo esc_attr( $icon ); ?>"></i>
				
				<?php if ( $cart_items_counter ) : ?>
				<span class="items-count hide-notification cart-items-<?php echo esc_attr( $cart_items ); ?>">&hellip;</span>
				<?php endif; ?>
			</a>
			
			
			<?php if ( $show_cart_contents != 'hide' ) : ?>
			<div class="lab-wc-mini-cart-contents">
			<?php get_template_part( 'tpls/wc-mini-cart' ); ?>
			</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

/**
 *  Mobile cart icon on menu
 */
if ( ! function_exists( 'kalium_woocommerce_cart_menu_icon_mobile' ) ) {
	
	function kalium_woocommerce_cart_menu_icon_mobile() {
		$icon				= get_data( 'shop_cart_icon' );
		$hide_empty			= get_data( 'shop_cart_icon_menu_hide_empty' );
		$show_cart_contents	= get_data( 'shop_cart_contents' );
		$cart_items_counter	= get_data( 'shop_cart_icon_menu_count' );
		
		$cart_items = WC()->cart->get_cart_contents_count();
		
		?>
		<div class="cart-icon-link-mobile-container">
			<a href="<?php echo wc_get_cart_url(); ?>" class="cart-icon-link-mobile icon-type-<?php echo esc_attr( $icon ); ?>">
				<i class="icon icon-<?php echo esc_attr( $icon ); ?>"></i>
				
				<?php _e( 'Cart', 'kalium' ); ?>
				
				<?php if ( $cart_items_counter ) : ?>
				<span class="items-count hide-notification cart-items-<?php echo esc_attr( $cart_items ); ?>">&hellip;</span>
				<?php endif; ?>
			</a>
		</div>
		<?php
	}
}

/**
 *  Cart Fragments for Minicart
 */
if ( ! function_exists( 'kalium_woocommerce_woocommerce_add_to_cart_fragments' ) ) {
		
	function kalium_woocommerce_woocommerce_add_to_cart_fragments( $fragments_arr ) {
		ob_start();
		get_template_part( 'tpls/wc-mini-cart' ); 
		$cart_contents = ob_get_clean();
		
		$fragments_arr['labMiniCart'] = $cart_contents;
		$fragments_arr['labMiniCartCount'] = WC()->cart->get_cart_contents_count();
		
		return $fragments_arr;
	}
}



/**
 *  Product Sharing
 */
if ( ! function_exists( 'kalium_woocommerce_share_product' ) ) {

	function kalium_woocommerce_share_product() {
		global $product;
	
		?>
		<div class="share-product-container">
			<h3><?php _e( 'Share this item:', 'kalium' ); ?></h3>
			
			<div class="share-product social-links">
			<?php
				
				$share_product_networks = get_data( 'shop_share_product_networks' );
		
				if ( is_array( $share_product_networks ) ) :
		
					foreach ( $share_product_networks['visible'] as $network_id => $network ) :
		
						if ( 'placebo' == $network_id ) {
							continue;
						}
		
						share_story_network_link( $network_id, $product->get_id(), '', true );
		
					endforeach;
		
				endif;
				
			?>
			</div>
		</div>
		<?php
	}
}


/**
 *  Account Navigation (before)
 */
if ( ! function_exists( 'kalium_woocommerce_before_account_navigation' ) ) {
	
	function kalium_woocommerce_before_account_navigation() {
		global $current_user;
		
		$account_page_id  = wc_get_page_id( 'myaccount' );
		$account_url      = get_permalink( $account_page_id );
		$logout_url       = wp_logout_url( $account_url );
		
		?>
		<div class="wc-my-account-tabs">
			
			<div class="user-profile">
				<a class="image">
					<?php echo get_avatar( $current_user->ID, 128 ); ?>
				</a>
				<div class="user-info">
					<a class="name" href="<?php echo the_author_meta( 'user_url', $current_user->ID ); ?>"><?php echo $current_user->display_name; ?></a>
					<a class="logout" href="<?php echo $logout_url; ?>"><?php _e( 'Logout', 'kalium' ); ?></a>
				</div>
			</div>
		<?php
	}
}
	
/**
 *  Account Navigation (after)
 */
if ( ! function_exists( 'kalium_woocommerce_after_account_navigation' ) ) {
	
	function kalium_woocommerce_after_account_navigation() {
		?>
		</div>
		<?php
	}
}

/**
 *  My Orders Page Title
 */
if ( ! function_exists( 'kalium_woocommerce_before_account_orders' ) ) {
	
	function kalium_woocommerce_before_account_orders( $has_orders ) {
		
		?>
		<div class="section-title">
			<h1><?php _e( 'My Orders', 'kalium' ); ?></h1>
			<p><?php _e( 'Your recent orders are displayed in the table below.', 'kalium' ); ?></p>
		</div>
		<?php
	}
}

/**
 *  My Downloads Page Title
 */
if ( ! function_exists( 'kalium_woocommerce_before_account_downloads' ) ) {
	
	function kalium_woocommerce_before_account_downloads( $has_orders ) {
		
		?>
		<div class="section-title">
			<h1><?php _e( 'My Downloads', 'kalium' ); ?></h1>
			<p><?php _e( 'Your digital downloads are displayed in the table below.', 'kalium' ); ?></p>
		</div>
		<?php
	}
}

/**
 *  Single Product Image – fadeIn effect for carousel type
 */
if ( ! function_exists( 'kalium_woocommerce_single_product_link_image_classes_carousel' ) ) {
	
	function kalium_woocommerce_single_product_link_image_classes_carousel( $classes ) {
		$classes[] = 'fadeIn';
		$classes[] = 'fast';
		return $classes;
	}
}

/**
 *  Single Product Image – fadeInLab effect for carousel type
 */
if ( ! function_exists( 'kalium_woocommerce_single_product_link_image_classes_plain' ) ) {
	
	function kalium_woocommerce_single_product_link_image_classes_plain( $classes ) {
		$classes[] = 'fadeInLab';
		return $classes;
	}
}

/**
 *  WooCommerce Fields
 */
if ( ! function_exists( 'kalium_woocommerce_woocommerce_form_field_args' ) ) {

	function kalium_woocommerce_woocommerce_form_field_args( $args ) {
		
		// Replace Input Labels with Placeholder (text, password, etc)
		if ( in_array( $args['type'], array( 'text', 'password', 'state', 'country', 'email', 'tel' ) ) ) {
			$args['placeholder'] = $args['label'];
			$args['label_class'][] = 'hidden';
		} 
		elseif ( in_array( $args['type'], array( 'checkbox', 'radio' ) ) ) {
			if ( 'checkbox' == $args['type'] ) {
				$args['label_class'][] = 'alternate-checkbox';
			} else {
				$args['label_class'][] = 'alternate-radio';
			}
		}
		
		return $args;
	}
}

/**
 *  Related products and Upsells columns count
 */
if ( ! function_exists( 'kalium_woocommerce_related_products_columns' ) ) {
	
	function kalium_woocommerce_related_products_columns( $args ) {
		return get_data( 'shop_related_products_columns' );
	}
}

/**
 *  Related products to show
 */
if ( ! function_exists( 'kalium_woocommerce_related_products_args' ) ) {

	function kalium_woocommerce_related_products_args( $args ) {
		$args['posts_per_page'] = get_data( 'shop_related_products_per_page' );
		return $args;
	}
}

/**
 *  Trigger lightbox button
 */
if ( ! function_exists( 'kalium_woocommerce_get_lightbox_trigger_button' ) ) {
	
	function kalium_woocommerce_get_lightbox_trigger_button( $attachment_id ) {
		return '<button class="product-gallery-lightbox-trigger" data-id="' . $attachment_id . '" title="' . __( 'View full size', 'kalium' ) . '"><i class="flaticon-close38"></i></button>';
	}
}

/**
 *  Return to shop after cart item adding (option enabled in Woo)
 */
if ( ! function_exists( 'kalium_woocommerce_get_lightbox_trigger_button' ) ) {
	
	function kalium_woocommerce_continue_shopping_redirect_to_shop( $url ) {
		return wc_get_page_permalink( 'shop' );
	}
}

/**
 *  Replace cart remove link icon
 */
if ( ! function_exists( 'kalium_woocommerce_woocommerce_cart_item_remove_link' ) ) {
	
	function kalium_woocommerce_woocommerce_cart_item_remove_link( $remove_link ) {
		return str_replace( '&times;', '<i class="flaticon-cross37"></i>', $remove_link );
	}
}

/**
 *  Single product image column width
 */
if ( ! function_exists( 'kalium_woocommerce_get_product_gallery_container_width' ) ) {
	
	function kalium_woocommerce_get_product_gallery_container_width() {
		$images_column_size = get_data( 'shop_single_image_column_size' );
		$size = 'defalt';
		
		switch ( $images_column_size ) {
			
			case 'xlarge':
			case 'large':
			case 'medium':
				$size = $images_column_size;
				break;
		}
		
		return $size;
	}
}

/**
 *  Single product images alignment
 */
if ( ! function_exists( 'kalium_woocommerce_get_product_gallery_container_alignment' ) ) {
	
	function kalium_woocommerce_get_product_gallery_container_alignment() {
		$image_alignment = get_data( 'shop_single_image_alignment' );
		
		return 'right' == $image_alignment ? 'right' : 'left';
	}
}

/**
 *  Bacs details before
 */
if ( ! function_exists( 'kalium_woocommerce_bacs_details_before' ) ) {
	
	function kalium_woocommerce_bacs_details_before() {
		echo '<div class="bacs-details-container">';
	}
}

/**
 *  Bacs details after
 */
if ( ! function_exists( 'kalium_woocommerce_bacs_details_after' ) ) {
		
	function kalium_woocommerce_bacs_details_after() {
		echo '</div>';
	}
}

/**
 * Show rating below top rated products widget
 */
if ( ! function_exists( 'kalium_woocommerce_top_rated_products_widget_rating' ) ) {
	
	function kalium_woocommerce_top_rated_products_widget_rating( $args ) {
		global $product;
		
		if ( ! empty( $args['show_rating'] ) && $product->get_average_rating() ) :
			
			?>
			<p class="rating">
				<i class="fa fa-star"></i>
				<?php echo $product->get_average_rating(); ?>
			</p>
			<?php
			
		endif;
	}
}

/**
 * Single product wrapper start
 */
if ( ! function_exists( 'kalium_woocommerce_single_product_wrapper_start' ) ) {
	
	function kalium_woocommerce_single_product_wrapper_start() {
		
		$classes = array( 'single-product' );
		$sidebar = kalium_woocommerce_single_product_get_sidebar_position();
		
		if ( $sidebar ) {
			$classes[] = 'single-product--has-sidebar';
			
			if ( 'left' == $sidebar ) {
				$classes[] = 'single-product--sidebar-left';
			}
			
			if ( get_data( 'shop_single_sidebar_before_products_mobile' ) ) {
				$classes[] = 'single-product--sidebar-first';
			}
		}
		
		?>
		<div <?php kalium_class_attr( $classes ); ?>>
			
			<div class="single-product--product-details">
		<?php
	}
}

/**
 * Single product wrapper end
 */
if ( ! function_exists( 'kalium_woocommerce_single_product_wrapper_end' ) ) {
	
	function kalium_woocommerce_single_product_wrapper_end() {
		
		?>
			</div>
			
			<?php
				// Sidebar
				if ( kalium_woocommerce_single_product_get_sidebar_position() ) :
				
				?>
				
				<div class="single-product--sidebar">
					
					<?php
						// Show widgets
						$sidebar = is_active_sidebar( 'shop_sidebar_single' ) ? 'shop_sidebar_single' : 'shop_sidebar';
						
						kalium_get_widgets( $sidebar, 'single-product--widgets' );
					?>
					
				</div>
				
				<?php
				endif;
			?>
			
		</div>
		<?php
	}
}

/**
 * Product flash badges
 */
if ( ! function_exists( 'kalium_woocommerce_product_badges' ) ) {
	
	function kalium_woocommerce_product_badges( $post, $product ) {
		$html = '';
		
		// Out of stock
		if ( ( $product->is_in_stock() == false && ! ( $product->is_type( 'variable' ) && $product->get_stock_quantity() > 0 ) ) && get_data( 'shop_oos_ribbon_show' ) ) {
			$html = sprintf( '<div class="onsale oos">%s</div>', esc_html__( 'Out of stock', 'kalium' ) );
		}
		
		// Featured product
		else if ( $product->is_featured() && get_data( 'shop_featured_ribbon_show' ) ) {
			$html = sprintf( '<span class="onsale featured">%s</span>', esc_html__( 'Featured', 'kalium' ) );
		}
		
		// Sale
		else if ( $product->is_on_sale() && get_data( 'shop_sale_ribbon_show' ) ) {			
			$html = apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span>', $post, $product );
		}
		
		echo $html;
	}
}

/**
 * Custom image size for single product images (main images)
 */
if ( ! function_exists( 'kalium_woocommerce_get_custom_image_size_single' ) ) {
	
	function kalium_woocommerce_get_custom_image_size_single( $size ) {
		
		$width = get_data( 'shop_single_product_custom_image_size_width' );
		$height = get_data( 'shop_single_product_custom_image_size_height' );
		
		if ( empty( $width ) || ! is_numeric( $width ) || $width <= 0 ) {
			$width = wc_get_theme_support( 'single_image_width' );
		}
		
		// Custom width
		$size['width'] = $width;
		
		// Custom height
		if ( $height && is_numeric( $height ) && $height > 0 ) {
			$size['height'] = $height;
		}
		
		// Crop if width and height are specified
		$size['crop'] = ! empty( $size['width'] ) && ! empty( $size['height'] );
		
		return $size;
	}
}

/**
 * Category thumbnail
 */
if ( ! function_exists( 'kalium_woocommerce_subcategory_thumbnail' ) ) {
	
	function kalium_woocommerce_subcategory_thumbnail( $category ) {
		
		ob_start();
		
		woocommerce_subcategory_thumbnail( $category );
		
		echo kalium_get_attachment_image( ob_get_clean() );
	}
}

/**
 * Catalog mode, show add to cart options
 */
if ( ! function_exists( 'kalium_woocommerce_catalog_mode_add_to_cart_options' ) ) {
	
	function kalium_woocommerce_catalog_mode_add_to_cart_options() {
		global $product;
		
		// Variable product
		if ( 'variable' == $product->get_type() ) {
			
			// Remove add to cart button
			remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
			remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
	
			// Variation product add-to-cart
			woocommerce_variable_add_to_cart();
		}
	}
}
