<?php
/**
 * Displayed when no entries are found matching the current query
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
 * @version 1.1.3
 */

defined( 'ABSPATH' ) || exit;

global $cur_initial;
$last_initial = ($cur_initial) ? $cur_initial : false;

$cur_initial = wppedia_get_post_initial_letter(get_the_ID());
		
if ($last_initial !== $cur_initial) { ?>
<div class="wppedia-char-section-indentifier">
	<a id="wppedia-section-char-<?php echo $cur_initial; ?>" class="wppedia-anchor" aria-hidden="true" href="#wppedia-section-char-<?php echo $cur_initial; ?>">
		<svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true">
			<path fill-rule="evenodd" fill="currentColor" d="M7.775 3.275a.75.75 0 001.06 1.06l1.25-1.25a2 2 0 112.83 2.83l-2.5 2.5a2 2 0 01-2.83 0 .75.75 0 00-1.06 1.06 3.5 3.5 0 004.95 0l2.5-2.5a3.5 3.5 0 00-4.95-4.95l-1.25 1.25zm-4.69 9.64a2 2 0 010-2.83l2.5-2.5a2 2 0 012.83 0 .75.75 0 001.06-1.06 3.5 3.5 0 00-4.95 0l-2.5 2.5a3.5 3.5 0 004.95 4.95l1.25-1.25a.75.75 0 00-1.06-1.06l-1.25 1.25a2 2 0 01-2.83 0z"></path>
		</svg>
	</a>
	<span class="char">
		<?php echo strtoupper($cur_initial); ?>
	</span>
</div>
<?php }
