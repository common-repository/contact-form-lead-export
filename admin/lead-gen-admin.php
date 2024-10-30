<?php
/**  WP ADMIN FOR CONTACT FORMS LEAD EXPORT
 * Creates admin page with link to download Contact Form 7 submissions as XML file
 *    - XML file downloaded from wp-content/uploads Directory
 *    - Requires at least 1 form submission since installation of this plugin to activate download link
 *    - WP admin page located at wp-admin/admin.php?page=lead-gen-admin
 *
 *
 */
  // Register & load admin page CSS
  function wpcf7le_admin_styles() {
    wp_register_style( 'wpcf7le-style', plugins_url(WPCF7LE_PLUGIN_FILE_NAME.'/css/admin.css') );
    wp_enqueue_style( 'wpcf7le-style' );
  }
  add_action( 'admin_enqueue_scripts', 'wpcf7le_admin_styles' );

  // Load admin menus, template and link for plugin
  function wpcf7le_lead_generation_menu() {
		add_menu_page(
			__( 'Export to XML - Contact Form Lead Export', 'contact-form-lead-export' ),
			__( 'Contact Export', 'contact-form-lead-export' ),
			'manage_options',
			'lead-gen-admin',
			'wpcf7le_lead_gen_admin_contents',
			'dashicons-schedule',
			30
		);
	}
	add_action( 'admin_menu', 'wpcf7le_lead_generation_menu' );

  // Define content of admin page
  function wpcf7le_lead_gen_admin_contents() {
		?>
    <div class="wrap wpcf7le">
			<h1 class="wp-heading-inline">
				<?php esc_html_e( 'Download Contact Form Leads', 'contact-form-lead-export' ); ?>
			</h1>
      <div class="postbox">
        <p>Use the Download File button below to download all of your website's form submissions in XML format.</p>
        <?php
        //check if XML file has been created - if true create download link
        if( file_exists(WPCF7LE_UPLOAD_FILE) ) {
        ?>
          <a class="button-primary" download="Contact-Form-Submission.xml" href="<?php echo WPCF7LE_DOWNLOAD_FILE; ?>">Download File</a>
        <?php
        } else {
        //no form submissions since plugin install no download link to display
        ?>
          <h2>No New Leads Have Been Submitted Since Installation of this Plugin.</h2>
        <?php
        }
        ?>
      </div>
    </div>
		<?php
	}
