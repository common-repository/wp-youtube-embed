<?php
/**
 * Shortcodes init
 * 
 * Init main shortcodes
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

include_once('shortcode-channel.php');								// Displays channel
include_once('shortcode-playlistitems.php');						// Displays playlist
include_once('shortcode-video.php');							// Displays video
include_once('pro/shortcode-search.php');							// Displays videos found by search terms

/**
 * Shortcode creation
 **/
add_shortcode( 'cws_ytp_channel', 'cws_ytp_shortcode_channel' );
add_shortcode( 'cws_ytp_playlistitems', 'cws_ytp_shortcode_playlistitems' );
add_shortcode( 'cws_ytp_video', 'cws_ytp_shortcode_video' );
add_shortcode( 'cws_ytp_search', 'cws_ytp_shortcode_search' );