<?php
/**
 * TZBC Settings Class
 *
 * Registers all plugin settings with the WordPress Settings API.
 * Handles license key activation with the ThemeZee Store API.
 *
 * @link https://codex.wordpress.org/Settings_API
 * @package ThemeZee Breadcrumbs
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * TZBC Settings Class
 */
class TZBC_Settings {
	/** Singleton *************************************************************/

	/**
	 * @var instance The one true TZBC_Settings instance
	 */
	private static $instance;

	/**
	 * @var options Plugin options array
	 */
	private $options;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return TZBC_Settings A single instance of this class.
	 */
	public static function instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Plugin Setup
	 *
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );
		add_action( 'admin_init', array( $this, 'check_license' ) );

		// Merge Plugin Options Array from Database with Default Settings Array.
		$this->options = wp_parse_args( get_option( 'tzbc_settings' , array() ), $this->default_settings() );
	}

	/**
	 * Get the value of a specific setting
	 *
	 * @param String $key     Settings key.
	 * @param String $default Default value.
	 * @return mixed
	 */
	public function get( $key, $default = false ) {
		$value = ! empty( $this->options[ $key ] ) ? $this->options[ $key ] : $default;
		return $value;
	}

	/**
	 * Get all settings
	 *
	 * @return array
	 */
	public function get_all() {
		return $this->options;
	}

	/**
	 * Retrieve default settings
	 *
	 * @return array
	 */
	public function default_settings() {

		$default_settings = array();

		foreach ( $this->get_registered_settings() as $key => $option ) :

			if ( 'multicheck' === $option['type'] ) :

				foreach ( $option['options'] as $index => $value ) :

					$default_settings[ $key ][ $index ] = isset( $option['default'] ) ? $option['default'] : false;

				endforeach;

			else :

				$default_settings[ $key ] = isset( $option['default'] ) ? $option['default'] : false;

			endif;

		endforeach;

		return $default_settings;
	}

	/**
	 * Register all settings sections and fields
	 *
	 * @return void
	 */
	function register_settings() {

		// Make sure that options exist in database.
		if ( false === get_option( 'tzbc_settings' ) ) {
			add_option( 'tzbc_settings' );
		}

		// Add Sections.
		add_settings_section( 'tzbc_settings_general', esc_html__( 'General', 'themezee-breadcrumbs' ), '__return_false', 'tzbc_settings' );
		add_settings_section( 'tzbc_settings_license', esc_html__( 'License', 'themezee-breadcrumbs' ), array( $this, 'license_section_intro' ), 'tzbc_settings' );

		// Add Settings.
		foreach ( $this->get_registered_settings() as $key => $option ) :

			$name = isset( $option['name'] ) ? $option['name'] : '';
			$section = isset( $option['section'] ) ? $option['section'] : 'widgets';

			add_settings_field(
				'tzbc_settings[' . $key . ']',
				$name,
				is_callable( array( $this, $option['type'] . '_callback' ) ) ? array( $this, $option['type'] . '_callback' ) : array( $this, 'missing_callback' ),
				'tzbc_settings',
				'tzbc_settings_' . $section,
				array(
					'id'      => $key,
					'name'    => isset( $option['name'] ) ? $option['name'] : null,
					'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
					'size'    => isset( $option['size'] ) ? $option['size'] : null,
					'max'     => isset( $option['max'] ) ? $option['max'] : null,
					'min'     => isset( $option['min'] ) ? $option['min'] : null,
					'step'    => isset( $option['step'] ) ? $option['step'] : null,
					'options' => isset( $option['options'] ) ? $option['options'] : '',
					'default'     => isset( $option['default'] ) ? $option['default'] : '',
				)
			);

		endforeach;

		// Creates our settings in the options table.
		register_setting( 'tzbc_settings', 'tzbc_settings', array( $this, 'sanitize_settings' ) );
	}

	/**
	 * License Section Intro
	 *
	 * @return void
	 */
	function license_section_intro() {
		printf( __( 'Please activate your license in order to receive automatic plugin updates and <a href="%s" target="_blank">support</a>.', 'themezee-breadcrumbs' ), 'https://themezee.com/support/?utm_source=plugin-settings&utm_medium=textlink&utm_campaign=related-posts&utm_content=support' );
	}

	/**
	 * Sanitize the Plugin Settings
	 *
	 * @param array $input User Input.
	 * @return array
	 */
	function sanitize_settings( $input = array() ) {

		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		$saved = get_option( 'tzbc_settings', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		$settings = $this->get_registered_settings();
		$input = $input ? $input : array();

		// Loop through each setting being saved and pass it through a sanitization filter.
		foreach ( $input as $key => $value ) :

			// Get the setting type (checkbox, select, etc).
			$type = isset( $settings[ $key ]['type'] ) ? $settings[ $key ]['type'] : false;

			// Sanitize user input based on setting type.
			if ( 'text' === $type or 'license' === $type ) :

				$input[ $key ] = sanitize_text_field( $value );

			elseif ( 'radio' === $type or 'select' === $type ) :

				$available_options = array_keys( $settings[ $key ]['options'] );
				$input[ $key ] = in_array( $value, $available_options, true ) ? $value : $settings[ $key ]['default'];

			elseif ( 'number' === $type ) :

				$input[ $key ] = floatval( $value );

			elseif ( 'textarea' === $type ) :

				$input[ $key ] = esc_html( $value );

			elseif ( 'textarea_html' === $type ) :

				if ( current_user_can( 'unfiltered_html' ) ) :
					$input[ $key ] = $value;
				else :
					$input[ $key ] = wp_kses_post( $value );
				endif;

			elseif ( 'checkbox' === $type or 'multicheck' === $type ) :

				$input[ $key ] = $value; // Validate Checkboxes later.

			else :

				// Default Sanitization.
				$input[ $key ] = esc_html( $value );

			endif;

		endforeach;

		// Ensure a value is always passed for every checkbox.
		if ( ! empty( $settings ) ) :
			foreach ( $settings as $key => $setting ) :

				// Single checkbox.
				if ( isset( $settings[ $key ]['type'] ) && 'checkbox' == $settings[ $key ]['type'] ) :
					$input[ $key ] = ! empty( $input[ $key ] );
				endif;

				// Multicheck list.
				if ( isset( $settings[ $key ]['type'] ) && 'multicheck' == $settings[ $key ]['type'] ) :
					foreach ( $settings[ $key ]['options'] as $index => $value ) :
						$input[ $key ][ $index ] = ! empty( $input[ $key ][ $index ] );
					endforeach;
				endif;

			endforeach;
		endif;

		return array_merge( $saved, $input );
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @return array
	 */
	function get_registered_settings() {

		$settings = array(
			'browse_text' => array(
				'name' => esc_html__( 'Browse Text', 'themezee-breadcrumbs' ),
				'desc' => esc_html__( 'Enter the text which is displayed before the breadcrumb list. ', 'themezee-breadcrumbs' ),
				'section' => 'general',
				'type' => 'text',
				'size' => 'regular',
				'default' => esc_html__( 'You are here: ', 'themezee-breadcrumbs' ),
			),
			'separator' => array(
				'name' => esc_html__( 'Link Separator', 'themezee-breadcrumbs' ),
				'desc' => esc_html__( 'Select the separator of the breadcrumb items.', 'themezee-breadcrumbs' ),
				'section' => 'general',
				'type' => 'select',
				'options' => array(
					'slash' => '/',
					'dash' => '&ndash;',
					'bull' => '&bull;',
					'arrow-bracket' => '&gt;',
					'raquo' => '&raquo;',
					'single-arrow' => '&rarr;',
					'double-arrow' => '&rArr;',
				),
				'default' => 'raquo',
			),
			'front_page' => array(
				'name' => esc_html__( 'Front Page', 'themezee-breadcrumbs' ),
				'desc' => esc_html__( 'Display breadcrumb list on front page.', 'themezee-breadcrumbs' ),
				'section' => 'general',
				'type' => 'checkbox',
				'default' => false,
			),
			'activate_license' => array(
				'name' => esc_html__( 'Activate License', 'themezee-breadcrumbs' ),
				'section' => 'license',
				'type' => 'license',
				'default' => '',
			),
		);

		return apply_filters( 'tzbc_settings', $settings );
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @global $this->options Array of all the ThemeZee Breadcrumbs Options
	 * @return void
	 */
	function checkbox_callback( $args ) {

		$checked = isset( $this->options[ $args['id'] ] ) ? checked( 1, $this->options[ $args['id'] ], false ) : '';
		$html = '<input type="checkbox" id="tzbc_settings[' . $args['id'] . ']" name="tzbc_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
		$html .= '<label for="tzbc_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @global $this->options Array of all the ThemeZee Breadcrumbs Options
	 * @return void
	 */
	function multicheck_callback( $args ) {

		if ( ! empty( $args['options'] ) ) :
			foreach ( $args['options'] as $key => $option ) {
				$checked = isset( $this->options[ $args['id'] ][ $key ] ) ? checked( 1, $this->options[ $args['id'] ][ $key ], false ) : '';
				echo '<input name="tzbc_settings[' . $args['id'] . '][' . $key . ']" id="tzbc_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="1" ' . $checked . '/>&nbsp;';
				echo '<label for="tzbc_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
			}
		endif;
		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @global $this->options Array of all the ThemeZee Breadcrumbs Options
	 * @return void
	 */
	function text_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['default'] ) ? $args['default'] : '';
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="tzbc_settings[' . $args['id'] . ']" name="tzbc_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @global $this->options Array of all the ThemeZee Breadcrumbs Options
	 * @return void
	 */
	function radio_callback( $args ) {

		if ( ! empty( $args['options'] ) ) :
			foreach ( $args['options'] as $key => $option ) :
				$checked = false;

				if ( isset( $this->options[ $args['id'] ] ) && $this->options[ $args['id'] ] == $key ) {
					$checked = true;
				} elseif ( isset( $args['default'] ) && $args['default'] == $key && ! isset( $this->options[ $args['id'] ] ) ) {
					$checked = true;
				}

				echo '<input name="tzbc_settings[' . $args['id'] . ']"" id="tzbc_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>&nbsp;';
				echo '<label for="tzbc_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';

			endforeach;
		endif;
		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	/**
	 * License Callback
	 *
	 * Renders license key fields.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @global $this->options Array of all the ThemeZee Breadcrumbs Options
	 * @return void
	 */
	function license_callback( $args ) {

		$html = '';
		$license_status = $this->get( 'license_status' );
		$license_key = TZBC_LICENSE;

		if ( 'valid' === $license_status && ! empty( $license_key ) ) {
			$html .= '<input type="submit" class="button" name="tzbc_deactivate_license" value="' . esc_attr__( 'Deactivate License', 'themezee-breadcrumbs' ) . '"/>';
			$html .= '<span style="display: inline-block; padding: 5px; color: green;">&nbsp;' . esc_html__( 'Your license is valid!', 'themezee-breadcrumbs' ) . '</span>';
		} elseif ( 'expired' === $license_status && ! empty( $license_key ) ) {
			$renewal_url = esc_url( add_query_arg( array( 'edd_license_key' => $license_key, 'download_id' => TZBC_PRODUCT_ID ), 'https://themezee.com/checkout' ) );
			$html .= '<a href="' . esc_url( $renewal_url ) . '" class="button-primary">' . esc_html__( 'Renew Your License', 'themezee-breadcrumbs' ) . '</a>';
			$html .= '<br/><span style="display: inline-block; padding: 5px; color: red;">&nbsp;' . esc_html__( 'Your license has expired, renew today to continue getting updates and support!', 'themezee-breadcrumbs' ) . '</span>';
		} elseif ( 'invalid' === $license_status && ! empty( $license_key ) ) {
			$html .= '<input type="submit" class="button" name="tzbc_activate_license" value="' . esc_attr__( 'Activate License', 'themezee-breadcrumbs' ) . '"/>';
			$html .= '<span style="display: inline-block; padding: 5px; color: red;">&nbsp;' . esc_html__( 'Your license is invalid!', 'themezee-breadcrumbs' ) . '</span>';
		} else {
			$html .= '<input type="submit" class="button" name="tzbc_activate_license" value="' . esc_attr__( 'Activate License', 'themezee-breadcrumbs' ) . '"/>';
		}

		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @global $this->options Array of all the ThemeZee Breadcrumbs Options
	 * @return void
	 */
	function number_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['default'] ) ? $args['default'] : '';
		}

		$max  = isset( $args['max'] ) ? $args['max'] : 999999;
		$min  = isset( $args['min'] ) ? $args['min'] : 0;
		$step = isset( $args['step'] ) ? $args['step'] : 1;

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="tzbc_settings[' . $args['id'] . ']" name="tzbc_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @global $this->options Array of all the ThemeZee Breadcrumbs Options
	 * @return void
	 */
	function textarea_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['default'] ) ? $args['default'] : '';
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<textarea class="' . $size . '-text" cols="20" rows="5" id="tzbc_settings_' . $args['id'] . '" name="tzbc_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Textarea HTML Callback
	 *
	 * Renders textarea fields which allow HTML code.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @global $this->options Array of all the ThemeZee Breadcrumbs Options
	 * @return void
	 */
	function textarea_html_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['default'] ) ? $args['default'] : '';
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<textarea class="' . $size . '-text" cols="20" rows="5" id="tzbc_settings_' . $args['id'] . '" name="tzbc_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @return void
	 */
	function missing_callback( $args ) {
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'themezee-breadcrumbs' ), $args['id'] );
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @param array $args Arguments passed by the setting.
	 * @global $this->options Array of all the ThemeZee Breadcrumbs Options
	 * @return void
	 */
	function select_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) ) {
			$value = $this->options[ $args['id'] ];
		} else {
			$value = isset( $args['default'] ) ? $args['default'] : '';
		}

		$html = '<select id="tzbc_settings[' . $args['id'] . ']" name="tzbc_settings[' . $args['id'] . ']"/>';

		foreach ( $args['options'] as $option => $name ) :
			$selected = selected( $option, $value, false );
			$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
		endforeach;

		$html .= '</select>';
		$html .= '<p class="description">'  . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Activate license key
	 *
	 * @return void
	 */
	public function activate_license() {

		if ( ! isset( $_POST['tzbc_settings'] ) ) {
			return;
		}

		if ( ! isset( $_POST['tzbc_activate_license'] ) ) {
			return;
		}

		// Retrieve the license from the database.
		$status  = $this->get( 'license_status' );

		if ( 'valid' === $status ) {
			return; // License already activated and valid.
		}

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license' 	=> TZBC_LICENSE,
			'item_name' => urlencode( TZBC_NAME ),
			'item_id'   => TZBC_PRODUCT_ID,
			'url'       => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( TZBC_STORE_API_URL, array( 'timeout' => 35, 'sslverify' => true, 'body' => $api_params ) );

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// Decode the license data.
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		$options = $this->get_all();

		$options['license_status'] = $license_data->license;

		update_option( 'tzbc_settings', $options );

		delete_transient( 'tzbc_license_check' );
	}

	/**
	 * Deactivate license key
	 *
	 * @return void
	 */
	public function deactivate_license() {

		if ( ! isset( $_POST['tzbc_settings'] ) ) {
			return;
		}

		if ( ! isset( $_POST['tzbc_deactivate_license'] ) ) {
			return;
		}

		// Get Options.
		$options = $this->get_all();

		// Set License Status to false.
		$options['license_status'] = 0;

		// Update Option.
		update_option( 'tzbc_settings', $options );

		delete_transient( 'tzbc_license_check' );
	}

	/**
	 * Check license key
	 *
	 * @return void
	 */
	public function check_license() {

		if ( ! empty( $_POST['tzbc_settings'] ) ) {
			return; // Don't fire when saving settings.
		}

		$status = get_transient( 'tzbc_license_check' );

		// Run the license check a maximum of once per day.
		if ( false === $status ) {

			// Data to send in our API request.
			$api_params = array(
				'edd_action' => 'check_license',
				'license' 	=> TZBC_LICENSE,
				'item_name' => urlencode( TZBC_NAME ),
				'item_id'   => TZBC_PRODUCT_ID,
				'url'       => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post( TZBC_STORE_API_URL, array( 'timeout' => 25, 'sslverify' => true, 'body' => $api_params ) );

			// Make sure the response came back okay.
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// Update License Status.
			if ( 'valid' !== $license_data->license ) {

				$options = $this->get_all();

				$options['license_status'] = $license_data->license;
				update_option( 'tzbc_settings', $options );

				set_transient( 'tzbc_license_check', $license_data->license, DAY_IN_SECONDS );
			}

			$status = $license_data->license;
		}

		return $status;
	}
}

// Run Setting Class.
TZBC_Settings::instance();
