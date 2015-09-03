<?php
/*
Plugin Name: ThemeZee Boilerplate Addon
Plugin URI: http://themezee.com/addons/widget-bundle/
Description: ADD Addon Plugin Description here
Author: ThemeZee
Author URI: http://themezee.com/
Version: 1.0
Text Domain: themezee-boilerplate-addon
Domain Path: /languages/
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Lean Custom Post Types Plugin
Copyright(C) 2014, ThemeZee.com - contact@themezee.com

*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Use class to avoid namespace collisions
if ( ! class_exists('ThemeZee_Boilerplate_Addon') ) :

/**
 * Main ThemeZee_Boilerplate_Addon Class
 *
 * @since 1.0
 */
class ThemeZee_Boilerplate_Addon {

	/**
	 * ThemeZee Boilerplate Addon Setup
	 *
	 * Calls all Functions to setup the Plugin
	 *
	 * @since 1.0
	 * @static
	 * @uses ThemeZee_Boilerplate_Addon::constants() Setup the constants needed
	 * @uses ThemeZee_Boilerplate_Addon::includes() Include the required files
	 * @uses ThemeZee_Boilerplate_Addon::setup_actions() Setup the hooks and actions
	 * @uses ThemeZee_Boilerplate_Addon::updater() Setup the plugin updater
	 */
	static function setup() {
	
		// Setup Constants
		self::constants();
		
		// Include Files
		self::includes();
		
		// Setup Action Hooks
		self::setup_actions();
		
		// Load Translation File
		load_plugin_textdomain( 'themezee-boilerplate-addon', false, dirname(plugin_basename(__FILE__)) );
		
	}
	
	
	/**
	 * Setup plugin constants
	 *
	 * @since 1.0
	 * @return void
	 */
	static function constants() {
		
		// Define Plugin Name
		define( 'TZBA_NAME', 'ThemeZee Boilerplate Addon');

		// Define Version Number
		define( 'TZBA_VERSION', '1.0' );
		
		// Define Plugin Name
		define( 'TZBA_PRODUCT_ID', 0);

		// Define Update API URL
		define( 'TZBA_STORE_API_URL', 'https://themezee.com' ); 

		// Plugin Folder Path
		define( 'TZBA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin Folder URL
		define( 'TZBA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Root File
		define( 'TZBA_PLUGIN_FILE', __FILE__ );
		
	}
	
	/**
	 * Include required files
	 *
	 * @since 1.0
	 * @return void
	 */
	static function includes() {

		// Include Admin Classes
		require_once TZBA_PLUGIN_DIR . '/includes/class-themezee-addons-page.php';
		require_once TZBA_PLUGIN_DIR . '/includes/class-tzba-plugin-updater.php';
		
		// Include Settings Classes
		require_once TZBA_PLUGIN_DIR . '/includes/settings/class-tzba-settings.php';
		require_once TZBA_PLUGIN_DIR . '/includes/settings/class-tzba-settings-page.php';
		
	}
	
	
	/**
	 * Setup Action Hooks
	 *
	 * @since 1.0
	 * @return void
	 */
	static function setup_actions() {

		// Enqueue Frontend Widget Styles
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
		
		// Add Widget Bundle Box to Add-on Overview Page
		add_action('themezee_addons_overview_page', array( __CLASS__, 'addon_overview_page' ) );
		
		// Add automatic plugin updater from ThemeZee Store API
		add_action( 'admin_init', array( __CLASS__, 'plugin_updater' ), 0 );
		
	}
	
	/* Enqueue Widget Styles */
	static function enqueue_styles() {
	
		// Enqueue BCW Plugin Stylesheet
		wp_enqueue_style('themezee-boilerplate-addon', self::get_stylesheet() );
		
	}
	
	/* Get Stylesheet URL */
	static function get_stylesheet() {
		
		if ( file_exists( get_stylesheet_directory() . '/css/themezee-boilerplate-addon.css' ) )
			$stylesheet = get_stylesheet_directory() . '/css/themezee-boilerplate-addon.css';
		elseif ( file_exists( get_template_directory() . '/css/themezee-boilerplate-addon.css' ) )
			$stylesheet = get_template_directory() . '/css/themezee-boilerplate-addon.css';
		else 
			$stylesheet = TZBA_PLUGIN_URL . '/assets/css/themezee-boilerplate-addon.css';
		
		return $stylesheet;
	}
	
	static function addon_overview_page() { 
	
		$plugin_data = get_plugin_data( __FILE__ );
		
		?>

		<dl><dt><h4><?php echo esc_html( $plugin_data['Name'] ); ?> <?php echo esc_html( $plugin_data['Version'] ); ?></h4></dt>
			<dd>
				<p>
					<?php echo wp_kses_post( $plugin_data['Description'] ); ?><br/>
				</p>
				<p>
					<a href="<?php echo admin_url( 'admin.php?page=themezee-add-ons&tab=boilerplate' ); ?>" class="button button-primary"><?php _e('Plugin Settings', 'themezee-boilerplate-addon'); ?></a> 
					<a href="<?php echo admin_url( 'plugins.php?s=ThemeZee+Boilerplate+Addon' ); ?>" class="button button-secondary"><?php _e('Deactivate', 'themezee-boilerplate-addon'); ?></a>
				</p>
				
			</dd>
		</dl>
		
		<?php
	}
	
	
	/**
	 * Plugin Updater
	 *
	 * @return void
	 */
	static function plugin_updater() {

		if( ! is_admin() ) :
			return;
		endif;
		
		$options = TZBA_Settings::instance();

		if( $options->get('license_key') <> '') :
			
			$license_key = $options->get('license_key');
			
			// setup the updater
			$tzba_updater = new TZBA_Plugin_Updater( TZBA_STORE_API_URL, __FILE__, array(
					'version' 	=> TZBA_VERSION,
					'license' 	=> $license_key,
					'item_name' => TZBA_NAME,
					'item_id'   => TZBA_PRODUCT_ID,
					'author' 	=> 'ThemeZee'
				)
			);
			
		endif;
		
	}
	
}

/* Run Plugin */
ThemeZee_Boilerplate_Addon::setup();

endif;