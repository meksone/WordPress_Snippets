<?php

$snippet_name = "add_main_wrapper_to_hello_theme";
$version = "<!#FV> 0.0.1 </#FV>";

/**
 * Using template_redirect hook to have more control
 * over where the wrapper appears
 */
function mk_add_main_wrapper_tmpl_redirect() {
    add_action('template_redirect', function() {
        ob_start(function($buffer) {
            // Find the end of header
            $header_pos = strpos($buffer, '</header>');
            if ($header_pos === false) {
                return $buffer;
            }
            
            // Find the start of footer
            $footer_pos = strpos($buffer, '<footer');
            if ($footer_pos === false) {
                return $buffer;
            }
            
            // Insert main tags
            $before_content = substr($buffer, 0, $header_pos + 9);
            $content = substr($buffer, $header_pos + 9, $footer_pos - ($header_pos + 9));
            $after_content = substr($buffer, $footer_pos);
            
            return $before_content . '<main id="main-content" class="site-main mk-transition-main">' . $content . '</main><!-- #main-content -->' . $after_content;
        });
    });
}
// Execute and add main wrapper
mk_add_main_wrapper_tmpl_redirect();