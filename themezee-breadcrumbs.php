<?php
/*
Plugin Name: ThemeZee Breadcrumbs
Plugin URI: https://themezee.com/plugins/breadcrumbs/
Description: This plugin allows you to add a nice and elegant breadcrumb navigation. Breadcrumbs make it easy for the user to navigate up and down the hierarchy of your website and are good for SEO.
Author: ThemeZee
Author URI: https://themezee.com/
Version: 1.0.5
Text Domain: themezee-breadcrumbs
Domain Path: /languages/
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

ThemeZee Breadcrumbs
Copyright(C) 2019, ThemeZee.com - support@themezee.com

*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main ThemeZee_Breadcrumbs Class
 *
 * @package ThemeZee Breadcrumbs
 */
class ThemeZee_Breadcrumbs {
	/**
	 * Call all Functions to setup the Plugin
	 *
	 * @uses ThemeZee_Breadcrumbs::constants() Setup the constants needed
	 * @uses ThemeZee_Breadcrumbs::includes() Include the required files
	 * @uses ThemeZee_Breadcrumbs::setup_actions() Setup the hooks and actions
	 * @return void
	 */
	static function setup() {

		// Setup Constants.
		self::constants();

		// Setup Translation.
		add_action( 'plugins_loaded', array( __CLASS__, 'translation' ) );

		// Include Files.
		self::includes();

		// Setup Action Hooks.
		self::setup_actions();
	}

	/**
	 * Setup plugin constants
	 *
	 * @return void
	 */
	static function constants() {

		// Define Plugin Name.
		define( 'TZBC_NAME', 'ThemeZee Breadcrumbs' );

		// Define Version Number.
		define( 'TZBC_VERSION', '1.0.5' );

		// Define Plugin Name.
		define( 'TZBC_PRODUCT_ID', 49729 );

		// Define Update API URL.
		define( 'TZBC_STORE_API_URL', 'https://themezee.com' );

		// Define Plugin Name.
		define( 'TZBC_LICENSE', 'd2830f6767515a780ebd6530ed48d4c2' );

		// Plugin Folder Path.
		define( 'TZBC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin Folder URL.
		define( 'TZBC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Root File.
		define( 'TZBC_PLUGIN_FILE', __FILE__ );
	}

	/**
	 * Load Translation File
	 *
	 * @return void
	 */
	static function translation() {
		load_plugin_textdomain( 'themezee-breadcrumbs', false, dirname( plugin_basename( TZBC_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Include required files
	 *
	 * @return void
	 */
	static function includes() {

		// Include Admin Classes.
		require_once TZBC_PLUGIN_DIR . '/includes/admin/class-themezee-plugins-page.php';
		require_once TZBC_PLUGIN_DIR . '/includes/admin/class-tzbc-plugin-updater.php';

		// Include Settings Classes.
		require_once TZBC_PLUGIN_DIR . '/includes/settings/class-tzbc-settings.php';
		require_once TZBC_PLUGIN_DIR . '/includes/settings/class-tzbc-settings-page.php';

		// Include Breadcrumb Files.
		require_once TZBC_PLUGIN_DIR . '/includes/class-tzbc-breadcrumb-trail.php';
		require_once TZBC_PLUGIN_DIR . '/includes/breadcrumbs-setup.php';
	}

	/**
	 * Setup Action Hooks
	 *
	 * @see https://codex.wordpress.org/Function_Reference/add_action WordPress Codex
	 * @return void
	 */
	static function setup_actions() {

		// Enqueue Frontend Widget Styles.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );

		// Add Settings link to Plugin actions.
		add_filter( 'plugin_action_links_' . plugin_basename( TZBC_PLUGIN_FILE ), array( __CLASS__, 'plugin_action_links' ) );

		// Add Breadcrumbs Plugin Box to Plugin Overview Page.
		add_action( 'themezee_plugins_overview_page', array( __CLASS__, 'plugin_overview_page' ) );

		// Add License Key admin notice.
		add_action( 'admin_notices', array( __CLASS__, 'license_key_admin_notice' ) );

		// Add automatic plugin updater from ThemeZee Store API.
		add_action( 'admin_init', array( __CLASS__, 'plugin_updater' ), 0 );
	}

	/**
	 * Enqueue Styles
	 *
	 * @return void
	 */
	static function enqueue_styles() {

		// Return early if theme handles styling.
		if ( current_theme_supports( 'themezee-breadcrumbs' ) ) :
			return;
		endif;

		// Enqueue Plugin Stylesheet.
		wp_enqueue_style( 'themezee-breadcrumbs', TZBC_PLUGIN_URL . 'assets/css/themezee-breadcrumbs.css', array(), TZBC_VERSION );
	}

	/**
	 * Add Settings link to the plugin actions
	 *
	 * @return array $actions Plugin action links
	 */
	static function plugin_action_links( $actions ) {

		$settings_link = array( 'settings' => sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=themezee-plugins&tab=breadcrumbs' ), __( 'Settings', 'themezee-breadcrumbs' ) ) );

		return array_merge( $settings_link, $actions );
	}

	/**
	 * Add widget bundle box to plugin overview admin page
	 *
	 * @return void
	 */
	static function plugin_overview_page() {

		$plugin_data = get_plugin_data( __FILE__ );
		?>

		<dl>
			<dt>
				<h4><?php echo esc_html( $plugin_data['Name'] ); ?></h4>
				<span><?php printf( esc_html__( 'Version %s', 'themezee-breadcrumbs' ), esc_html( $plugin_data['Version'] ) ); ?></span>
			</dt>
			<dd>
				<p><?php echo wp_kses_post( $plugin_data['Description'] ); ?><br/></p>
				<a href="<?php echo admin_url( 'options-general.php?page=themezee-plugins&tab=breadcrumbs' ); ?>" class="button button-primary"><?php esc_html_e( 'Plugin Settings', 'themezee-breadcrumbs' ); ?></a>&nbsp;
				<a href="<?php echo esc_url( 'https://themezee.com/docs/breadcrumbs-documentation/?utm_source=plugin-overview&utm_medium=button&utm_campaign=breadcrumbs&utm_content=documentation' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'View Documentation', 'themezee-breadcrumbs' ); ?></a>
			</dd>
		</dl>

		<?php
	}

	/**
	 * Add license key admin notice
	 *
	 * @return void
	 */
	static function license_key_admin_notice() {
		global $pagenow;

		// Display only on Plugins and Updates page.
		if ( ! ( 'plugins.php' == $pagenow or 'update-core.php' == $pagenow ) ) {
			return;
		}

		// Get Settings.
		$options = TZBC_Settings::instance();

		if ( 'valid' !== $options->get( 'license_status' ) ) :
			?>

			<div class="updated">
				<p>
					<?php
					printf( __( 'Please activate your license for the %1$s plugin in order to receive updates and support. <a href="%2$s">Activate License</a>', 'themezee-breadcrumbs' ),
						TZBC_NAME,
						admin_url( 'options-general.php?page=themezee-plugins&tab=breadcrumbs' )
					);
					?>
				</p>
			</div>

			<?php
		endif;
	}

	/**
	 * Plugin Updater
	 *
	 * @return void
	 */
	static function plugin_updater() {

		if ( ! is_admin() ) :
			return;
		endif;

		$options = TZBC_Settings::instance();

		if ( 'valid' === $options->get( 'license_status' ) ) :

			// Setup the updater.
			$tzbc_updater = new TZBC_Plugin_Updater( TZBC_STORE_API_URL, __FILE__, array(
				'version'   => TZBC_VERSION,
				'license'   => TZBC_LICENSE,
				'item_name' => TZBC_NAME,
				'item_id'   => TZBC_PRODUCT_ID,
				'author'    => 'ThemeZee',
			) );

		endif;
	}
}

// Run Plugin.
ThemeZee_Breadcrumbs::setup();
