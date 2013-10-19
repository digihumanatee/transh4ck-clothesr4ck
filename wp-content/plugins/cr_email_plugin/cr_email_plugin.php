<?php
/*
Plugin Name: _Custom Email Plugin
Plugin URI: http://transfrwith.us/
Description: Created to send emails. Source: http://wp.smashingmagazine.com/2011/10/25/create-perfect-emails-wordpress-website/
Version: 1.0
Author: Harlan
Author URI: http://transfrwith.us/about/us/#harlan
*/

add_action("publish_post", "cr_publication_notification");

function cr_publication_notification($post_id) {
	$post = get_post($post_id);
	$author = get_userdata($post->post_author);
	
	$author_email = $author->user_email;
    $email_subject = "Your article has been published!";
	
	ob_start();
	
	include("cr_email_header.php");
	
	?>
	
	<p>
		Hi, <?php echo $author->display_name ?>. I've just published one of your articles 
		(<?php echo $post->post_title ?>) on Transfrwith.us!
	</p>
	
	<p>
		If you'd like to take a look, <a href="<?php echo get_permalink($post->ID) ?>">click here</a>. 
		I would appreciate it if you could come back now and again to respond to some comments.
	</p>
	
	
	<?php
	
	include("cr_email_footer.php");
	
	
	$message = ob_get_contents();
	
	ob_end_clean();
	
	
	wp_mail($author_email, $email_subject, $message);
	
}

?>