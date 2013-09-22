<?php
/**
* Posts content actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_post_list {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        //header of the list of post : archive, search...
        add_action  ( '__before_loop'                 , array( $this , 'tc_list_header' ));
        

        //posts parts actions
        add_action  ( '__before_content'              , array( $this , 'tc_post_list_header' ));
        //add_action  ( '__after_content'               , array( $this , 'tc_post_list_footer' ));


        //defines the article layout : content + thumbnail
        add_action  ( '__loop'                        , array( $this , 'tc_post_list_layout'));

        //selector filter
        add_filter  ( '__article_selectors'           , array( $this , 'tc_post_list_selectors' ));

        //Include attachments in search results
        add_filter  ( 'pre_get_posts'                 , array( $this , 'tc_include_attachments_in_search' ));
    }




    /**
     * The default template for displaying posts lists.
     *
     * @package Customizr
     * @since Customizr 3.0.10
     */
    function tc_post_list_layout() {

      global $wp_query;
      //must be archive or search result. Returns false if home is empty in option.
      if ( is_singular() || is_404() || (is_search() && 0 == $wp_query -> post_count) || tc__f( '__is_home_empty') )
        return;

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__, 'right'); 
        //we get the thumbnail if any
        $this -> tc_get_post_list_thumbnail();

        //alternative priority for content and thumbnail
        global $wp_query;
        $thumb_priority = ( 0 == $wp_query->current_post % 2 ) ? 30 : 10 ;
        add_action ('__post_list_layout'      , array( $this, 'tc_post_list_content' ) , 20);
        add_action ('__post_list_layout'      , array( $this, 'tc_post_list_thumbnail' ) , $thumb_priority);
        
        //renders the layout
        do_action ('__post_list_layout');

        //clean hooks actions until next loop
        remove_all_actions( '__post_list_layout' );
        ?>
          <hr class="featurette-divider">
      <?php
    }
    



    /**
     * The template part for displaying the posts header
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_post_list_header() {

      if ( is_singular() )
        return;

      if( in_array( get_post_format(), tc__f('post_type_with_no_headers') ) )
        return;

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      ob_start();

        ?>
          <header class="entry-header">
          <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__); ?>
          <?php //bubble color computation
              $style                      = ( 0 == get_comments_number() ) ? 'style="color:#ECECEC" ':'';

                  if ((get_the_title() != null)) {
                      printf( 
                          '<h2 class="entry-title format-icon">%1$s %2$s</h2>' ,
                          '<a href="'.get_permalink().'" title="'.esc_attr( sprintf( __( 'Permalink to %s' , 'customizr' ), the_title_attribute( 'echo=0' ) ) ).'" rel="bookmark">'.((get_the_title() == null) ? __( '{no title} Read the post &raquo;' , 'customizr' ):get_the_title()).'</a>' ,
                          //check if comments are opened AND if there are comments to display
                          (comments_open() && get_comments_number() != 0) ? '<span class="comments-link"><span '.$style.' class="fs1 icon-bubble"></span><span class="inner">'.get_comments_number().'</span></span>' : ''
                      );
                  }
              ?>
              <div class="entry-meta">
                  <?php //meta not displayed on home page, only in archive or search pages
                      if ( !tc__f('__is_home') ) { 
                          do_action( '__post_metas' );
                      }
                  ?>

              </div><!-- .entry-meta -->
            
          </header><!-- .entry-header -->
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_post_list_header', $html );

    }





    /**
     * Displays the posts list text content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_post_list_content() {
      global $wp_query;
      if ( is_singular() || is_404() || (is_search() && 0 == $wp_query -> post_count) )
        return;

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      ob_start();
      global $tc_has_thumbnail;
      $content_class        = ( $tc_has_thumbnail ) ? 'span8' : 'span12';
        ?>

          <section class="tc-content <?php echo $content_class; ?>">
              
          <?php do_action( '__before_content' ); ?>
              

              <?php //bubble color computation
                  $style                  = ( 0 == get_comments_number() ) ? 'style="color:#AFAFAF" ':'';
              ?>
              
              <?php //display an icon for div if there is no title
                      $icon_class = in_array(get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' )) ? 'format-icon':'';
                  ?>
              <?php if (!get_post_format()) :  // Only display Excerpts for lists of posts with format different than quote, status, link, aside ?>
                  
                  <section class="entry-summary">
                    <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__); ?>
                      <?php the_excerpt(); ?>
                  </section><!-- .entry-summary -->
              
              <?php elseif ( in_array(get_post_format(), array( 'image' , 'gallery' ))) : ?>
                  
                  <section class="entry-content">
                      <p class="format-icon"></p>
                  </section><!-- .entry-content -->
              
              <?php else : ?>
              
                  <section class="entry-content <?php echo $icon_class ?>">
                      <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
                      <?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>
                  </section><!-- .entry-content -->
              <?php endif; ?>


          <?php do_action( '__after_content' ) ?>
                  
          </section>
        <?php
      $html = ob_get_contents();
      ob_end_clean();
      echo apply_filters( 'tc_post_list_content', $html );
    }



      /**
      * Gets the thumbnail or the first images attached to the post if any
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_get_post_list_thumbnail() {

        global $wp_query;

        if ( is_singular() || is_404() || (is_search() && 0 == $wp_query -> post_count) )
          return;

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        //output vars declaration
        $tc_thumb            = '';
        $tc_thumb_height      = '';
        $tc_thumb_width       = '';

        //define the default thumb size
        $tc_thumb_size                  = 'tc-thumb';

        //define the default thumnail if has thumbnail
        if (has_post_thumbnail()) {
            $tc_thumb_id                = get_post_thumbnail_id();

            //check if tc-thumb size exists for attachment and return large if not
            $image                      = wp_get_attachment_image_src( $tc_thumb_id, $tc_thumb_size);
            if (null == $image[3]) {
              $tc_thumb_size            = 'medium';
             }
            $tc_thumb                   = get_the_post_thumbnail( get_the_ID(),$tc_thumb_size);
            //get height and width
            $tc_thumb_height            = $image[2];
            $tc_thumb_width             = $image[1];
        }

        //check if there is a thumbnail and if not uses the first attached image
        else {
          //Case if we display a post or a page
           if ( 'attachment' != tc__f('__post_type') ) {
             //look for attachements in post or page
             $tc_args = array(
                        'numberposts'             =>  1,
                        'post_type'               =>  'attachment' ,
                        'post_status'             =>  null,
                        'post_parent'             =>  get_the_ID(),
                        'post_mime_type'          =>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' )
                );

              $attachments              = get_posts( $tc_args);
            }

            //case were we display an attachment (in search results for example)
            elseif ( 'attachment' == tc__f('__post_type') && wp_attachment_is_image() ) {
              $attachments = array( get_post() );
            }


          if ( $attachments) {
            foreach ( $attachments as $attachment) {
               //check if tc-thumb size exists for attachment and return large if not
              $image                = wp_get_attachment_image_src( $attachment->ID, $tc_thumb_size);
              if (false == $image[3]) {
                $tc_thumb_size      = 'medium';
               }
              $tc_thumb             = wp_get_attachment_image( $attachment->ID, $tc_thumb_size);
              //get height and width
              $tc_thumb_height      = $image[2];
              $tc_thumb_width       = $image[1];
            }
          }
        }
        global $tc_has_thumbnail;
        $tc_has_thumbnail = empty($tc_thumb) ? false : true;

        return apply_filters( 'tc_get_post_list_thumbnail', array($tc_thumb, $tc_thumb_width, $tc_thumb_height) );

      }//end of function
        





      /**
      * Displays the thumbnail or the first images attached to the post if any
      *
      * @package Customizr
      * @since Customizr 3.0.10
      */
      function tc_post_list_thumbnail() {
        global $tc_has_thumbnail;
        if ( !$tc_has_thumbnail )
          return;
       
        global $wp_query;

        if ( is_singular() || is_404() || (is_search() && 0 == $wp_query -> post_count) )
          return;

        //get tc_thumb = array($tc_thumb, $tc_thumb_width, $tc_thumb_height)
        $tc_thumb = $this -> tc_get_post_list_thumbnail();

        //handle the case when the image dimensions are too small
        $no_effect_class            = '';
        if (isset( $tc_thumb[0]) && ( $tc_thumb[1] < 270)) {
          $no_effect_class          = 'no-effect';
        }

        //render the thumbnail
        if ( isset( $tc_thumb)) {
              $html             = '<section class="tc-thumbnail span4">';
                 $html          .= '<div class="thumb-wrapper '.$no_effect_class.'">';
                    $html           .=  '<a class="round-div '.$no_effect_class.'" href="'.get_permalink( get_the_ID() ).'" title="'.get_the_title( get_the_ID()).'"></a>';
                    //$html         .= '<div class="round-div"></div>';
                      $html             .= $tc_thumb[0];
                $html           .= '</div>';
              $html             .= '</section><!--.thumb_class-->'.tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__);

          echo apply_filters( 'tc_post_list_thumbnail', $html );
        }//endif

      }//end of function



      /**
     * Displays the conditional selectors of the article
     * 
     * @package Customizr
     * @since 3.0.10
     */
    function tc_post_list_selectors () {
        //must be archive or not-null search result. Returns false if home is empty in option.
        global $wp_query;
        if ( is_singular() || is_404() || (is_search() && 0 == $wp_query -> post_count) || tc__f( '__is_home_empty') )
          return;

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        echo 'id="post-'.get_the_ID().'" '.tc__f('__get_post_class' , 'row-fluid');
    }








    /**
     * The template part for displaying additional header for posts list.
     *
     * @package Customizr
     * @since Customizr 1.0
     */
    function tc_list_header() {
      if ( is_singular() )
            return;
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
    ?>
    
    <?php if ( is_search()) : ?>

    <?php ob_start(); ?>
      <header class="search-header">
      <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>  
        <h1 class="format-icon">
          <?php 
            printf( __( '%1sSearch Results for: %2s' , 'customizr' ), 
            have_posts() ? '' :  __( 'No' , 'customizr' ).'&nbsp;' ,
            '<span>' . get_search_query() . '</span>' );
          ?>
        </h1>
        
      </header>

      <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_search_list_header', $html );
      ?>

    <?php elseif ( is_author()) : ?>

      <?php
      /* Get the user ID. */
      $user_id = get_query_var( 'author' );
      ?>
        <?php ob_start(); ?>
        <header class="archive-header">
          <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
          <h1 class="format-icon"><?php printf( __( 'Author Archives: %s' , 'customizr' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( $user_id ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' , $user_id ) ) . '" rel="me">' . get_the_author_meta( 'display_name' , $user_id ) . '</a></span>' ); ?></h1>
          <?php if ( get_the_author_meta( 'description', $user_id  ) ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries. ?>
                <hr class="featurette-divider">
                <div class="author-info">
                  <div class="row-fluid">
                    <div class="comment-avatar author-avatar span2">
                       <?php echo get_avatar( get_the_author_meta( 'user_email', $user_id ), apply_filters( 'tc_author_bio_avatar_size' , 100 ) ); ?>
                    </div><!-- .author-avatar -->
                    <div class="author-description span10">
                        <h2><?php printf( __( 'About %s' , 'customizr' ), get_the_author($user_id) ); ?></h2>
                        <p><?php the_author_meta( 'description' , $user_id  ); ?></p>
                    </div><!-- .author-description -->
                  </div>
                </div><!-- .author-info -->
            <?php endif; ?>

        </header><!-- .archive-header -->
       <?php
          $html = ob_get_contents();
          ob_end_clean();
          echo apply_filters( 'tc_author_header', $html );
        ?>

    <?php elseif ( is_category()) : ?>
      <?php ob_start(); ?>
      <header class="archive-header">
        <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
        <h1 class="format-icon"><?php printf( __( 'Category Archives: %s' , 'customizr' ), '<span>' . single_cat_title( '' , false ) . '</span>' ); ?></h1>

        <?php if ( category_description() ) : // Show an optional category description ?>
          <div class="archive-meta"><?php echo category_description(); ?></div>
        <?php endif; ?>
      </header><!-- .archive-header -->
       <?php
          $html = ob_get_contents();
          ob_end_clean();
          echo apply_filters( 'tc_category_header', $html );
        ?>

    <?php elseif ( is_tag()) : ?>
      <?php ob_start(); ?>
      <header class="archive-header">
        <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
        <h1 class="format-icon"><?php printf( __( 'Tag Archives: %s' , 'customizr' ), '<span>' . single_tag_title( '' , false ) . '</span>' ); ?></h1>

      <?php if ( tag_description() ) : // Show an optional tag description ?>
        <div class="archive-meta"><?php echo tag_description(); ?></div>
      <?php endif; ?>
      </header><!-- .archive-header -->
      <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_tag_header', $html );
      ?>
    <?php elseif ( is_archive()) : ?>

      <?php ob_start(); ?>
      <header class="archive-header">
        <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
        <h1 class="format-icon"><?php
          if ( is_day() ) :
            printf( __( 'Daily Archives: %s' , 'customizr' ), '<span>' . get_the_date() . '</span>' );
          elseif ( is_month() ) :
            printf( __( 'Monthly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'F Y' , 'monthly archives date format' , 'customizr' ) ) . '</span>' );
          elseif ( is_year() ) :
            printf( __( 'Yearly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'Y' , 'yearly archives date format' , 'customizr' ) ) . '</span>' );
          else :
            _e( 'Archives' , 'customizr' );
          endif;
        ?></h1>
      </header><!-- .archive-header -->
      <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_time_archive_header', $html );
      ?>

    <?php endif; ?>

    <?php 
      //displays the hr after the title, if we are not on home page or the blog page.
      global $wp_query;
      echo ( tc__f('__is_home') || $wp_query -> is_posts_page ) ? '' : '<hr class="featurette-divider">';

    }//end of function






      /**
      * Includes attachments in search results
      *
      * @package Customizr
      * @since Customizr 3.0.10
      */
      function tc_include_attachments_in_search( $query ) {
          if (! is_search() )
            return $query;    

          // add post status 'inherit' 
          $post_status = $query->get( 'post_status' );
          if ( ! $post_status || 'publish' == $post_status )
            $post_status = array( 'publish', 'inherit' );
          if ( is_array( $post_status ) ) 
            $post_status[] = 'inherit';

          $query->set( 'post_status', $post_status );
         
          return $query;
      }

}//end of class