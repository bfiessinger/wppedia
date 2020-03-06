<div class="--wp-pedia-admin-listing">

<?php

$paged = ( isset( $_GET['paged'] ) && $_GET['paged'] ) ? $_GET['paged'] : 1;

$wiki_query = $this->wp_query_all_initial_letters([
  'paged' => $paged
]);

if ( $wiki_query->have_posts() ) {
  while ( $wiki_query->have_posts() ) {
    $wiki_query->the_post();

    echo '<div>';
    echo get_the_title();
    echo '</div>';

  }
  wp_reset_postdata();
}
 
// Pagination
echo paginate_links( array(
  'base' => str_replace( $paged + 1, '%#%', esc_url( get_pagenum_link( $paged + 1 ) ) ),
  'format' => '?paged=%#%',
  'current' => max( 1, $paged ),
  'total' => $wiki_query->max_num_pages
) );

?>

</div>
