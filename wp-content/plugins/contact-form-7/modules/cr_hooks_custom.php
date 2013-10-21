<?php
/**
** A custom base module for the following types of tags:
** 	[thesource]		# source page form was submitted from
**/

/* Shortcode handler */

// HK 2013/10/19

// get source URL
wpcf7_add_shortcode('sourceurl', 'wpcf7_sourceurl_shortcode_handler', true);

function wpcf7_sourceurl_shortcode_handler($tag) 
{
	if (!is_array($tag)) return '';

	$name = $tag['name'];
	if (empty($name)) return '';

	$html = '<input type="hidden" name="' . $name . '" value="http://' . $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"] . '" />';
	return $html;
}

// HK 2013/10/20 - update database before emails sent

add_action( ‘wpcf7_before_send_mail’, ‘db_insert_cr_request’ );

function db_insert_cr_request( $cf7 )
{
	global $wpdb;

	$size_top = $cf7->posted_data["size-top"];
	$size_bottom = $cf7->posted_data["size-bottom"];

	// insert info
	$user_ID = get_current_user_id();
	$request_type_ID = 0;
	$item_details = '[' . $size_top . '|' . $size_bottom . ']';

	$wpdb->insert(
		'cr_requests',
		array(
			'user_id' => $user_ID,
			'request_type_id' => $request_type_ID,
			'item_details' => $item_details
		),
		array(
			'%d',
			'%d',
			'%s'
		)
	);
}

?>