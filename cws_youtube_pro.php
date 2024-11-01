<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://cheshirewebsolutions.com/
 * @since             1.0.0
 * @package           CWS_YouTube_Pro
 *
 * @wordpress-plugin
 * Plugin Name:       WP YouTube Embed
 * Plugin URI:        http://wordpress-plugin-youtube.cheshirewebsolutions.com/
 * Description:       Add a YouTube Channel, Playlist or Video to your Website
 * Version:           1.0.0
 * Author:            Cheshire Web Solutions
 * Author URI:        http://cheshirewebsolutions.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cws_youtube_pro
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cws-google-picasa-pro-activator.php
 */
function activate_cws_youtube_pro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cws-youtube-pro-activator.php';
	CWS_YouTube_Pro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cws-google-picasa-pro-deactivator.php
 */
function deactivate_cws_youtube_pro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cws-youtube-pro-deactivator.php';
	CWS_YouTube_Pro_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cws_youtube_pro' );
register_deactivation_hook( __FILE__, 'deactivate_cws_youtube_pro' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cws-youtube-pro.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_cws_youtube_pro() {
    
    $plugin = new CWS_YouTube_Pro();
	$plugin->run();

}
run_cws_youtube_pro();