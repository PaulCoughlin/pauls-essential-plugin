<?php
/*
Plugin Name: Paul's Essential Plugin
Plugin URI: https://github.com/paulcoughlin/pauls-essential-plugin/
Description: This is a plugin which sets defaults, such as revisions, dashboard cleanup etc.
Version: 1.1
Author: Paul Coughlin
Author URI: http://www.paulcoughlin.com
*/

// Set the revision to maximum 2
define('WP_POST_REVISIONS', 2);

// Create the function to use in the action hook

function paul_remove_dashboard_widgets() {
	
	remove_meta_box( 'dashboard_browser_nag', 'dashboard', 'normal' );
	// remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
} 

// Hook into the 'wp_dashboard_setup' action to register our function

add_action('wp_dashboard_setup', 'paul_remove_dashboard_widgets' );

// Email Obfuscation
// Usage: [obf email="email@example.com" noscript="what's shown to bots/people without javascript"]
function paul_email_obfuscate( $atts )
{
	extract( shortcode_atts( array(
		'email'     => get_settings('admin_email'), // Defaulting to admin email
		'noscript'  => 'myname at thisdomain.com',
	), $atts ) );

    // JavaScript by Allan Odgaard
    // http://pastie.textmate.org/101495 (line 116)
    $javascript = '.replace(/[a-zA-Z]/g, function(c){ return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26 );})';

    return '<script type="text/javascript">'
            . 'document.write("'
            . str_rot13( "<a class='rot13' href='mailto:" . $email . "'>" . $email . '</a>' )
            . '"' . $javascript . ');</script>'
            . '<noscript>' . $noscript . '</noscript>';
}
add_shortcode( 'obf', 'paul_email_obfusctate' );

// Set the default image link to none..
function paul_image_link_setup() {
	$image_link = get_option( 'image_default_link_type' );
	
	if ($image_link !== 'none') {
		update_option('image_default_link_type', 'none');
	}
}
add_action('admin_init', 'paul_image_link_setup', 10);

// Prevent WordPress From Participating In Pingback Denial of Service Attacks
// Hacked from http://wordpress.org/plugins/remove-xmlrpc-pingback-ping

function paul_remove_xmlrpc_pingback_ping( $methods ) {
   unset( $methods['pingback.ping'] );
   return $methods;
}
add_filter( 'xmlrpc_methods', 'paul_remove_xmlrpc_pingback_ping' );
