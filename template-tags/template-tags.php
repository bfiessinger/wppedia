<?php

/**
 * Wiki Template Tags
 */

/**
 * Template hook used to display the "Initial Character Navigation"
 */
if ( ! function_exists( 'wppedia_tpl_initial_nav' ) ) {

  function wppedia_tpl_initial_nav() { ?>

	<nav id="glossary-char-navigation" class="container px-0">
		<ul class="flex justify-center bg-lightgrey list-none leading-none p-2 mb-8">

		<?php foreach ( wppedia_utils()->get_wiki_initial_letters(['hide_empty' => false]) as $slug => $initial ): ?>

			<li>

			<?php if ( term_exists( $slug, 'initialcharacter' ) ): 
				// Get Information about the current term
				$initial_term = get_term_by( 'slug', $slug, 'initialcharacter' );
			?>

				<a class="block px-3 py-2 bg-grey text-darkgrey" href="<?php echo get_term_link( $initial_term ); ?>" title="<?php echo sprintf(__('Glossary terms with initial character „%s“ (%d)', 'wppedia'), $initial, $initial_term->count); ?>"><?php echo $initial; ?></a>		
				<?php else: /* Term does not exist */ ?>
				<span class="block px-3 py-2 text-grey"><?php echo $initial; ?></span>

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

	<div class="wppedia-entries sm:flex flex-wrap -mx-4 mb-4">

		<?php foreach ( wppedia_utils()->get_current_initial_letters() as $initial ): ?>

			<?php do_action( 'wppedia_tpl_list_entries_single_char', $initial ); ?>

		<?php endforeach; ?>

	</div>

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

	<div class="w-full lg:w-3/12 md:w-4/12 sm:w-6/12 m-4" id="glossary-initial-<?php echo strtoupper( \rawurlencode( $initial_letter ) ); ?>">
		<span class="initial-letter text-primary"><?php echo $initial_letter; ?></span>
		<ul class="initial-letter-listing list-none p-0">
			<?php while ( $initial_query->have_posts() ):	$initial_query->the_post(); ?>
				<li>
					<a class="text-black" href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a>
				</li>
			<?php endwhile; ?>
		</ul>
	</div>

	<?php
			
			// Restore original Postdata
			wp_reset_postdata();

		endif;

	}

}
add_action( 'wppedia_tpl_list_entries_single_char', 'wppedia_tpl_list_entries_single_char', 10, 1 );
