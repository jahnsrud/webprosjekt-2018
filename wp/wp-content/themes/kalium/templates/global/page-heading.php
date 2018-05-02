<?php
/**
 *	Page heading title and description
 *	
 *	Laborator.co
 *	www.laborator.co 
 *
 *	@author		Laborator
 *	@var		$heading_tag
 *	@var		$title
 *	@var		$description
 *	@version	2.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}
?>
<section class="page-heading" role="heading">
	
	<div class="container">
		
		<div class="row">
			
			<?php
				/**
				 * Before page heading title hooks
				 */
				do_action( 'page_heading_title_section_before' );
			?>
	
			<div class="page-heading--title-section">
			
				<?php if ( $title ) : ?>
					
					<<?php echo $heading_tag; ?> class="page-heading--title"><?php echo $title; ?></<?php echo $heading_tag; ?>>
					
				<?php endif; ?>
				
				<?php if ( $description ) : ?>
				
					<div class="page-heading--description">
						
						<?php echo wpautop( $description ); ?>
						
					</div>
					
				<?php endif; ?>
				
			</div>
			
			<?php
				/**
				 * After page heading title hooks
				 */
				do_action( 'page_heading_title_section_after' );
			?>
		
		</div>
	
	</div>
	
</section>