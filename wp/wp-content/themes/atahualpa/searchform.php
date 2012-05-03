<form method="get" class="searchform" action="<?php echo esc_url( home_url() ); ?>/">
<table class="searchform" cellpadding="0" cellspacing="0" border="0">
<tr>
<td class="searchfield">
<input type="text" class="text inputblur" value="<?php esc_attr( the_search_query() ); ?>" name="s" />
</td>
<td class="searchbutton">
<input name="submit" value="Search" type="image" src="<?php echo get_template_directory_uri(); ?>/images/magnifier2-gray.gif" style="display: block; border:none; padding: 0 0 0 5px; margin: 0;" />
</td>
</tr></table>
</form>