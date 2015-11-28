<?php
/***
 * ThemeZee Add-ons page
 *
 * Registers and displays the ThemeZee Addons Page
 *
 * @package ThemeZee Breadcrumbs
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Use class to avoid namespace collisions
if ( ! class_exists( 'ThemeZee_Addons_Page' ) ) :

class ThemeZee_Addons_Page {

	/**
	 * Setup the ThemeZee Addons Settings class
	 *
	 * @return void
	*/
	static function setup() {
		
		/* Add overview page to admin menu */
		add_action( 'admin_menu', array( __CLASS__, 'add_addons_page' ), 8 );

		/* Enqueue Admin Page Styles */
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );

	}
	

	/**
	 * Add Settings Page to Admin menu
	 *
	 * @return void
	*/
	static function add_addons_page() {
			
		add_theme_page(
			esc_html__( 'ThemeZee Add-ons', 'themezee-breadcrumbs' ),
			esc_html__( 'Theme Add-ons', 'themezee-breadcrumbs' ),
			'manage_options',
			'themezee-addons',
			array( __CLASS__, 'display_addons_page' )
		);
		
	}
	
	
	/**
	 * Displays Addons Settings Page
	 *
	 * @return void
	*/
	static function display_addons_page() { 
	
		$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], ThemeZee_Addons_Page::get_settings_tabs() ) ? $_GET[ 'tab' ] : 'overview';
		?>
		
		<div id="themezee-addons-wrap" class="wrap">
			
			<h2 class="nav-tab-wrapper">
				<?php // Display Tabs
				foreach( ThemeZee_Addons_Page::get_settings_tabs() as $tab_id => $tab_name ) {

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
			
			<div id="themezee-addons-tab-<?php echo $active_tab; ?>" class="themezee-addons-tab-content">

				<?php // Display Tab Content
				if ( 'overview' == $active_tab ) :
					
					ThemeZee_Addons_Page::display_overview_page();
				
				else :
				
					do_action('themezee_addons_page_' . $active_tab );
					
				endif;
				
				?>
				
			</div>

		</div>
		
	<?php	
	}
	
	
	/**
	 * Displays Addons Overview Page
	 *
	 * @return void
	*/
	static function display_overview_page() { 
	
		$addon_link = '<a target="_blank" href="http://themezee.com/addons/" title="'. esc_html__( 'ThemeZee Add-ons', 'themezee-breadcrumbs' ) . '">'. esc_html__( 'add-ons', 'themezee-breadcrumbs' ) . '</a>';
		?>
		
		<div id="themezee-addons-overview">
		
			<h1 id="themezee-addon-header"><?php esc_html_e( 'ThemeZee Add-ons', 'themezee-breadcrumbs' ); ?></h1>
			<div class="themezee-addons-intro">
				<?php printf( esc_html__( 'You need more features and functionality? Extend your website with our customized %s.', 'themezee-breadcrumbs' ), $addon_link ); ?>
			</div>
			<hr/>

			<div id="themezee-addons-list" class="themezee-addons-clearfix">
			
				<?php do_action('themezee_addons_overview_page'); ?>
				
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
		
		return apply_filters( 'themezee_addons_settings_tabs', $tabs );
	}

	
	/**
	 * Enqueue Admin Styles
	 *
	 * @return void
	*/
	static function enqueue_admin_styles( $hook ) {

		// Embed stylesheet only on admin settings page
		if( 'appearance_page_themezee-addons' != $hook )
			return;
				
		// Enqueue Admin CSS
		wp_enqueue_style( 'themezee-addons-stylesheet', TZBC_PLUGIN_URL . 'assets/css/themezee-addons.css', array(), TZBC_VERSION );
		
	}
	
}

// Run Class
ThemeZee_Addons_Page::setup();

endif;