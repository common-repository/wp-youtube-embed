<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    CWS_YouTube_Pro
 * @subpackage CWS_YouTube_Pro/includes
 * @author     Ian Kennerley <info@cheshirewebsolutions.com>
 */
class CWS_YouTube_Pro_Activator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if( !get_option('cws_ytp_defaults') ){

				// Defaults Settings 
				$cws_ytp_defaults = array(
											'maxResults' => '6',
											'show_social' => (bool) 1,
											'show_likes' => (bool) 1,
											'show_views' => (bool) 1,
											'show_date' => (bool) 1,
											'show_subscribe' =>  (bool) 1,
											'show_channel_header' => (bool) 1,
											'show_channel_description' =>  (bool) 1,
											'default_view' => 'Grid',
										); 

				update_option( 'cws_ytp_defaults', $cws_ytp_defaults );
		}

		if( !get_option('cws_ytp_slider') ) {

			// Slider Settings 
			$cws_ytp_slider = array(
										'slidesToShow' => (int) 3,
										'slidesToScroll' => (int) 3,
										'arrows' => (bool) 1,
										'dots' => (bool) 1,
										'autoplay' => (bool) 1,
										'infinite' => (bool) 1,
										'autoplaySpeed' => (int) 3000,
										'speed' => (int) 2000,
										'duration' => (bool) 1,
										'playIcon' => (bool) 1,
										'title' => (bool) 1,
								);

			update_option( 'cws_ytp_slider', $cws_ytp_slider );
		}


		// Delete dismiss upgrade notice
		$current_user = getYTPCurrentUser();
		delete_user_meta( $current_user->ID, 'cws_ytp_ignore_upgrade' );
	}
}