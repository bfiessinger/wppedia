<?php

/**
 * Wiki Template Tags
 */

/**
 * Template hook used to display the "Initial Character Navigation"
 */
if ( ! function_exists( 'wp_wiki_tpl_initial_nav' ) ) {

  function wp_wiki_tpl_initial_nav() { ?>

	<nav id="glossary-char-navigation">
		<ul>

		<?php foreach ( wiki_utils()->get_wiki_initial_letters(['hide_empty' => false]) as $slug => $initial ): ?>

			<li>

			<?php if ( term_exists( $slug, 'initialcharacter' ) ): 
				// Get Information about the current term
				$initial_term = get_term_by( 'slug', $slug, 'initialcharacter' );
			?>

				<a href="<?php echo get_post_type_archive_link('wp_pedia_term') . $slug . '/'; ?>" title="<?php echo sprintf(__('Glossary terms with initial character „%s“ (%d)', 'wppedia'), $initial, $initial_term->count); ?>"><?php echo $initial; ?></a>		
				<?php else: /* Term does not exist */ ?>
				<span><?php echo $initial; ?></span>

			<?php endif; ?>

			</li>

		<?php endforeach; ?>

		</ul>
	</nav>

  <?php }

}
add_action( 'wp_wiki_tpl_initial_nav', 'wp_wiki_tpl_initial_nav', 10 );

/**
 * Template hook used to display a list of Glossary entries 
 * based on their initial character
 */
