<?php

get_header();

?>

<div class="wrapper page-section">
  <section id="main" class="site-main" role="main" itemscope="" itemprop="mainContentOfPage" itemtype="https://schema.org/WebPageElement">
    <div class="contentwrap">
      
      <header>
				<h1><?php echo __('Glossary', 'messring'); ?></h1>
				<?php get_search_form();?>
      </header>

<?php
/**
 * wppedia_tpl_initial_nav hook
 *
 * @hooked wppedia_tpl_initial_nav -  10
 *
 */
do_action( 'wppedia_tpl_initial_nav' );

/**
 * wppedia_tpl_list_entries hook
 * 
 * @hooked wppedia_tpl_list_entries - 10
 * 
 */
do_action( 'wppedia_tpl_list_entries' );

get_footer();
