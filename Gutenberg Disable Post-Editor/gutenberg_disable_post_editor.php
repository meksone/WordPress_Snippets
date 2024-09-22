<?php
$version = "<!#FV> 0.0.2 </#FV>";

/*
 * Disable all Gutenberg blocks for certain post types
 * define post to clean from all blocks in the postTypes array;
 * all other post types have normal Gutenberg editor
 * */


add_filter('allowed_block_types', 'mk_disable_all_blocks', 10, 2);

function mk_disable_all_blocks($allowed_blocks, $post) {
    // Define the post types where Gutenberg blocks should be disabled
    $postTypes = array('film');

    // If the current post type is in the list, disable Gutenberg blocks
    if (in_array($post->post_type, $postTypes)) {
		add_action( 'acf/input/admin_footer', 'mk_fix_post_content_height' );
        return array();
    }

    // Otherwise, allow Gutenberg blocks
    return $allowed_blocks;
}

// Fix the post content height
function mk_fix_post_content_height(){
    // Define the CSS styles
    $styles = "
    :root :where(.editor-styles-wrapper)::after {
        height: unset;
    }
    .editor-visual-editor.edit-post-visual-editor {
        max-height: 150px;
    }
    h1.wp-block.wp-block-post-title.block-editor-block-list__block.editor-post-title.editor-post-title__input.rich-text {
        margin: 15px;
        line-height: 0.9;
    }
    .editor-visual-editor__post-title-wrapper.edit-post-visual-editor__post-title-wrapper {
        margin-top: 1px !important;
    }
    ";
    // Print the styles in the page
    echo "<style>$styles</style>";
}
