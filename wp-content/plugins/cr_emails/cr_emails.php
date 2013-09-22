<?php
/*
Plugin Name: CR Emails
Plugin URI: https://github.com/Trans-H4CK/
Description: Custom plugin to send emails
Version: 1.0
Author: HK
Author URI: http://twitter.com/DigiHumanatee
*/

add_filter ("wp_mail_content_type", "cr_email_content_type");
function cr_email_content_type() {
	return "text/html";
}
	
add_filter ("wp_mail_from", "cr_email_from");
function cr_email_from() {
	return "hkellaway.tech@gmail.com";
}
	
add_filter ("wp_mail_from_name", "cr_email_from_name");
function cr_email_from_name() {
	return "TransH4CK ClothesR4CK";
}
?>