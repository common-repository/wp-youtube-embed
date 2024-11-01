<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://cheshirewebsolutions.com/
 * @since      1.0.0
 *
 * @package    CWS_YouTube_Pro
 * @subpackage CWS_YouTube_Pro/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    CWS_YouTube_Pro
 * @subpackage CWS_YouTube_Pro/public
 * @author     Ian Kennerley <info@cheshirewebsolutions.com>
 */
class CWS_YouTube_Pro_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $isPro ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->isPro = $isPro;

	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CWS_Google_Picasa_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CWS_Google_Picasa_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cws-youtube-pro-public.css', array(), $this->version, 'all' );

		// Include FontAwesome
		wp_enqueue_style( 'cws-fa', 'https://use.fontawesome.com/releases/v5.4.2/css/all.css', $this->version, 'all' );

		// slider is a Pro Only fearure and would be better not to incude the files for Free version
		// might get missing resources	
        wp_enqueue_style( 'cws_ytp_slick_carousel_css', plugin_dir_url( __FILE__ ) . '../shortcodes/pro/lib/slick/slick.css' , array(), $this->version, 'all' );
        wp_enqueue_style( 'cws_ytp_slick_carousel_css_theme', plugin_dir_url( __FILE__ ) . '../shortcodes/pro/lib/slick/slick-theme.css', array(), $this->version, 'all' );
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CWS_Google_Picasa_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CWS_Google_Picasa_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// NEED TO ADD google apis shizzle here
        wp_enqueue_script( 'iframe_api', 'https://www.youtube.com/iframe_api', array('jquery'), null, true );
        wp_enqueue_script( 'my_app', plugin_dir_url( __FILE__ ) . 'js/cws-youtube-pro-public.js', array( 'jquery'), null, true );
 
		// had to add this here to stop error
	    wp_enqueue_script( 'cws_ytp_init1', plugin_dir_url( __FILE__ )  . '../shortcodes/js/init-channel.js', array('my_app'), false, 0 );
	    wp_enqueue_script( 'cws_ytp_init1', plugin_dir_url( __FILE__ )  . '../shortcodes/js/init-playlistitemsxxx.js', array(), false, 0 );
	    wp_enqueue_script( 'cws_ytp_init1', plugin_dir_url( __FILE__ )  . '../shortcodes/js/init-video.js', array(), false, 0 );

        wp_enqueue_script( 'client', 'https://apis.google.com/js/client.js?onload=onClientLoad', array('my_app'), null, true );
        wp_enqueue_script( 'cws-jquery-cookie', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js', array(), null, true );

		// wp_enqueue_script( 'cws-jquery-cookie', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js', array(), null, true );
	}

}
