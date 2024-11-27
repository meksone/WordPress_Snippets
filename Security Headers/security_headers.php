<?php

$snippet_name = "security_headers";
$version = "<!#FV> 0.0.3 </#FV>";
/*
 * Google Recaptcha V3
 * Youtube
 * Google Tag Manager
 * Google Fonts
 * Gravatar.com
 * 
 * Custom sites
 * alcinema.it
 */

function add_security_headers() {
    // Strict Transport Security
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    // Content Security Policy
	header("Content-Security-Policy: default-src 'self' 'unsafe-inline' https://fonts.googleapis.com; connect-src 'self' 'unsafe-inline' https://* ; frame-src *.alcinema.it *.youtube.com youtube.com *.youtube-nocookie.com *.google.com; img-src 'self' https://secure.gravatar.com data:; font-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com data:; script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: https://www.youtube.com https://www.googletagmanager.com https://www.google.com https://www.gstatic.com; worker-src 'self' blob:");
	// X Content Type Options
    header("X-Content-Type-Options: nosniff");
    // Referrer Policy
    header("Referrer-Policy: no-referrer");
    // Permissions Policy
    header("Permissions-Policy: geolocation=*");
    // framing policy
    header("X-Frame-Options: SAMEORIGIN");
    // XSS protection
    header("X-XSS-Protection: 1; mode=block");
    // Prevent resource abuse
    header("X-Permitted-Cross-Domain-Policies: none");
    // Features permitted
    header("Feature-Policy: camera 'none'; fullscreen 'self'; microphone 'self'; compute-pressure 'self' https://youtube.com");
}

add_action('send_headers', 'add_security_headers');