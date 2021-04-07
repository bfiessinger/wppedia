<?php
/**
 * WPPedia search form
 * 
 * This template can be overridden by copying it to yourtheme/wppedia/search/form.php
 * 
 * ATTENTION!
 * In case WPPedia needs to make changes to the template files, you (the theme developer)
 * will need to copy these new template files to maintain compatibility.
 * 
 * Whenever we make changes to the template files we will bump the version and list all changes
 * in the CHANGELOG.md file.
 * 
 * Happy editing!
 * 
 * @see https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package wppedia
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hook: wppedia_before_search_form.
 *
 */
do_action('wppedia_before_search_form');

?>
<form <?php echo wppedia_get_search_form_attrs(); ?>>
	<label class="screen-reader-text"><?php _e('Search glossary', 'wppedia'); ?></label>	
	<input type="search" class="search-field" id="<?php echo apply_filters( 'wppedia_search_input_id', 'wppedia_search_input' ); ?>" placeholder="<?php _e('Search glossary', 'wppedia'); ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php _e('Search for', 'wppedia'); ?>:" autocomplete="off" />
	<?php
	/**
	 * If any nice search is active try to get around this and
	 * add a query parameter.
	 */
	global $wp_rewrite;
	if ( isset( $wp_rewrite->search_structure ) ): ?>
	<input type="hidden" name="WPPedia" value="true" />
	<?php endif; ?>
	<input type="submit" class="search-submit" value="Search" />
</form>

<?php

/**
 * Hook: wppedia_after_search_form.
 *
 */
do_action('wppedia_after_search_form');
