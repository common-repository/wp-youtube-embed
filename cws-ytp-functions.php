<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPPicasa Admin Functions
 * 
 * Loads main functions used by admin menu and front-end.
 * 
 * Copyright (c) 2011, cheshirewebsolutions.com, Ian Kennerley (info@cheshirewebsolutions.com).
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/ 


/**
 *
 *  Allow redirection, even if my theme starts to send output to the browser
 *
 */	

add_action( 'init', 'cws_ytp_do_output_buffer' );
function cws_ytp_do_output_buffer() {
	ob_start();
}

// Retrieve and display the URL parameter
function cws_ytp_output_album_id() {
	global $wp_query;
	
	if( isset( $wp_query->query_vars['album_id'] ) ) {
		return $wp_query->query_vars['album_id'];
	}
}

function custom_ytp_query_vars_filter($vars) {
	//$vars[] = 'cws_page';
	//$vars[] .= 'cws_album';
	//$vars[] .= 'cws_album_title'; // pass album title to results pages, expander, grid, list
  $vars[] .= 'cws_debug'; // add for simple way to enable debugging via address bar
  return $vars;
}
///add_filter( 'query_vars', 'custom_query_vars_filter' );

function getYTPPM() {

  if ( ! did_action('wp_loaded') ) {
    $msg = 'Please call getCurrentUser after wp_loaded is fired.';
    return new WP_Error( 'to_early_for_user', $msg );
  }

  static $wp_pm = NULL;

  if ( is_null( $wp_pm ) ) {
    $wp_pm = new CWS_YTP_PM( new CWS_YTP_PM_User( get_current_user_id() ) );
  }

  return $wp_pm;
}

function getYTPCurrentUser() {

  $wppm = getYTPPM();

  if ( is_wp_error( $wppm ) ) return $wppm;

  $user = $wppm->getUser();

  if ( $user instanceof CWS_YTP_PM_User ) return $user;
}

add_action( 'wp_loaded', 'getYTPCurrentUser' );



function displayYTPUpgradeID() {

  $current_user = getYTPCurrentUser();
  if ( $current_user instanceof CWS_YTP_PM_User ) {
    $plugin = new CWS_YouTube_Pro();
    $plugin_admin = new CWS_YouTube_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );
    $plugin_admin->cws_ytp_admin_installed_notice($current_user);
    $plugin_admin->cws_ytp_ignore_upgrade($current_user);
  } else { //echo 'No one logged in'; 
  }
}

add_action( 'wp_loaded', 'displayYTPUpgradeID', 30 );


/**
 * Google client class Instantiate
 * @since      3.0.10
 * @return object
 */

function cws_ytp_google_class() {
    static $client;
    if ( !$client ) {
        $plugin_path = CWS_YTP_PATH . '/api-libs';
        set_include_path( $plugin_path . PATH_SEPARATOR . get_include_path());

        require_once CWS_YTP_PATH . '/api-libs/Google/Client.php';

        $client = new Google_Client();
    }

    return $client;
}