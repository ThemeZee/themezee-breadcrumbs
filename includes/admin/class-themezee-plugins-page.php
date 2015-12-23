<?php
/***
 * ThemeZee Plugins page
 *
 * Registers and displays the ThemeZee Plugins Page
 *
 * @package ThemeZee Breadcrumbs
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Use class to avoid namespace collisions
if ( ! class_exists( 'ThemeZee_Plugins_Page' ) ) :

class ThemeZee_Plugins_Page {

	/**
	 * Setup the ThemeZee Plugins Settings class
	 *
	 * @return void
	*/
	static function setup() {
		
		/* Add overview page to admin menu */
		add_action( 'admin_menu', array( __CLASS__, 'add_plugins_page' ), 8 );

		/* Enqueue Admin Page Styles */
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );

	}
	

	/**
	 * Add Settings Page to Admin menu
	 *
	 * @return void
	*/
	static function add_plugins_page() {
			
		add_options_page(
			esc_html__( 'ThemeZee Plugins', 'themezee-breadcrumbs' ),
			esc_html__( 'ThemeZee Plugins', 'themezee-breadcrumbs' ),
			'manage_options',
			'themezee-plugins',
			array( __CLASS__, 'display_plugins_page' )
		);
		
	}
	
	
	/**
	 * Displays Plugins Settings Page
	 *
	 * @return void
	*/
	static function display_plugins_page() { 
	
		$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], ThemeZee_Plugins_Page::get_settings_tabs() ) ? $_GET[ 'tab' ] : 'overview';
		?>
		
		<div id="themezee-plugins-wrap" class="wrap">
			
			<h2 class="nav-tab-wrapper">
				<?php // Display Tabs
				foreach( ThemeZee_Plugins_Page::get_settings_tabs() as $tab_id => $tab_name ) {

					$tab_url = add_query_arg( array(
						'settings-updated' => false,
						'tab' => $tab_id
					) );

					$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

					echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
						echo esc_html( $tab_name );
					echo '</a>';
				}
				?>
			</h2>
			
			<div id="themezee-plugins-tab-<?php echo $active_tab; ?>" class="themezee-plugins-tab-content">

				<?php // Display Tab Content
				if ( 'overview' == $active_tab ) :
					
					ThemeZee_Plugins_Page::display_overview_page();
				
				else :
				
					do_action('themezee_plugins_page_' . $active_tab );
					
				endif;
				
				?>
				
			</div>

		</div>
		
	<?php	
	}
	
	
	/**
	 * Displays Plugins Overview Page
	 *
	 * @return void
	*/
	static function display_overview_page() { 
	
		$plugin_link = '<a target="_blank" href="http://themezee.com/plugins/" title="'. esc_html__( 'ThemeZee Plugins', 'themezee-breadcrumbs' ) . '">'. esc_html__( 'plugins', 'themezee-breadcrumbs' ) . '</a>';
		?>
		
		<div id="themezee-plugins-overview">
		
			<h1 id="themezee-plugin-header"><?php esc_html_e( 'ThemeZee Plugins', 'themezee-breadcrumbs' ); ?></h1>
			<div class="themezee-plugins-intro">
				<?php printf( esc_html__( 'You need more features and functionality? Extend your website with our customized %s.', 'themezee-breadcrumbs' ), $plugin_link ); ?>
			</div>
			<hr/>

			<div id="themezee-plugins-list" class="themezee-plugins-clearfix">
			
				<?php do_action('themezee_plugins_overview_page'); ?>
				
			</div>
			
		</div>
	<?php	
	}
	
	
	/**
	 * Retrieve settings tabs
	 *
	 * @return array $tabs
	 */
	static function get_settings_tabs() {

		$tabs                 = array();
		$tabs['overview']      = esc_html__( 'Overview', 'themezee-breadcrumbs' );
		
		return apply_filters( 'themezee_plugins_settings_tabs', $tabs );
	}

	
	/**
	 * Enqueue Admin Styles
	 *
	 * @return void
	*/
	static function enqueue_admin_styles( $hook ) {

		// Embed stylesheet only on admin settings page
		if( 'appearance_page_themezee-plugins' != $hook )
			return;
				
		// Enqueue Admin CSS
		wp_enqueue_style( 'themezee-plugins-stylesheet', TZBC_PLUGIN_URL . 'assets/css/themezee-plugins.css', array(), TZBC_VERSION );
		
	}
	
}

// Run Class
ThemeZee_Plugins_Page::setup();

endif;