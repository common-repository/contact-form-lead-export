<?php
/**
 * Plugin Name: Contact Form Lead Export
 * Plugin URI:  http://matthewklodtwebdesign.elementfx.com/portfolio
 * Description: This plugin takes submissions from forms built with Contact Form 7 and automatically adds them to an external XML file that can be exported for import into a CRM or other database
 * Version:     1.0
 * Author:      Matthew Jason Klodt
 * Author URI:  http://matthewklodtwebdesign.elementfx.com/
 * Text Domain: contact-form-lead-export
 * License:     GPLv2 or later
 */
 // Define CF7LE directory and my plugin file name
define('WPCF7LE_PLUGIN_FILE_NAME', 'contact-form-lead-export');
define('WPCF7LE_DIR', realpath(dirname(__FILE__)));
$wpcf7le_upload_dir = wp_upload_dir();
// Define uploads directory and path URL depending on multi or single site installation
if ( is_multisite() ) {
  define('WPCF7LE_UPLOAD_FILE', esc_url( $wpcf7le_upload_dir['basedir'].'/sites/'.get_current_blog_id().'/Contact-Form-Submission.xml' ));
  define('WPCF7LE_DOWNLOAD_FILE', esc_url( $wpcf7le_upload_dir['baseurl'].'/sites/'.get_current_blog_id().'/Contact-Form-Submission.xml' ));
} else {
  define('WPCF7LE_UPLOAD_FILE', esc_url( $wpcf7le_upload_dir['basedir'].'/Contact-Form-Submission.xml' ));
  define('WPCF7LE_DOWNLOAD_FILE', esc_url( $wpcf7le_upload_dir['baseurl'].'/Contact-Form-Submission.xml' ));
}

if ( is_admin() ) {
  // If user is logged into WP admin, include admin file and functions
  include_once WPCF7LE_DIR . '/admin/lead-gen-admin.php';
} else {
  // Else include front end file and functions
  include_once WPCF7LE_DIR . '/lead-gen-front.php';
}

// Add 'Download XML' link to all plugins page
add_filter('plugin_action_links', 'wpcf7le_plugin_action_links', 10, 2);

function wpcf7le_plugin_action_links($wpcf7le_links, $wpcf7le_file) {
    static $wpcf7le_plugin;

    if (!$wpcf7le_plugin) {
        $wpcf7le_plugin = plugin_basename(__FILE__);
    }
    if ($wpcf7le_file == $wpcf7le_plugin) {
        $wpcf7le_settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=lead-gen-admin">Download XML</a>';
        array_unshift($wpcf7le_links, $wpcf7le_settings_link);
    }
    return $wpcf7le_links;
}

// Upon plugin activation register plugin uninstallation file
function wpcf7le_activate() {
    register_uninstall_hook( 'uninstall.php', 'wpcf7le_uninstall' );
}
register_activation_hook( __FILE__, 'wpcf7le_activate' );
