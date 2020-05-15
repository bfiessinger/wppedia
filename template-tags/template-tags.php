<?php

/**
 * Wiki Template Tags
 */

/**
 * Template hook used to display the "Initial Character Navigation"
 */
if ( ! function_exists( 'wppedia_tpl_initial_nav' ) ) {

  function wppedia_tpl_initial_nav() { ?>

	<nav id="glossary-char-navigation">
		<ul>

		<?php foreach ( wppedia_utils()->get_wiki_initial_letters(['hide_empty' => false]) as $slug => $initial ): ?>

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
add_action( 'wppedia_tpl_initial_nav', 'wppedia_tpl_initial_nav', 10 );

/**
 * Template hook used to display a list of Glossary entries 
 * based on their initial character
 */
if ( ! function_exists( 'wppedia_tpl_list_entries' ) ) {

	function wppedia_tpl_list_entries() { ?>

		<?php foreach ( wppedia_utils()->get_current_initial_letters() as $initial ): ?>

			<?php do_action( 'wppedia_tpl_list_entries_single_char', $initial ); ?>

		<?php endforeach; ?>

	<?php }

}
add_action( 'wppedia_tpl_list_entries', 'wppedia_tpl_list_entries', 10 );

/**
 * Template hook used to display all Glossary entries
 * from a single initial Character
 */
if ( ! function_exists( 'wppedia_tpl_list_entries_single_char' ) ) {

	function wppedia_tpl_list_entries_single_char( $initial_letter ) {

		$initial_query = wppedia_utils()->get_wiki_entries(['initial_letter' => $initial_letter]);

		if ( $initial_query->have_posts() ):

	?>

		<span class="initial-letter"><?php echo $initial_letter; ?></span>
		<ul>
			<?php while ( $initial_query->have_posts() ): $initial_query->the_post(); ?>
				<li>
					<a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a>
				</li>				
			<?php endwhile; ?>
		</ul>

	<?php
			
			// Restore original Postdata
			wp_reset_postdata();

		endif;

	}

}
add_action( 'wppedia_tpl_list_entries_single_char', 'wppedia_tpl_list_entries_single_char', 10, 1 );
