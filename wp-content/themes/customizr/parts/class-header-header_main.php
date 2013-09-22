<?php
/**
* Header actions
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

class TC_header_main {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        //html > head actions
        add_action ( '__before_body'			, array( $this , 'tc_head_display' ));
        add_action ( 'wp_head'     				, array( $this , 'tc_favicon_display' ));

        //html > header actions
        add_action ( '__before_main_wrapper'	, 'get_header');
        add_action ( '__header' 				, array( $this , 'tc_logo_title_display' ) , 10 );
        add_action ( '__header' 				, array( $this , 'tc_tagline_display' ) , 20, 1 );
        add_action ( '__header' 				, array( $this , 'tc_navbar_display' ) , 30 );

        //body > header > navbar actions ordered by priority
        add_action ( '__navbar' 				, array( $this , 'tc_social_in_header' ) , 10, 1 );
        add_action ( '__navbar' 				, array( $this , 'tc_tagline_display' ) , 20, 1 );
    }
	



    /**
	 * Displays what is inside the head html tag. Includes the wp_head() hook.
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_head_display() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
		?>
		<head>
		    <meta charset="<?php bloginfo( 'charset' ); ?>" />
		    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
		    <title><?php wp_title( '|' , true, 'right' ); ?></title>
		    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		    <link rel="profile" href="http://gmpg.org/xfn/11" />
		    <?php
		      /* We add some JavaScript to pages with the comment form
		       * to support sites with threaded comments (when in use).
		       */
		      if ( is_singular() && get_option( 'thread_comments' ) )
		        wp_enqueue_script( 'comment-reply' );
		    ?>
		    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		   
		   <!-- Icons font support for IE6-7 -->
		    <!--[if lt IE 8]>
		      <script src="<?php echo TC_BASE_URL ?>inc/css/fonts/lte-ie7.js"></script>
		    <![endif]-->
		    <?php
		      /* Always have wp_head() just before the closing </head>
		       * tag of your theme, or you will break many plugins, which
		       * generally use this hook to add elements to <head> such
		       * as styles, scripts, and meta tags.
		       */
		      wp_head();
		    ?>
		</head>
		<?php
	}




	 /**
      * Render favicon from options
      *
      * @package Customizr
      * @since Customizr 3.0 
     */
      function tc_favicon_display() {
      	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        $url = esc_url( tc__f( '__get_option' , 'tc_fav_upload' ) );
        if( $url != null)   {
          $type = "image/x-icon";
          if(strpos( $url, '.png' )) $type = "image/png";
          if(strpos( $url, '.gif' )) $type = "image/gif";
        
          $html = '<link rel="shortcut icon" href="'.$url.'" type="'.$type.'">';
        
        echo apply_filters( 'tc_favicon_display', $html );
        }

      }




      /**
	 * The template for displaying the title or the logo
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_logo_title_display() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
       $logo_src    			= esc_url ( tc__f( '__get_option' , 'tc_logo_upload') ) ;
       $logo_resize 			= esc_attr( tc__f( '__get_option' , 'tc_logo_resize') );
       //logo styling option
       $logo_img_style			= '';
       if( $logo_resize == 1) {
       	 $logo_img_style 		= 'style="max-width:250px;max-height:100px"';
       }
       ob_start();
		?>

		<?php if( $logo_src != null) :?>

          <div class="brand span3">
          	<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
            <h1><a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' , 'display' ) ); ?> | <?php bloginfo( 'description' ); ?>"><img src="<?php echo $logo_src ?>" alt="<?php _e( 'Back Home' , 'customizr' ); ?>" <?php echo $logo_img_style ?>/></a>
            </h1>
          </div>

	    <?php else : ?>

          <div class="brand span3 pull-left">
          	<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
             <h1><a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' , 'display' ) ); ?> | <?php bloginfo( 'description' ); ?>"><?php bloginfo( 'name' ); ?></a>
              </h1>
          </div>

	   <?php endif; ?>
	   <?php 
	   $html = ob_get_contents();
       ob_end_clean();
       echo apply_filters( 'tc_logo_title_display', $html );
	}


	
	/**
	 * Displays what's inside the navbar of the website. Uses the resp parameter for __navbar action.
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0.10
	 */
	function tc_navbar_display() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
		ob_start();

		?>
		<?php do_action( 'before_navbar' ); ?>

	      	<div class="navbar-wrapper clearfix span9">

          		<div class="navbar notresp row-fluid pull-left">
          			<div class="navbar-inner" role="navigation">
          				<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
          				<div class="row-fluid">
	            			<?php do_action( '__navbar' ); //hook of social, tagline, menu, ordered by priorities 10, 20, 30?>
	            		</div><!-- .row-fluid -->
	            	</div><!-- /.navbar-inner -->
	            </div><!-- /.navbar notresp -->

	            <div class="navbar resp">
	            	<div class="navbar-inner" role="navigation">
	            		<?php do_action( '__navbar' , 'resp' ); //hook of social, menu, ordered by priorities 10, 20?>
	            	</div><!-- /.navbar-inner -->
          		</div><!-- /.navbar resp -->

        	</div><!-- /.navbar-wrapper -->

        	<?php do_action( '__after_navbar' ); ?>
		<?php

		$html = ob_get_contents();
       	ob_end_clean();
       	echo apply_filters( 'tc_navbar_display', $html );
	}


	


	/**
	 * Displays the social networks block in the header
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0.10
	 */
    function tc_social_in_header($resp = null) {
      	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        ob_start();

        //class added if not resp
        $class 		=  ('resp' == $resp) ? '':'span5' 
        ?>

        	<div class="social-block <?php echo $class ?>">
        		<?php if ( 0 != tc__f( '__get_option', 'tc_social_in_header') ) : ?>
	        		<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
	           		<?php echo tc__f( '__get_socials' ) ?>
	           	<?php endif; ?>
        	</div><!--.social-block-->

        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_social_in_header', $html );
    }





	/**
	 * Displays the tagline. This function has two hooks : __header and __navbar
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_tagline_display() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
		ob_start();
		?>
			<?php if ( '__header' == current_filter() ) : //when hooked on  __header?>
				<div class="container outside">
			        <h2 class="site-description">
			        	 <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
			        	 <?php bloginfo( 'description' ); ?>
			        </h2>
			    </div>
			<?php else : //when hooked on __navbar?>
				<h2 class="span7 inside site-description">
					<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
                      <?php bloginfo( 'description' ); ?>
                </h2>

			<?php endif; ?>

		<?php
		$html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_tagline_display', $html );
	}


}//end of class