<?php
function nattywp_more_themes_page(){  
        ?>
        <div class="wrap">
          <h2>More NattyWP Themes</h2>
          <div class="info">
          <a href="http://www.nattywp.com/themes-club.php">Join the NattyWP Club</a> / <a href="http://www.nattywp.com/nattywp-services.php">NattyWP Services</a></div>          
          
            <?php 
            include_once(ABSPATH . WPINC . '/feed.php'); //class-simplepie.php
            $rss = fetch_feed('http://www.nattywp.com/feed/rss.xml');
            $rss->strip_attributes(false);

            // RSS is failed.
            if ( is_wp_error($rss) ) {                        
              $error = $rss->get_error_code();
              if($error == 'simplepie-error') {            
                //Simplepie Error
                echo "<div class='updated fade'><p>An error has occured with the RSS feed. (<code>". $error ."</code>)</p></div>";              }            
            return;        
            }          
            $maxitems = $rss->get_item_quantity(100); 
            $items = $rss->get_items(0, $maxitems); 
            ?>

            <ul class="themes">
            <?php if (empty($items) || $maxitems == 0) echo '<li>No items</li>';
            else
            foreach ( $items as $item ) : ?>
                <li class="theme">
                <?php echo $item->get_description(); ?>
                </li>
            <?php endforeach; ?>
            </ul>            
            </div>         
         <?php
}
?>