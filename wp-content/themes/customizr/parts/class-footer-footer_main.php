<?php
/**
* Footer actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_footer_main {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;
        //html > footer actions
        add_action ( '__after_main_wrapper'		, 'get_footer');

        //footer actions
        add_action ( '__footer'					, array( $this , 'tc_widgets_footer' ), 10 );
        add_action ( '__footer'					, array( $this , 'tc_colophon_display' ), 20 );
    }


    /**
	 * Displays the footer widgets areas
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0.10
	 */
    function tc_widgets_footer() {
		if ( !is_active_sidebar( 'footer_one' ) && !is_active_sidebar( 'footer_two' ) && !is_active_sidebar( 'footer_three' ))
			return;
		tc__f('rec' , __FILE__ , __FUNCTION__ );

		ob_start() 
		?>
			<div class="container footer-widgets">
				<div class="row widget-area" role="complementary">
				<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>

					<?php if ( is_active_sidebar( 'footer_one' ) ) : ?>
					<div class="span4">
						<?php dynamic_sidebar( 'footer_one' ); ?>
					</div>
					<?php endif; ?>

					<?php if ( is_active_sidebar( 'footer_two' ) ) : ?>
					<div class="span4">
						<?php dynamic_sidebar( 'footer_two' ); ?>
					</div>
					<?php endif; ?>
					
					<?php if ( is_active_sidebar( 'footer_three' ) ) : ?>
					<div class="span4">
						<?php dynamic_sidebar( 'footer_three' ); ?>
					</div>
					<?php endif; ?>

				</div><!-- .row widget-area -->
			</div><!--.footer-widgets -->
		<?php
		$html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_widgets_footer', $html );
	}//end of function






    /**
	 * Displays the colophon (block below the widgets areas).
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0.10
	 */
    function tc_colophon_display() {
    	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
    	?>

    	<?php ob_start() ?>

		 <div class="colophon">
		 <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>

		 	<div class="container">

		 		<div class="row-fluid">

				    <?php 
					    //colophon blocks actions priorities
				        add_action ( '__colophon', array( $this , 'tc_social_in_footer' ), 10 );
				        add_action ( '__colophon', array( $this , 'tc_credits_display' ), 20 , 2 );
				        add_action ( '__colophon', array( $this , 'tc_back_to_top_display' ), 30 );
					    
					    //renders blocks
					    do_action( '__colophon' ); 
				    ?>

      			</div><!-- .row-fluid -->

      		</div><!-- .container -->

      	</div><!-- .colophon -->
    	<?php
    	$html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_colophon_display', $html );
    }




    /**
	 * Displays the social networks block in the footer
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0.10
	 */
    function tc_social_in_footer() {
      	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        ob_start();
        ?>

	        <div class="span4 social-block pull-left">
	        	<?php if ( 0 != tc__f( '__get_option', 'tc_social_in_footer') ) : ?>
	        		<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
	           		<?php echo tc__f( '__get_socials' ) ?>
	           	<?php endif; ?>
	        </div>
	        
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_social_in_footer', $html );
    }




    /**
	 * Footer Credits call back functions
	 * Can be filtered using the $site_credits, $tc_credits parameters
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0.6
	 */
    function tc_credits_display( $site_credits = null, $tc_credits = null ) {
    	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
    	?>

    	<?php ob_start() ?>

    	<div class="span4 credits">
    	<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
	    	<?php
		    	$credits =  sprintf( '<p> &middot; &copy; %1$s <a href="%2$s" title="%3$s" rel="bookmark">%3$s</a> &middot; Design: %4$s &middot;</p>',
					    esc_attr( date( 'Y' ) ),
					    esc_url( home_url() ),
					    esc_attr(get_bloginfo()),
					    '<a href="'.TC_WEBSITE.'">Themes &amp; Co</a>'
				);
				echo $credits;
			?>
		</div>
		<?php
		$html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_credits_display', $html );
    }





    /**
	 * Displays the back to top block
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0.10
	 */
	function tc_back_to_top_display() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
    	?>

    	<?php ob_start() ?>
	    <div class="span4 backtop">
	    	<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
	    	<p class="pull-right">
	    		<a class="back-to-top" href="#"><?php _e( 'Back to top' , 'customizr' ) ?></a>
	    	</p>

	    </div>
	    <?php
	    $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_back_to_top_display', $html );
	}

 }//end of class


