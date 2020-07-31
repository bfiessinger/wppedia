<?php

	/**
	 * Template Part for the WPPedia Initial letter navigation
	 * 
	 * @since 1.0.0
	 */

?>
<nav id="wppedia-char-navigation">
	<ul>

	<?php foreach ( wppedia_utils()->get_wiki_initial_letters(['hide_empty' => false]) as $slug => $initial ): ?>

		<li><?php echo wppedia_navigation_link( $slug ); ?></li>

	<?php endforeach; ?>

	</ul>
</nav>