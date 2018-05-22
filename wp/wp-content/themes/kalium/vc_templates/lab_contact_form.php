<?php
/**
 *	Contact Form
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// Atts
if ( function_exists( 'vc_map_get_attributes' ) ) {
	$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
}

extract( $atts );

// Nonce
$uniqid = uniqid( 'el_' );

// Element Class
$class = $this->getExtraClass( $el_class );

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, "lab-contact-form contact-form {$class}", $this->settings['base'], $atts );
$css_class .= vc_shortcode_custom_css_class( $css, ' ' );
?>
<div class="<?php echo esc_attr( $css_class ); ?>">
	<form action="#" class="contact-form" id="<?php echo esc_attr( $uniqid ); ?>" data-alerts="<?php echo $alert_errors == 'yes' ? 1 : 0; ?>" data-alerts-msg="<?php echo esc_attr( __( 'Please fill "%" field.', 'kalium' ) ); ?>" data-use-subject="<?php echo $show_subject_field && $subject_field_as_email_subject ? 1 : 0; ?>" novalidate>
		<input type="hidden" name="request" value="<?php echo str_rot13( base64_encode( json_encode( $atts ) ) ); ?>" />
		<div class="row">
    		<div class="col-sm-6">
				<div class="form-group labeled-input-row">
					<?php if ( $name_title ) : ?>
					<label for="<?php echo "{$uniqid}_name"; ?>"><?php echo esc_html( $name_title ); ?></label>
					<?php endif; ?>
					<input name="name" id="<?php echo "{$uniqid}_name"; ?>" type="text" placeholder="" data-label="<?php echo esc_attr( trim( $name_title, ':?.' ) ); ?>">
				</div>
    		</div>
			<div class="col-sm-6">
				<div class="form-group labeled-input-row">
					<?php if ( $email_title ) : ?>
					<label for="<?php echo "{$uniqid}_email"; ?>"><?php echo esc_html( $email_title ); ?></label>
					<?php endif; ?>
					<input name="email" id="<?php echo "{$uniqid}_email"; ?>" type="email" placeholder="" data-label="<?php echo esc_attr( trim( $email_title, ':?.' ) ); ?>">
				</div>
			</div>
			
			<?php if ( $show_subject_field == 'yes' ) : ?>
    		<div class="col-sm-12">
				<div class="form-group labeled-input-row">
					<?php if ( $subject_title ) : ?>
					<label for="<?php echo "{$uniqid}_subject"; ?>"><?php echo esc_html( $subject_title ); ?></label>
					<?php endif; ?>
					<input name="subject" id="<?php echo "{$uniqid}_subject"; ?>"<?php echo apply_filters(  'kalium_contact_form_subject_field_required', false ) ? ' class="is-required"' : ''; ?> type="text" placeholder="" data-label="<?php echo esc_attr( trim( $subject_title, ':?.' ) ); ?>">
				</div>
    		</div>
			<?php endif; ?>
			
			<div class="col-sm-12">
				<div class="form-group labeled-textarea-row">
					<?php if ( $message_title ) : ?>
					<label for="<?php echo "{$uniqid}_message"; ?>"><?php echo esc_html( $message_title ); ?></label>
					<?php endif; ?>
					<textarea name="message" id="<?php echo "{$uniqid}_message"; ?>" placeholder="" data-label="<?php echo esc_attr( trim( $message_title, ':?.' ) ); ?>"></textarea>
				</div>
			</div>
		</div> <!-- row -->
		<button type="submit" name="send" class="button">
			<span class="pre-submit"><?php echo esc_html( $submit_title ); ?></span>
			<span class="success-msg"><?php echo strip_tags( $submit_success, '<strong><span><em>' ); ?> <i class="flaticon-verification24"></i></span>
			<span class="loading-bar">
				<span></span>
			</span>
		</button>
	</form>
</div>