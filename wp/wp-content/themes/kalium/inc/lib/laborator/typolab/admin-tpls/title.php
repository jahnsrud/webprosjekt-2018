<?php
/**
 *	TypoLab Title
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$add_font_link = '<a href="' . admin_url( "admin.php?page={$_GET['page']}&typolab-action=add-font" ) . '" class="page-title-action">Add Font</a>';

?>
<h1>
	<?php if ( isset( $title ) ) : ?>
		<?php echo str_replace( '{add-font-link}', $add_font_link, esc_html( $title ) ); ?>
		<?php if ( isset( $sub_title ) ) : ?>
		<small><?php echo esc_html( $sub_title ); ?></small>
		<?php endif; ?>
	<?php else : ?>
	Typography <?php echo $add_font_link; ?>
	<?php endif; ?>
</h1>