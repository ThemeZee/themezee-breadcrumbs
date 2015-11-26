<?php
/***
 * Breadcrumbs Setup
 *
 * This file adds a basic template function and shortcode to display the Breadcrumb Trail
 * Also hooks into ThemeZee WordPress themes to display breadcrumbs if automatic display is activated
 *
 * @package ThemeZee Breadcrumbs
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Shows a breadcrumb for all types of pages.  This is a wrapper function for the TZBC_Breadcrumb_Trail class,
 * which should be used in theme templates.
 *
 * @access public
 * @param  array $args Arguments to pass to TZBC_Breadcrumb_Trail.
 * @return void
 */
function themezee_breadcrumbs( $args = array() ) {

	$breadcrumb = new TZBC_Breadcrumb_Trail( $args );

	return $breadcrumb->trail();
}