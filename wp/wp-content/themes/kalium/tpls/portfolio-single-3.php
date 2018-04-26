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

include locate_template( 'tpls/portfolio-single-item-details.php' );

$has_services_section   = $launch_link_href || count( $checklists );
$is_centered            = $layout_type == 'centered';

wp_enqueue_script( 'nivo-lightbox' );
wp_enqueue_style( 'nivo-lightbox-default' );

// Columns Gap
if ( $gallery_columns_gap ) {
	kalium_portfolio_generate_gallery_gap( $gallery_columns_gap, 'carousel' );
}

do_action( 'kalium_portfolio_item_before', 'type-3' ); 
?>
<div class="container">

	<div class="page-container">
    	<div class="single-portfolio-holder portfolio-type-3<?php when_match( $is_centered, 'portfolio-centered-layout alt-six', 'alt-four' ); ?>">

			<div class="title section-title<?php echo $is_centered ? ' text-on-center' : ''; ?>">
				<h1><?php the_title(); ?></h1>
				<?php if ( $sub_title ) : ?>
				<p><?php echo esc_html( $sub_title ); ?></p>
				<?php endif; ?>

				<?php if ( $is_centered ) : ?>
				<div class="dash small"></div>
				<?php endif; ?>
			</div>

    		<div class="details row">
    			<div class="<?php echo $has_services_section && $is_centered == false ? 'col-sm-8' : 'col-sm-12'; ?>">
	    			<div class="project-description">
	    				<div class="post-formatting">
							<?php the_content(); ?>
						</div>
	    			</div>
    			</div>

				<?php if ( $has_services_section && $is_centered == false ) : ?>
    			<div class="col-sm-3 col-sm-offset-1">
	    			<div class="services">
		    			<?php include locate_template( 'tpls/portfolio-checklists.php' ); ?>

		    			<?php include locate_template( 'tpls/portfolio-launch-project.php' ); ?>
	    			</div>
    			</div>
    			<?php endif; ?>
				
				<div class="col-sm-12">
		    		<?php include locate_template( 'tpls/portfolio-single-like-share.php' ); ?>
	    		</div>


				<?php if ( $has_services_section && $is_centered == true ) : ?>
	    		<div class="col-sm-12 inline-checklists">

	    			<?php include locate_template( 'tpls/portfolio-checklists.php' ); ?>

					<?php include locate_template( 'tpls/portfolio-launch-project.php' ); ?>

				</div>
				<?php endif; ?>
    		</div>

			<?php include locate_template( 'tpls/portfolio-gallery-slider.php' ); ?>

			<?php include locate_template( 'tpls/portfolio-single-prevnext.php' ); ?>
    	</div>
	</div>

</div>