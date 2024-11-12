<?php

$snippet_name = "gutenberg_add_meta_message";
$version = "<!#FV> 0.0.2 </#FV>";
/**
 * More info https://wordpress.stackexchange.com/questions/360418/enhancing-gutenberg-featured-image-control
 */
function mk_gute_add_message() {
    global $post;
    $post_type = get_post_type($post);
    ?>
    <script>
        var el = wp.element.createElement;
        var postType = '<?php echo $post_type; ?>';
        function wrapPostFeaturedImage( OriginalComponent ) {
            return function( props ) {
                var message = 'Default message';
                if (postType === 'film') {
                    message = 'Inserire la locandina del film - formato verticale, larghezza minima consigliata 1024px';
                } else if (postType === 'news') {
                    message = 'Immagine in formato orizzontale, dimensioni consigliate 1920x900px';
                }
                // Add more conditions for other post types as needed

                return (
                    el(
                        wp.element.Fragment,
                        {},
                        '',
                        el(
                            OriginalComponent,
                            props
                        ),
                        el(
                            'div',
                            { className: 'mk-gute-message' },
                            message
                        )
                    )
                );
            }
        }
        wp.hooks.addFilter(
            'editor.PostFeaturedImage',
            'wrap-post-featured-image',
            wrapPostFeaturedImage
        );
    </script>
    <style>
        .mk-gute-message {
            background-color: #f1f1f1;
            padding: 10px;
            font-style: italic;
            color: #666;
            border-radius: 8px;
        }
    </style>
    <?php
}
add_action( 'admin_head-post.php', 'mk_gute_add_message' );
add_action( 'admin_head-post-new.php', 'mk_gute_add_message' );
add_action( 'admin_head-edit.php', 'mk_gute_add_message' );
