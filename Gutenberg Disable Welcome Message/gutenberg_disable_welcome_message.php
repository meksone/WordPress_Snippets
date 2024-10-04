<?php

$snippet_name = "gutenberg_disable_welcome_message";
$version = "<!#FV> 0.0.1 </#FV>";

/**
 * More info https://www.wpexplorer.com/disable-welcome-to-the-block-editor/
 */
function mk_disable_editor_welcome_message() {
	?>
	<script>
		window.onload = (event) => {
			wp.data && wp.data.select( 'core/edit-post' ).isFeatureActive( 'welcomeGuide' ) && wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'welcomeGuide' );
		};
	</script>
	<?php
}
add_action( 'admin_head-post.php', 'mk_disable_editor_welcome_message' );
add_action( 'admin_head-post-new.php',  'mk_disable_editor_welcome_message' );
add_action( 'admin_head-edit.php', 'mk_disable_editor_welcome_message' );