<?php

$snippet_name = "update_title_suffix_to_title";
$version = "<!#FV> 0.0.1 </#FV>";

function mk_update_corsi( $post_id, $post, $update ) {
	// Set locale in IT
	setlocale( LC_TIME, 'it_IT' );

	// If this isn't a 'corso-master' post, don't update anything.
	if ( 'corsi' != $post->post_type ) {
		return;
	}


	// get data for actual corsi
	$datacorso = get_post_meta( $post_id, 'datacorso', true );
	$datacorsoFine = get_post_meta( $post_id, 'datacorso_fine', true );


	$humandate = date( 'd F Y', $datacorso );
	$shortDate = date( 'd-m-Y', $datacorso );
	

	$titleSuffix = get_post_meta( $post_id, 'suffisso_titolo', true );

	if ( empty( $titleSuffix ) ) {
		$titleSuffix = "";
	}

	// Create complete title with date
	if ( empty( $datacorsoFine ) ) {
		$complete_title = $post_title_master . " " . $titleSuffix . " " . $shortDate;
		$complete_title_noDate = $post_title_master . " " . $titleSuffix;
		$complete_title_slug = sanitize_title( $complete_title );
	} else {
		// Alternate date formatting with range
		$altDateStart = date( '%d-', $datacorso );
		$altDateEnd = date( '%d-%m-%Y', $datacorsoFine );
		$complete_title = $post_title_master . " " . $titleSuffix . " " . $altDateStart . $altDateEnd;
		$complete_title_noDate = $post_title_master . " " . $titleSuffix;
		$complete_title_slug = sanitize_title( $complete_title );
	}

	// Unhook this function to avoid infinite loop
	remove_action( 'save_post', 'mk_update_corsi' );


	// Update the post's title and slug
	$updated_post = array(
		'ID' => $post_id,
		'post_title' => html_entity_decode( $complete_title_noDate ),
		'post_name' => $complete_title_slug, // the post slug
	);

	// Update the post into the database

	wp_update_post( $updated_post );
	// Re-hook this function
	add_action( 'save_post', 'mk_update_corsi', 10, 3 );



} // END

add_action( 'save_post', 'mk_update_corsi', 10, 3 );