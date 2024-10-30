<?php
/**  FRONT END FOR CONTACT FORMS LEAD EXPORT
 * Returns all contact form 7 input values and inserts into XML file for download from WP admin
 *    - XML file generated exists in wp-content/uploads Directory
 *    - Contact Form 7 form must have a form title to be processed
 *    - Only stores form submissions from date/time this plugin is activated
 *
 * @see https://positionabsolute.de/blog/creating-an-xml-file-from-a-contactform7-submission
 * @see https://developer.wordpress.org/plugins/wordpress-org/
 */
add_action('wpcf7_before_send_mail', 'wpcf7le_get_form_values');

function wpcf7le_get_form_values( $wpcf7le_contact_form ) {
    // get info about the form and current submission instance
    $wpcf7le_form_title = $wpcf7le_contact_form->title();
    $wpcf7le_submission = WPCF7_Submission::get_instance();
    // Checks if there's a form on the page by checking for the form title
    // and if the submission is valid
    if ( $wpcf7le_form_title !== null && $wpcf7le_submission ) {
      // get the actual data!
      $wpcf7le_posted_data = $wpcf7le_submission->get_posted_data();
      $wpcf7le_s_posted_data = $wpcf7le_posted_data;
			wpcf7le_generate_xml( $wpcf7le_posted_data, $wpcf7le_s_posted_data );
    }
}

function wpcf7le_generate_xml( $wpcf7le_posted_data, $wpcf7le_s_posted_data ) {
    // Create XML Leads file
    $wpcf7le_xml_doc = new DOMDocument('1.0', 'UTF-8');
    $wpcf7le_xml_doc->formatOutput = true;

		//Check if XML file is aclready created. If so, append to current file.
		if ( file_exists(WPCF7LE_UPLOAD_FILE) ) {
			// Load already created XML file
    	$wpcf7le_xml = file_get_contents( WPCF7LE_UPLOAD_FILE );
			$wpcf7le_xml_doc->loadXML( $wpcf7le_xml, LIBXML_NOBLANKS );
			// find the Contact-Form-Submission tag
    	$wpcf7le_xml_root = $wpcf7le_xml_doc->getElementsByTagName('leads')->item(0);
    	// Create node for current user
    	$wpcf7le_xml_user = $wpcf7le_xml_doc->createElement('User');
      // add the user tag before the first element in the 'leads' tag
			$wpcf7le_xml_root->insertBefore( $wpcf7le_xml_user, $wpcf7le_xml_root->firstChild );
    } else {
      // If XML file doesn't exist, create it.
    	// Create & add root Node
    	$wpcf7le_xml_root = $wpcf7le_xml_doc->createElement('Contact-Form-Submission');
    	$wpcf7le_xml_doc->appendChild($wpcf7le_xml_root);

			// Create & add container node 'leads'
    	$wpcf7le_xml_container = $wpcf7le_xml_doc->createElement('leads');
    	$wpcf7le_xml_root->appendChild($wpcf7le_xml_container);

    	// Create node for current user
    	$wpcf7le_xml_user = $wpcf7le_xml_doc->createElement('User');
      //Add node for current user to root node
      $wpcf7le_xml_container->appendChild($wpcf7le_xml_user);
    }

    if ( !empty($wpcf7le_s_posted_data) ) {
        foreach ( $wpcf7le_s_posted_data as $wpcf7le_name => $wpcf7le_value ) {
            if ('_wpcf7' !== substr($wpcf7le_name, 0, 6)) {
                // skip empty arrays
                if( is_array($wpcf7le_value) && !array_filter($wpcf7le_value) ){
                    continue;
                }

                $fields[$wpcf7le_name] = $wpcf7le_value;

                // check if field is a checkbox which returns as an array containing each checkbox value and print nodes for each value
                if( is_array($wpcf7le_value) ) {
                  foreach ( $wpcf7le_value as $wpcf7le_name_option => $wpcf7le_value_option ) {
                    $fields[$wpcf7le_name_option] = $wpcf7le_value_option;
                    $wpcf7le_xml_cn = $wpcf7le_xml_doc->createElement( strval($wpcf7le_name), strval($wpcf7le_value_option) );
                    $wpcf7le_xml_user->appendChild( $wpcf7le_xml_cn );
                  }
                } else { // if not a checkbox simply print single node for value
                  $wpcf7le_xml_cn = $wpcf7le_xml_doc->createElement( strval($wpcf7le_name), strval($wpcf7le_value) );
                	$wpcf7le_xml_user->appendChild( $wpcf7le_xml_cn );
                }

            }
          }
      }
    	// save XML file to uploads directory for further processing
    	//$content = chunk_split( base64_encode($wpcf7le_xml_doc->saveXML()) );
    	$wpcf7le_xml_doc->save( WPCF7LE_UPLOAD_FILE );
}
?>
