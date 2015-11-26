<?php
/***
 * TZBC Settings Page Class
 *
 * Adds a new tab on the themezee addons page and displays the settings page.
 *
 * @package ThemeZee Breadcrumbs
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Use class to avoid namespace collisions
if ( ! class_exists( 'TZBC_Settings_Page' ) ) :

class TZBC_Settings_Page {

	/**
	 * Setup the Settings Page class
	 *
	 * @return void
	*/
	static function setup() {
		
		// Add settings page to addon tabs
		add_filter( 'themezee_addons_settings_tabs', array( __CLASS__, 'add_settings_page' ) );
		
		// Hook settings page to addon page
		add_action( 'themezee_addons_page_breadcrumbs', array( __CLASS__, 'display_settings_page' ) );
	}

	/**
	 * Add settings page to tabs list on themezee add-on page
	 *
	 * @return void
	*/
	static function add_settings_page($tabs) {
			
		// Add Breadcrumbs Settings Page to Tabs List
		$tabs['breadcrumbs'] = esc_html__( 'Breadcrumbs', 'themezee-breadcrumbs' );
		
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
		
		<div id="tzbc-settings" class="tzbc-settings-wrap">
			
			<h1><?php esc_html_e( 'Breadcrumbs', 'themezee-breadcrumbs' ); ?></h1>
			<?php settings_errors(); ?>
			
			<form class="tzbc-settings-form" method="post" action="options.php">
				<?php
					settings_fields('tzbc_settings');
					do_settings_sections('tzbc_settings');
					submit_button();
				?>
			</form>
			
		</div>
<?php
		echo ob_get_clean();
	}
	
}

// Run TZBC Settings Page Class
TZBC_Settings_Page::setup();

endif;