<?php

get_header();

?>

<main id="main" role="main">
      
  <header class="container">
		<h1><?php echo __('Glossary', 'messring'); ?></h1>
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

	<div class="container">
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
