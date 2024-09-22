
<?php
$version = "<!#FV> 0.0.1 </#FV>";


add_action('save_post', 'mk_update_related_posts', 10, 3);

function mk_update_related_posts($post_id, $post, $update) {
	  // Set locale in IT
    setlocale(LC_TIME, 'it_IT');
	
    // If this isn't a 'corso-master' post, don't update anything.
    if ('corso-master' != $post->post_type) {
        return;
    }
	
	$check_blocco = get_post_meta($post_id, 'blocco_modifiche', true);
	if ($check_blocco == 'true' ) {
        return;
    }

    // Get the 'tag-corso' and 'categoria-corso' terms from the post.
    $tag_corso_terms = wp_get_post_terms($post_id, 'tag-corso', array('fields' => 'ids'));
    $categoria_corso_terms = wp_get_post_terms($post_id, 'categoria-corso', array('fields' => 'ids'));
	
	// Get the value of 'modalita_erogazione' 
    $modalita_erogazione_value = get_post_meta($post_id, 'modalita_erogazione', true);
	
	// get title of master corso
	$post_title_master = get_the_title($post_id);
	
	$emp = NULL; //empty value for clearing
	
    // Get the related posts using JetEngine's API.
    // get related posts - 4 is the active relation "corsi"
	$relation = jet_engine()->relations->get_active_relations( 4 );
	
	// Get the related post "luoghi"
	// Get related post - 11 is the active relation "luoghi / master corso"
	$relation_luoghi = jet_engine()->relations->get_active_relations( 11 );
	
	// Get active relation "Luoghi - Corsi" ID 14
	$relation_luoghi_corsi = jet_engine()->relations->get_active_relations( 14 );
	$relation_luoghi_corsi->set_update_context( 'parent' );
	
  	$field = 'ids';
	
	// Get children "corsi" from Corso Master
  	$related_posts = $relation->get_children( $post_id, $field );
	// Get children "luoghi" from Corso Master
	$related_post_luoghi = $relation_luoghi->get_children( $post_id, $field );
	
    // Loop through each related post.
    foreach ($related_posts as $related_post) {
        // Set the 'tag-corso' and 'categoria-corso' terms.
        wp_set_post_terms($related_post, $tag_corso_terms, 'tag-corso', false);
        wp_set_post_terms($related_post, $categoria_corso_terms, 'categoria-corso', false);
		
		// get data for each single corsi
		$datacorso = get_post_meta( $related_post, 'datacorso', true );
		
		if (empty($datacorso)) {
        // do nothing
    } else {
		// date exists, get human readable time
		$humandate = strftime('%d %B %Y', $datacorso);
		 // Create complete title with date
    	$complete_title = $post_title_master . " " . $humandate;
		$complete_title_slug = sanitize_title($complete_title);

			 // Unhook this function to avoid infinite loop
    		remove_action('save_post', 'mk_update_related_post');
			

    // Update the post's title and slug
    $updated_post = array(
        'ID'           => $related_post,
        'post_title'   => $complete_title,
        'post_name'    => $complete_title_slug, // the post slug
    );

    // Update the post into the database
    
	wp_update_post( $updated_post );
			// Re-hook this function
    add_action('save_post', 'mk_update_related_post', 10, 3);
		} // trigger only if data corso is popoulated in corsi figlio
			
		
		// Update the 'modalita_erogazione_corso' of the related post with the value of 'modalita_erogazione'
    update_post_meta($related_post, 'modalita_erogazione_corso', $modalita_erogazione_value);
		
		if (empty($related_post_luoghi) ){ // If luoghi in Master is empty,then...
			$relation_luoghi_corsi->delete_rows( $related_post, $emp, true );
		} else {
			/*$related_post_luoghi_corsi = $relation_luoghi_corsi->get_children( $post_id, $field );
			if(in_array($related_post_luoghi[0], $related_post_luoghi_corsi)){
				$relation_luoghi_corsi->delete_rows( $related_post, $related_post_luoghi[0], true );
			}*/
			$relation_luoghi_corsi->update( $related_post, $related_post_luoghi[0] );
		}

    }
	// Update the blocco_mofifiche field
    //update_post_meta($post_id, 'blocco_modifiche', 'true');
}