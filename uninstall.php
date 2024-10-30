<?php
/**  UNINSTALL PLUGIN FUNCTIONS FOR CONTACT FORMS LEAD EXPORT
 * If lead XML file exists, delete file upon CF7LE plugin deletion
 *    - XML file generated exists in wp-content/uploads Directory
 *
 */
$wpcf7le_xml_file_dir = wp_upload_dir();
// Define location of XML file depending on multi or single site installation
if ( is_multisite() ) {
  define('WPCF7LE_XML_FILE', esc_url( $wpcf7le_xml_file_dir['basedir'].'/sites/'.get_current_blog_id().'/Contact-Form-Submission.xml') );
} else {
  define('WPCF7LE_XML_FILE', esc_url( $wpcf7le_xml_file_dir['basedir'].'/Contact-Form-Submission.xml') );
}

function wpcf7le_uninstall() {
  if( file_exists(WPCF7LE_XML_FILE) ) {
    wp_delete_file(WPCF7LE_XML_FILE);
  }
}

wpcf7le_uninstall();
?>
