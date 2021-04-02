<nav id="wppedia-char-navigation">
	<ul>

	<?php foreach ( wppedia_get_posts_initial_letter_list(['hide_empty' => false, 'show_option_home' => true]) as $slug => $initial ): ?>

		<li><?php echo bf\wpPedia\template::getInstance()->generate_char_navigation_link( $slug ); ?></li>

	<?php endforeach; ?>

	</ul>
</nav>
