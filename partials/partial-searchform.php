<?php

	/**
	 * Template Part for the WPPedia Searchform
	 * 
	 * @since 1.0.0
	 */

	$post_type = 'wp_pedia_term';
  $searchUrl = get_post_type_archive_link( $post_type );

?> 

<form role="search" method="get" class="search-form wppedia-search" action="<?php echo $searchUrl ?>">
  <label class="screen-reader-text"><?php _e('Search glossary', 'wppedia'); ?></label>
  <input type="search" class="search-field" placeholder="<?php _e('Search glossary', 'wppedia'); ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php _e('Search for', 'wppedia'); ?>:" />
  <input type="hidden" name="post_type" value="<?php echo $post_type; ?>" />
	<input type="submit" class="search-submit" value="Search" />
</form>
