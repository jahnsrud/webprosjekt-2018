<?php
/**
 *	Portable Contact Form
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

// Element Information
$lab_vc_element_icon = kalium()->locateFileUrl( 'inc/lib/vc/lab_contact_form/contact.svg' );

vc_map( array(
	'base'             => 'lab_contact_form',
	'name'             => 'Contact Form',
	"description"      => "Insert AJAX form",
	'category'         => 'Laborator',
	'icon'             => $lab_vc_element_icon,
	'params' => array(
		array(
			'type'           => 'textfield',
			'heading'        => 'Name field title',
			'param_name'     => 'name_title',
			'value'          => 'Name:'
		),
		array(
			'type'           => 'textfield',
			'heading'        => 'Email field title',
			'param_name'     => 'email_title',
			'value'          => 'Email:'
		),
		array(
			'type'           => 'textfield',
			'heading'        => 'Message field title',
			'param_name'     => 'message_title',
			'value'          => 'Message:'
		),
		array(
			'type'           => 'checkbox',
			'heading'        => 'Subject field',
			'param_name'     => 'show_subject_field',
			'std'            => 'no',
			'value'          => array(
				'Show subject field' => 'yes',
			),
		),
		array(
			'type'       => 'textfield',
			'heading'    => 'Subject field title',
			'param_name' => 'subject_title',
			'value'      => 'Subject:',
			'dependency' => array(
				'element'   => 'show_subject_field',
				'value'     => array('yes')
			),
		),
		array(
			'type'           => 'textfield',
			'heading'        => 'Submit button title',
			'param_name'     => 'submit_title',
			'value'          => 'Send Message'
		),
		array(
			'type'           => 'textfield',
			'heading'        => 'Success message',
			'param_name'     => 'submit_success',
			'value'          => 'Thank you #, message sent!'
		),
		array(
			'type'           => 'checkbox',
			'heading'        => 'Show error alerts',
			'param_name'     => 'alert_errors',
			'std'            => 'no',
			'value'          => array(
				'Yes' => 'yes',
			),
			'description' => 'Show JavaScript alert message when required field is not filled.'
		),
		array(
			'type'       => 'checkbox',
			'heading'    => 'Use subject field as email subject',
			'param_name' => 'subject_field_as_email_subject',
			'value'          => array(
				'Yes' => 'yes',
			),
			'dependency' => array(
				'element'   => 'show_subject_field',
				'value'     => array( 'yes' )
			),
		),
		array(
			'type'           => 'textfield',
			'heading'        => 'Receiver',
			'description'	 => 'Enter an email to receive contact form messages. If empty default admin email will be used ('.get_option('admin_email').')',
			'param_name'     => 'email_receiver'
		),
		array(
			'type'           => 'textfield',
			'heading'        => 'Extra class name',
			'param_name'     => 'el_class',
			'description'    => 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'
		),
		array(
			'type'       => 'css_editor',
			'heading'    => 'Css',
			'param_name' => 'css',
			'group'      => 'Design options'
		)
	)
) );

class WPBakeryShortCode_Lab_Contact_Form extends WPBakeryShortCode {}



// Contact Form Processing
function kalium_vc_contact_form_request() {
	$resp = array(
		'success' => false
	);
	
	$id          = kalium()->post( 'id' );
	$check       = kalium()->post( 'check' );
	$name        = kalium()->post( 'name' );
	$email       = kalium()->post( 'email' );
	$subject     = kalium()->post( 'subject' );
	$use_subject = kalium()->post( 'useSubject' ) == 'true';
	$message     = kalium()->post( 'message' );
	
	$details = kalium()->post( 'request' );
	$details = (array) json_decode( base64_decode( str_rot13( $details ) ) );
	
	$nonce_id = "cf_" . md5( $id );
	
	$email_receiver = $details['email_receiver'];
	
	if ( ! is_email( $email_receiver ) ) {
		$email_receiver = get_option( 'admin_email' );
	}
	
	$email_receiver = apply_filters( 'kalium_contact_form_email_receiver', $email_receiver );
	
	// Subject
	$email_subject = sprintf( _x( '[%s] New Contact Form message has been received.', 'contact form subject', 'kalium' ), get_bloginfo( 'name' ) );
	
	// Message Body
	$details['name_title']    .= strpos( $details['name_title'], ':' ) 	  == -1 ? ': ' : '';
	$details['email_title']   .= strpos( $details['email_title'], ':' )   == -1 ? ': ' : '';
	$details['subject_title'] .= strpos( $details['subject_title'], ':' ) == -1 ? ': ' : '';
	$details['message_title'] .= strpos( $details['message_title'], ':' ) == -1 ? ': ' : '';
	
	$message_body  = _x( 'You have received new contact form message.', 'contact form', 'kalium' );
	$message_body .= "\n\n";
	$message_body .= _x( '----- Message Details -----', 'contact form', 'kalium' );
	$message_body .= "\n\n";
	
	$message_body .= "{$details['name_title']} {$name}\n\n";
	$message_body .= "{$details['email_title']} {$email}\n\n";
	
	if ( apply_filters( 'kalium_contact_form_subject_in_body', true ) && $details['show_subject_field'] == 'yes' ) {
		if ( $subject ) {
			$message_body .= "{$details['subject_title']} {$subject}\n\n";
		}
	}
	
	$message_body .= "{$details['message_title']}\n\n{$message}\n\n";
	$message_body .= "\n\n";
	$message_body .= sprintf( _x( 'This message has been sent from IP: %s', 'contact form', 'kalium' ), $_SERVER['REMOTE_ADDR'] );
	$message_body .= "\n\n";
	$message_body .= sprintf( _x( 'Site URL: %s', 'contact form', 'kalium' ), home_url() );
	
	// Use subject content as email subject field
	if ( $use_subject ) {
		$email_subject = sprintf( '[%s] %s', get_bloginfo( 'name' ), $subject );
	}
	
	// Filter Subject and Body
	$email_subject = apply_filters( 'kalium_contact_form_subject', html_entity_decode( $email_subject ), $details );
	$message_body  = apply_filters( 'kalium_contact_form_message_body', $message_body, $details );
	
	$headers = array();
	$headers[] = "Reply-To: {$name} <{$email}>";
	
	$headers = apply_filters( 'kalium_contact_form_mail_headers', $headers );
	
	if ( wp_mail( $email_receiver, $email_subject, $message_body, $headers ) ) {
		$resp['success'] = true;
	}
	
	// Execute actions after email are sent
	$email_sent_action_args = array(
		'receiver' => $email_receiver,
		'subject' => $email_subject,
		'message' => $message_body,
		'fields' => array(
			'name' => array(
				'title' => $details['name_title'],
				'value' => $name,
			),
			'email' => array(
				'title' => $details['email_title'],
				'value' => $email,
			),
			'subject' => array(
				'title' => $details['subject_title'],
				'value' => $subject,
			),
			'message' => array(
				'title' => $details['message_title'],
				'value' => $message,
			),
		)
	);
	
	do_action( 'kalium_contact_form_email_sent', $email_sent_action_args );
	
	echo json_encode( $resp );
	die();
}

add_action( 'wp_ajax_kalium_vc_contact_form_request', 'kalium_vc_contact_form_request' );
add_action( 'wp_ajax_nopriv_kalium_vc_contact_form_request', 'kalium_vc_contact_form_request' );
