<?php

	/**
	 * Template Part for the WPPedia Initial letter navigation
	 * 
	 * @since 1.0.0
	 */

?>
<nav id="wppedia-char-navigation">
	<ul>

	<?php foreach ( bf\wpPedia\helper::getInstance()->get_wiki_initial_letters(['hide_empty' => false, 'show_option_home' => true]) as $slug => $initial ): ?>

		<li><?php echo bf\wpPedia\template::getInstance()->get_char_navigation_link( $slug ); ?></li>

	<?php endforeach; ?>

	</ul>
</nav>