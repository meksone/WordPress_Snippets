<?php

$snippet_name = "ACF_ACFE_disable_options";
$version = "<!#FV> 0.0.1 </#FV>";

// Using ACF hooks
// https://www.acf-extended.com/features/wordpress/options
add_action('acf/init', 'mk_disable_acf_modules');
function mk_disable_acf_modules(){
	
	// Disable ACF Settings
	acf_update_setting('preload_blocks', false);
    // Disable ACFE Settings
    acf_update_setting('acfe/modules/options', false);
	acf_update_setting('acfe/modules/forms', false);
	acf_update_setting('acfe/modules/block_types', false);
	acf_update_setting('acfe/modules/options_pages', false);
	acf_update_setting('acfe/modules/post_types', false);
	acf_update_setting('acfe/modules/taxonomies', false);
	acf_update_setting('acfe/modules/multilang', false);
	acf_update_setting('acfe/modules/categories', false);
	acf_update_setting('acfe/modules/author', false);
}