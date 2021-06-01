<?php
/**
 * WPPedia char navigation
 * 
 * This template can be overridden by copying it to yourtheme/wppedia/nav/char-navigation.php
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
 * @version 1.2.0
 */

?>

<nav id="wppedia-char-navigation">
	<ul>

	<?php foreach ( wppedia_get_posts_initial_letter_list(['hide_empty' => false, 'show_option_home' => true]) as $slug => $initial ): ?>

		<li><?php echo WPPedia\template::getInstance()->generate_char_navigation_link( $slug ); ?></li>

	<?php endforeach; ?>

	</ul>
</nav>
