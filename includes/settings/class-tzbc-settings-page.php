<?php
/***
 * TZBA Settings Page Class
 *
 * Adds a new tab on the themezee addons page and displays the settings page.
 *
 * @package ThemeZee Boilerplate Addon
 */
 

// Use class to avoid namespace collisions
if ( ! class_exists('TZBA_Settings_Page') ) :

class TZBA_Settings_Page {

	/**
	 * Setup the Settings Page class
	 *
	 * @return void
	*/
	static function setup() {
		
		// Add settings page to addon tabs
		add_filter( 'themezee_addons_settings_tabs', array( __CLASS__, 'add_settings_page' ) );
		
		// Hook settings page to addon page
		add_action( 'themezee_addons_page_boilerplate', array( __CLASS__, 'display_settings_page' ) );
		
		// Enqueue Admin Page Styles
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Add settings page to tabs list on themezee add-on page
	 *
	 * @return void
	*/
	static function add_settings_page($tabs) {
			
		// Add Boilerplate Settings Page to Tabs List
		$tabs['boilerplate']      = __( 'Boilerplate', 'themezee-boilerplate-addon' );
		
		return $tabs;
		
	}
	
	/**
	 * Display settings page
	 *
	 * @return void
	*/
	static function display_settings_page() { 
	
		ob_start();
	?>
		
		<div id="tzba-settings" class="tzba-settings-wrap">
			
			<h2><?php _e( 'ThemeZee Boilerplate Addon', 'themezee-boilerplate-addon' ); ?></h2>
			<?php settings_errors(); ?>
			
			<form class="tzba-settings-form" method="post" action="options.php">
				<?php
					settings_fields('tzba_settings');
					do_settings_sections('tzba_settings');
					submit_button();
				?>
			</form>
			
		</div>
<?php
		echo ob_get_clean();
	}
	
	/**
	 * Enqueue file upload js on settings page
	 *
	 * @return void
	*/
	static function enqueue_admin_scripts( $hook ) {

		// Embed stylesheet only on admin settings page
		if( 'appearance_page_themezee-add-ons' != $hook )
			return;
				
		// Enqueue Admin CSS
		wp_enqueue_script( 'tzwb-settings-file-upload', TZBA_PLUGIN_URL . '/assets/js/upload-setting.js', array(), TZBA_VERSION );
		
	}
	
}

// Run TZBA Settings Page Class
TZBA_Settings_Page::setup();

endif;