<?php

$snippet_name = "check_duplicate_custom_field";
$version = "<!#FV> 0.0.1 </#FV>";

/**
 * Check if there's more of one post with a custom field set (boolean) with same value;
 * Pop up a warning message in post message screen with a list of posts
 */

 add_action('admin_notices', 'check_in_evidenza');
 
 function check_in_evidenza() {
     $screen = get_current_screen();
     
     if($screen->id != 'edit-film') {
         return;
     }
 
     $args = array(
         'post_type' => 'film',
         'meta_query' => array(
             array(
                 'key' => 'in_evidenza',
                 'value' => '1', // or 'true' if it's stored as a string
                 'compare' => '=',
             ),
         ),
     );
 
     $query = new WP_Query($args);
 
     if($query->found_posts > 1) {
         echo '<div class="notice notice-warning is-dismissible">';
         echo '<h4><strong>⚠️ Attenzione!</h4><p>Più di un post è indicato come "in evidenza"! Di seguito la lista:</p>';
         echo '<ul>';
 
         while($query->have_posts()) {
             $query->the_post();
             echo '<li>🟠<a href="'.get_edit_post_link().'">'.get_the_date() ." - ".   get_the_title().'</a></li>';
         }
 
         echo '</ul>';
         echo '</div>';
 
         wp_reset_postdata();
     } else if ($query->found_posts == 0) {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<h4><strong>⚠️ Attenzione!</h4><p>Nessun film in evidenza!</p>';
        echo '</div>';
     }
 }