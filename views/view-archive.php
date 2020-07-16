<?php

get_header();

?>

<main id="main" role="main">
			
	<div class="container">
		<header class="flex justify-between">
			<h1><?php echo __('Glossary', 'wppedia'); ?></h1>
			<?php get_wppedia_searchform(); ?>
		</header>

		<?php

		/**
		 * wppedia_tpl_initial_nav hook
		 *
		 * @hooked wppedia_tpl_initial_nav -  10
		 *
		 */
		do_action( 'wppedia_tpl_initial_nav' ); ?>

		<?php
		/**
		 * wppedia_tpl_list_entries hook
		 * 
		 * @hooked wppedia_tpl_list_entries - 10
		 * 
		 */
		do_action( 'wppedia_tpl_list_entries' ); ?>
	</div>

</main>

<?php
get_footer();
