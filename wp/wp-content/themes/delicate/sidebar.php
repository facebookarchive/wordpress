<ul>
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>							
<li class="widget png_scale" id="categories_id">
 <h2 class="blocktitle"><span>Categories</span></h2>	
 <ul>
<?php wp_list_categories('orderby=name&show_count=1&hide_empty=0&exclude=,2&title_li='); ?>
</ul>		
</li>
<li class="widget png_scale" id="text_id">
<h2 class="blocktitle">Archive</h2>
<ul>
<?php wp_get_archives('type=monthly'); ?>
</ul>
</li>
<li>
    
<li class="widget png_scale" id="meta">
<h2 class="blocktitle">Meta</h2>
<ul>
<?php wp_register(); ?>
<li><?php wp_loginout(); ?></li>
<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
<li class="rss"><?php $t_feedburnerurl = t_get_option('t_feedburnerurl'); if ($t_feedburnerurl == '') {?>
							<a href="<?php bloginfo('rss2_url'); ?>">Read in RSS</a>
							<?php } else { ?>
							<a href="<?php echo $t_feedburnerurl ?>">Read in RSS</a>
							<?php } ?>

</li>
<?php if(t_get_option('t_twitterurl') != '') { ?>
<li class="twitter"><a href="<?php echo t_get_option('t_twitterurl');?>" title="Twitter profile">Twitter</a></li>
<?php } ?>
<?php wp_meta(); ?>
</ul>
</li>  <?php endif; ?> 
</ul>