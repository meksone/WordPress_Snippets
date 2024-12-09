

<?php
$snippet_name = "tinymce_customize_buttons";
$version = "<!#FV> 0.0.1 </#FV>";

function mk_add_mce_buttons( $buttons ) {	
	/**
	 * Add in a core button that's disabled by default
	 */
	//$buttons[] = 'superscript';
	//$buttons[] = 'subscript';

	return $buttons;
}
add_filter( 'mce_buttons', 'mk_add_mce_buttons' );

/**
 * Removes buttons from the first row of the tiny mce editor
 *
 * @link     http://thestizmedia.com/remove-buttons-items-wordpress-tinymce-editor/
 *
 * @param    array    $buttons    The default array of buttons
 * @return   array                The updated array of buttons that exludes some items
 */
add_filter( 'mce_buttons', 'mk_remove_mce_buttons');
function mk_remove_mce_buttons( $buttons ) {

    $remove_buttons = array(
        'alignleft',
        'aligncenter',
        'alignright',
        'wp_more', // read more link
        'spellchecker',
        'dfw', // distraction free writing mode
		'fullscreen',
		'formatselect',
		'wp_adv', // line 2 button!
		'code',
    );
    foreach ( $buttons as $button_key => $button_value ) {
        if ( in_array( $button_value, $remove_buttons ) ) {
            unset( $buttons[ $button_key ] );
        }
    }
    return $buttons;
}