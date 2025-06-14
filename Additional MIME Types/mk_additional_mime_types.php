<?php

$snippet_name = "enable_additional_mimetypes";
$version = "<!#FV> 0.0.3 </#FV>";

/*
 * Enable additional MIME types
 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types
 * */

function mk_additional_mime_types( $mimes ) {
// New allowed mime types.
$mimes['cur'] = 'application/octet-stream';
$mimes['ico'] = 'application/octet-stream';
$mimes['woff'] = 'font/woff';
$mimes['woff2'] = 'font/woff2';
$mimes['otf'] = 'font/otf';
$mimes['eot'] = 'application/vnd.ms-fontobject';
$mimes['ttf'] = 'font/ttf';
$mimes['svg'] = 'image/svg+xml';

// Optional. Remove a mime type.
unset( $mimes['exe'] );
return $mimes;
}
add_filter( 'upload_mimes', 'mk_additional_mime_types' );