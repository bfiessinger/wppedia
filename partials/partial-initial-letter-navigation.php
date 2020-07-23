<?php

	/**
	 * Template Part for the WPPedia Initial letter navigation
	 * 
	 * @since 1.0.0
	 */

?> 
<nav id="glossary-char-navigation" class="container px-0">
	<ul class="flex justify-center bg-lightgrey list-none leading-none p-2 mb-8">

	<?php foreach ( wppedia_utils()->get_wiki_initial_letters(['hide_empty' => false]) as $slug => $initial ): ?>

		<li>

		<?php if ( term_exists( $slug, 'wppedia_initial_letter' ) ): 
			// Get Information about the current term
			$initial_term = get_term_by( 'slug', $slug, 'wppedia_initial_letter' );
		?>

			<a class="block px-3 py-2 bg-grey text-darkgrey" href="<?php echo get_term_link( $initial_term ); ?>" title="<?php echo sprintf(__('Glossary terms with initial character „%s“ (%d)', 'wppedia'), $initial, $initial_term->count); ?>"><?php echo $initial; ?></a>		
			<?php else: /* Term does not exist */ ?>
			<span class="block px-3 py-2 text-grey"><?php echo $initial; ?></span>

		<?php endif; ?>

		</li>

	<?php endforeach; ?>

	</ul>
</nav>