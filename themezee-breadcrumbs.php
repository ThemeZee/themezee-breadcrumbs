<?php
/*
Plugin Name: ThemeZee Breadcrumbs
Plugin URI: http://themezee.com/add-ons/breadcrumbs/
Description: This plugin automatically detects your permalink setup and displays breadcrumbs based off that structure. Breadcrumb Trail recognizes your website hierarchy and builds a set of unique breadcrumbs for each page on your site.
Author: ThemeZee
Author URI: http://themezee.com/
Version: 1.0
Text Domain: themezee-breadcrumbs
Domain Path: /languages/
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Copyright(C) 2015, ThemeZee.com - support@themezee.com

*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Use class to avoid namespace collisions
if ( ! class_exists('ThemeZee_Breadcrumbs') ) :

/**
 * Main ThemeZee_Breadcrumbs Class
 *
 * @since 1.0
 */
class ThemeZee_Breadcrumbs {

	/**
	 * ThemeZee Breadcrumbs Setup
	 *
	 * Calls all Functions to setup the Plugin
	 *
	 * @since 1.0
	 * @static
	 * @uses ThemeZee_Breadcrumbs::constants() Setup the constants needed
	 * @uses ThemeZee_Breadcrumbs::includes() Include the required files
	 * @uses ThemeZee_Breadcrumbs::setup_actions() Setup the hooks and actions
	 * @uses ThemeZee_Breadcrumbs::updater() Setup the plugin updater
	 */
	static function setup() {
	
		// Setup Constants
		self::constants();
		
		// Include Files
		self::includes();
		
		// Setup Action Hooks
		self::setup_actions();
		
		// Load Translation File
		load_plugin_textdomain( 'themezee-breadcrumbs', false, dirname(plugin_basename(__FILE__)) );
		
	}
	
	
	/**
	 * Setup plugin constants
	 *
	 * @since 1.0
	 * @return void
	 */
	static function constants() {
		
		// Define Plugin Name
		define( 'TZBC_NAME', 'ThemeZee Breadcrumbs');

		// Define Version Number
		define( 'TZBC_VERSION', '1.0' );
		
		// Define Plugin Name
		define( 'TZBC_PRODUCT_ID', 0);

		// Define Update API URL
		define( 'TZBC_STORE_API_URL', 'https://themezee.com' ); 

		// Plugin Folder Path
		define( 'TZBC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin Folder URL
		define( 'TZBC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Root File
		define( 'TZBC_PLUGIN_FILE', __FILE__ );
		
	}
	
	/**
	 * Include required files
	 *
	 * @since 1.0
	 * @return void
	 */
	static function includes() {

		// Include Admin Classes
		require_once TZBC_PLUGIN_DIR . '/includes/class-themezee-addons-page.php';
		require_once TZBC_PLUGIN_DIR . '/includes/class-tzbc-plugin-updater.php';
		
		// Include Settings Classes
		require_once TZBC_PLUGIN_DIR . '/includes/settings/class-tzbc-settings.php';
		require_once TZBC_PLUGIN_DIR . '/includes/settings/class-tzbc-settings-page.php';
		
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
		wp_enqueue_style('themezee-breadcrumbs', self::get_stylesheet() );
		
	}
	
	/* Get Stylesheet URL */
	static function get_stylesheet() {
		
		if ( file_exists( get_stylesheet_directory() . '/css/themezee-breadcrumbs.css' ) )
			$stylesheet = get_stylesheet_directory() . '/css/themezee-breadcrumbs.css';
		elseif ( file_exists( get_template_directory() . '/css/themezee-breadcrumbs.css' ) )
			$stylesheet = get_template_directory() . '/css/themezee-breadcrumbs.css';
		else 
			$stylesheet = TZBC_PLUGIN_URL . '/assets/css/themezee-breadcrumbs.css';
		
		return $stylesheet;
	}
	
	static function addon_overview_page() { 
	
		$plugin_data = get_plugin_data( __FILE__ );
		
		?>

		<dl>
			<dt>
				<h4><?php echo esc_html( $plugin_data['Name'] ); ?></h4>
				<span><?php printf( __( 'Version %s', 'themezee-breadcrumbs'),  esc_html( $plugin_data['Version'] ) ); ?></span>
			</dt>
			<dd>
				<p><?php echo wp_kses_post( $plugin_data['Description'] ); ?><br/></p>
				<a href="<?php echo admin_url( 'admin.php?page=themezee-addons&tab=breadcrumbs' ); ?>" class="button button-primary"><?php _e('Plugin Settings', 'themezee-breadcrumbs'); ?></a> 
				<a href="<?php echo esc_url( 'http://themezee.com/docs/breadcrumbs/'); ?>" class="button button-secondary" target="_blank"><?php _e('View Documentation', 'themezee-breadcrumbs'); ?></a>
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
		
		$options = TZBC_Settings::instance();

		if( $options->get('license_key') <> '') :
			
			$license_key = $options->get('license_key');
			
			// setup the updater
			$tzbc_updater = new TZBC_Plugin_Updater( TZBC_STORE_API_URL, __FILE__, array(
					'version' 	=> TZBC_VERSION,
					'license' 	=> $license_key,
					'item_name' => TZBC_NAME,
					'item_id'   => TZBC_PRODUCT_ID,
					'author' 	=> 'ThemeZee'
				)
			);
			
		endif;
		
	}
	
}

/* Run Plugin */
ThemeZee_Breadcrumbs::setup();

endif;