<div style="position:relative;float:left;z-index:0;"><div class="social-trans-left"></div><div class="social-trans-right"></div>
<div class="sc_menu">





<ul class="sc_menu">

<li><a target="_blank" href="<?php $options = get_option('evolve');if ($options['evl_rss_feed'] != "" ) { echo $options['evl_rss_feed']; } else { bloginfo( 'rss_url' ); } ?>" class="tipsytext" id="rss" original-title="RSS Feed"></a></li>

<?php 
  if (!empty($options['evl_newsletter'])) { ?>
<li><a target="_blank" href="<?php $options = get_option('evolve');if ($options['evl_newsletter'] != "" ) echo $options['evl_newsletter']; ?>" class="tipsytext" id="email-newsletter" original-title="Newsletter"></a></li><?php } else { ?><?php } ?>

<?php 
  if (!empty($options['evl_facebook'])) { ?>
<li><a target="_blank" href="http://facebook.com/<?php $options = get_option('evolve');if ($options['evl_facebook'] == "" ) $options['evl_facebook'] = $default_facebook;echo stripslashes($options['evl_facebook']);?>" class="tipsytext" id="facebook" original-title="Facebook"></a></li><?php } else { ?><?php } ?>

<?php 
  if (!empty($options['evl_twitter_id'])) { ?>
<li><a target="_blank" href="http://twitter.com/<?php $options = get_option('evolve');if ($options['evl_twitter_id'] == "" ) $options['evl_twitter_id'] = $default_twitter_id;echo stripslashes($options['evl_twitter_id']);?>" style="text-decoration: none;" class="tipsytext" id="twitter" original-title="Twitter"></a></li><?php } else { ?><?php } ?>

<?php 
  if (!empty($options['evl_googleplus'])) { ?>
<li><a target="_blank" href="http://plus.google.com/<?php $options = get_option('evolve');if ($options['evl_googleplus'] != "" ) echo $options['evl_googleplus']; ?>" class="tipsytext" id="plus" original-title="Google Plus"></a></li><?php } else { ?><?php } ?>

<?php 
  if (!empty($options['evl_myspace'])) { ?>
<li><a target="_blank" href="http://myspace.com/<?php $options = get_option('evolve');if ($options['evl_myspace'] != "" ) echo $options['evl_myspace']; ?>" class="tipsytext" id="myspace" original-title="MySpace"></a></li><?php } else { ?><?php } ?>

<?php 
  if (!empty($options['evl_skype'])) { ?>
<li><a href="skype:<?php $options = get_option('evolve');if ($options['evl_skype'] != "" ) echo $options['evl_skype']; ?>?call" class="tipsytext" id="skype" original-title="Skype"></a></li><?php } else { ?><?php } ?>

<?php 
  if (!empty($options['evl_youtube'])) { ?>
<li><a target="_blank" href="http://youtube.com/user/<?php $options = get_option('evolve');if ($options['evl_youtube'] != "" ) echo $options['evl_youtube']; ?>" class="tipsytext" id="youtube" original-title="YouTube"></a></li><?php } else { ?><?php } ?>

<?php 
  if (!empty($options['evl_flickr'])) { ?>
<li><a target="_blank" href="http://flickr.com/photos/<?php $options = get_option('evolve');if ($options['evl_flickr'] != "" ) echo $options['evl_flickr']; ?>" class="tipsytext" id="flickr" original-title="Flickr"></a></li><?php } else { ?><?php } ?>

<?php 
  if (!empty($options['evl_linkedin'])) { ?>
<li><a target="_blank" href="<?php $options = get_option('evolve');if ($options['evl_linkedin'] != "" ) echo $options['evl_linkedin']; ?>" class="tipsytext" id="linkedin" original-title="LinkedIn"></a></li><?php } else { ?><?php } ?>




</ul>
</div>

</div>