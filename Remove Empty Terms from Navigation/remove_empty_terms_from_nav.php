<?php

$snippet_name = "remove_empty_terms_from_nav";
$version = "<!#FV> 0.0.1 </#FV>";

/**
 * Remove empty terms from a navigation menu
 * The term must be added to navigation menu as category/taxonomy, not as custom link
 */

add_filter( 'wp_get_nav_menu_items', 'mk_nav_remove_empty_terms', 10, 3 );
function mk_nav_remove_empty_terms( $items, $menu, $args ) {
  foreach ( $items as $key => $item ) {
    if ( 'taxonomy' === $item->type ) {
      $term = get_term( $item->object_id );
      if ( empty( $term ) || empty( $term->count ) ) {
        unset( $items[$key] );
      }
    }
  }
  return $items;
}