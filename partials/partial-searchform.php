<?php

	$post_type = 'wp_pedia_term';
  $searchUrl = get_post_type_archive_link( $post_type );

?> 

<form role="search" method="get" class="search-form wppedia-search" action="<?php echo $searchUrl ?>">
  <label class="search-label ghost"><?php _e('Search', 'wppedia'); ?></label>
  <input type="search" class="search-field" placeholder="<?php _e('Search', 'wppedia'); ?>…" value="<?php echo get_search_query() ?>" name="s" title="<?php _e('Search for', 'wppedia'); ?>:" />
  <input type="hidden" name="post_type" value="<?php echo $post_type; ?>" />
	<input type="submit" class="search-submit" value="Search" />
</form>
