<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://cheshirewebsolutions.com/
 * @since      0.0.1
 *
 * @package    CWS_YouTube_Pro
 * @subpackage CWS_YouTube_Pro/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.0.1
 * @package    CWS_YouTube_Pro
 * @subpackage CWS_YouTube_Pro/includes
 * @author     Ian Kennerley <info@cheshirewebsolutions.com>
 */
class CWS_YouTube_Pro_i18n {

	/**
	 * The domain specified for this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $domain    The domain identifier for this plugin.
	 */
	private $domain;

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.0.1
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	/**
	 * Set the domain equal to that of the specified domain.
	 *
	 * @since    0.0.1
	 * @param    string    $domain    The domain that represents the locale of this plugin.
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}

}