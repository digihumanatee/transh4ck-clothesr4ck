<?php
/*
Template Name: Custom: CR Requests Page
Author: H Kellaway
Date: 2013/10/20
*/
?>
<?php do_action( '__before_main_wrapper' ); ##hooks the header with get_header ?>
<?php tc__f('rec' , __FILE__ , __FUNCTION__ ); ?>
<div id="main-wrapper" class="container">
    <?php do_action( '__before_main_container' ); ##hooks the featured page (priority 10) and breadcrumb (priority 20)...and whatever you need! ?>
    <div class="container" role="main">

        <div class="row">
            <?php do_action( '__sidebar' , 'left' ); ?>
                <div class="<?php echo tc__f( '__screen_layout' , tc__f ( '__ID' ) , 'class' ) ?> article-container">
                    
                    <?php do_action ('__before_loop');##hooks the header of the list of post : archive, search... ?>
                        
                        <?php
                            global $wp_query;
                            ##do we have posts? If not are we in the no search result case?
                            if ( have_posts() || (is_search() && 0 == $wp_query -> post_count) ) : ?>
                                <?php if ( is_search() && 0 == $wp_query -> post_count ) : ##no search results case ?>
                                    <article <?php tc__f('__article_selectors') ?>>
                                        <?php do_action( '__loop' ); ?>
                                    </article>
                                <?php endif; ?>

                                <?php while ( have_posts() ) : ##all other cases for single and lists: post, custom post type, page, archives, search, 404 ?>
                                    <?php the_post(); ?>
                                    <article <?php tc__f('__article_selectors') ?>>
                                        <?php
                                        do_action( '__loop' );
                                        ##we don't want to display more than one post if 404!
                                        if ( is_404() )
                                            break;
                                        ?>
                                    </article>
                                <?php endwhile; ?>

                                <?php 
                                    global $wpdb;

                                    $user_ID = get_current_user_id();

                                    $query = "SELECT request_ID, item_details, created, expiration_date" . " ";
                                    $query .= "FROM cr_requests" . " ";
                                    $query .= "WHERE user_ID=" . $user_ID . " ";
                                    $query .= "AND is_active=1" . " ";
                                    $query .= "ORDER BY request_type_ID, created";
                
                                    $requests = $wpdb->get_results($query);

                                    if(count($requests) > 0)
                                    {
                                        echo "<p><br /></p>";
                                        echo "<h3>Active</h3>";
                                        echo "<table>";
                                            echo "<tr style='text-align: left'>";
                                                echo "<th>Request ID</th>";
                                                echo "<th>Item Details</th>";
                                                echo "<th>Request Date</th>";
                                                echo "<th>Request Expiration</th>";
                                            echo "</tr>";

                                            foreach($requests as $request)
                                            {
                                                echo "<tr>";
                                                    echo "<td>".$request->request_ID."</td>";
                                                    echo "<td>".$request->item_details."</td>";
                                                    echo "<td>".$request->created."</td>";
                                                    echo "<td>".$request->expiration_date."</td>";
                                                echo "</tr>";
                                            }
                                        echo "</table>";
                                    }
                                    else
                                    {
                                        echo "\n<h3>No Active Requests</h3>";
                                        echo "<a href='../..'>Create new request?</a>";
                                    }
                                ?>

                            <?php endif; ##end if have posts ?>

                    <?php do_action ('__after_loop');##hooks the comments and the posts navigation with priorities 10 and 20 ?>

                </div><!--.article-container -->
            <?php do_action( '__sidebar' , 'right' ); ?>
        </div><!--.row -->
    </div><!-- .container role: main -->
    <?php do_action( '__after_main_container' ); ?>
</div><!--#main-wrapper"-->
<?php do_action( '__after_main_wrapper' );##hooks the footer with get_get_footer ?>