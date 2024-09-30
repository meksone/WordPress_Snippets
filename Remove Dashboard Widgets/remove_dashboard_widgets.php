<?php

$snippet_name = "remove_dashboard_widgets";
$version = "<!#FV> 0.0.6 </#FV>";
remove_action('welcome_panel', 'wp_welcome_panel'); // remove the welcome panel - doesn't work from 6.0 onward...

add_action( 'wp_dashboard_setup', 'mk_rm_meta_boxes' );
function mk_rm_meta_boxes()
{
	//remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal');				// Site Health widget
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' ); 				// Right Now widget that tells you post/comment counts	
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );			// Recent comments widget
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );			// Incoming links widget
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );					// Plugins widgets that displays the most popular plugins
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );				// Quick press widget that allows you post right from the dashboard
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );				// Widget containing the list of recent drafts
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );					// WordPress Blog widget
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );					// Other WordPress News widget
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');					// Activity widget
	remove_meta_box( 'e-dashboard-overview', 'dashboard', 'normal');				// Elementor Overview Dashboard
	remove_meta_box( 'duplicator_dashboard_widget', 'dashboard', 'normal');			// Duplicator widget
	remove_action('welcome_panel', 'wp_welcome_panel');								// WordPress Welcome Panel
	remove_meta_box( 'sq_dashboard_widget', 'dashboard', 'normal');					// SqirrlySEO
	remove_meta_box( 'wc_admin_dashboard_setup', 'dashboard', 'normal');			// Remove WooCommerce
	remove_meta_box( 'wp_mail_smtp_reports_widget_lite', 'dashboard', 'normal');	// Remove WP Mail SMTP
}