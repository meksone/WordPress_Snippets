<?php
$snippet_name="custom_admin_dashboard_font";
$version = "<!#FV> 0.0.1 </#FV>";

// WordPress Admin Area Custom Font
function custom_admin_dashboard_font() {
    //echo '<link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">' . PHP_EOL;
    echo '<style>body, #wpadminbar *:not([class="ab-icon"]), .wp-core-ui, .media-menu, .media-frame *, .media-modal *{font-family: -apple-system, BlinkMacSystemFont, "Roboto","Roboto Condensed", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;} </style>' . PHP_EOL;
}
add_action( 'admin_head', 'custom_admin_dashboard_font' );