<?php
/**
* Social networks hooks
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0.10
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_socials {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;
       
        add_filter  ( '__get_socials'                     , array( $this , 'tc_get_social_networks' ) );
    }



      /**
      * Gets the social networks list defined in customizer options
      * 
      * @package Customizr
      * @since Customizr 3.0.10 
      */
      function tc_get_social_networks() {
        $__options          = tc__f( '__options' );

        $socials = array (
              'tc_rss'            => 'feed',
              'tc_twitter'        => 'twitter',
              'tc_facebook'       => 'facebook',
              'tc_google'         => 'google',
              'tc_instagram'      => 'instagram',
              'tc_wordpress'      => 'wordpress',
              'tc_youtube'        => 'youtube',
              'tc_pinterest'      => 'pinterest',
              'tc_github'         => 'github',
              'tc_dribbble'       => 'dribbble',
              'tc_linkedin'       => 'linkedin'
              );

          $html = '';

          foreach ( $socials as $key => $nw) {
            //all cases except rss
            $title = __( 'Follow me on ' , 'customizr' ).$nw;
            $target = 'target=_blank';
            //rss case
            if ( $key == 'tc_rss' ) {
              $title = __( 'Suscribe to my rss feed' , 'customizr' );
              $target = '';
            }

            if ( $__options[$key] != '' ) {
              //$html .= '<li>';
                $html .= '<a class="social-icon icon-'.$nw.'" href="'.esc_url( $__options[$key]).'" title="'.$title.'" '.$target.'></a>';
            }
         }
         return $html;
      }

}//end of class