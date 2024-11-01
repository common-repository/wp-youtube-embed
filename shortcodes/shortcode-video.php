<?php
/**
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
function cws_ytp_shortcode_video( $atts ) {

    if( isset($_GET['cws_debug']) ){ $cws_debug = $_GET['cws_debug']; }

    $plugin = new CWS_YouTube_Pro();
    $plugin_admin = new CWS_YouTube_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );

    // var to hold markup
    $strOutput = "";

    // Grab API KEY options stored in db
    $cws_ytp_code   = get_option( 'cws_ytp_code' );
    $api_key        = $cws_ytp_code['api_key'];

    // Grab Defaults options stored in db
    $cws_ytp_defaults       = get_option( 'cws_ytp_defaults' );
    $arrSocial              = get_option( 'cws_ytp_social' );
    $cws_ytp_slider         = get_option( 'cws_ytp_slider' );
    $cws_ytp_slider_adv     = get_option( 'cws_ytp_slider_adv' );

    // Create Default vars with values stored in db
    $showSocial             = isset($cws_ytp_defaults['show_social']) ? $cws_ytp_defaults['show_social']: "";
    $showSubscribe          = isset($cws_ytp_defaults['show_subscribe']) ? $cws_ytp_defaults['show_subscribe']: "";
    $showChannelDescription = isset($cws_ytp_defaults['show_channel_description']) ? $cws_ytp_defaults['show_channel_description']: "";
    $showChannelHeader      = isset($cws_ytp_defaults['show_channel_header']) ? $cws_ytp_defaults['show_channel_header']: "";
    $showLikes              = isset($cws_ytp_defaults['show_likes']) ? $cws_ytp_defaults['show_likes']: "";
    $showViews              = isset($cws_ytp_defaults['show_views']) ? $cws_ytp_defaults['show_views']: "";
    $showDate               = isset($cws_ytp_defaults['show_date']) ? $cws_ytp_defaults['show_date']: "";
    $maxResults             = isset($cws_ytp_defaults['maxResults']) ? $cws_ytp_defaults['maxResults']: "";
    $strView                = strtolower( isset($cws_ytp_defaults['default_view']) ? $cws_ytp_defaults['default_view']: "");

    // Create Slider vars with values stored in db
    $slidesToShow   = isset($cws_ytp_slider['slidesToShow']) ? $cws_ytp_slider['slidesToShow']: "";
    $slidesToScroll = isset($cws_ytp_slider['slidesToScroll']) ? $cws_ytp_slider['slidesToScroll']: "";
    $dots           = isset($cws_ytp_slider['dots']) ? $cws_ytp_slider['dots']: "";
    $arrows         = isset($cws_ytp_slider['arrows']) ? $cws_ytp_slider['arrows']: "";
    $autoplay       = isset($cws_ytp_slider['autoplay']) ? $cws_ytp_slider['autoplay']: "";
    $infinite       = isset($cws_ytp_slider['infinite']) ? $cws_ytp_slider['infinite']: "";
    $autoplaySpeed  = isset($cws_ytp_slider['autoplaySpeed']) ? $cws_ytp_slider['autoplaySpeed']: "";
    $speed          = isset($cws_ytp_slider['speed']) ? $cws_ytp_slider['speed']: "";
    $duration       = isset($cws_ytp_slider['duration']) ? $cws_ytp_slider['duration']: "";
    $playIcon       = isset($cws_ytp_slider['playIcon']) ? $cws_ytp_slider['playIcon']: "";
    $title          = isset($cws_ytp_slider['title']) ? $cws_ytp_slider['title']: "";

    // Create Slider Advanced 1 vars with values stored in db
    // these cannot be overridden in shortcode, it will get unmanagable
    $breakpoint_adv1        = isset($cws_ytp_slider_adv['breakpoint_1']) ? $cws_ytp_slider_adv['breakpoint_1']: "";
    $slidesToShow_adv1      = isset($cws_ytp_slider_adv['slidesToShow_1']) ? $cws_ytp_slider_adv['slidesToShow_1']: "";
    $slidesToScroll_adv1    = isset($cws_ytp_slider_adv['slidesToScroll_1']) ? $cws_ytp_slider_adv['slidesToScroll_1']: "";
    $dots_adv1              = isset($cws_ytp_slider_adv['dots_1']) ? $cws_ytp_slider_adv['dots_1']: "";
    $arrows_adv1            = isset($cws_ytp_slider_adv['arrows_1']) ? $cws_ytp_slider_adv['arrows_1']: "";
    $autoplay_adv1          = isset($cws_ytp_slider_adv['autoplay_1']) ? $cws_ytp_slider_adv['autoplay_1']: "";
    $infinite_adv1          = isset($cws_ytp_slider_adv['infinite_1']) ? $cws_ytp_slider_adv['infinite_1']: "";
    $autoplaySpeed_adv1     = isset($cws_ytp_slider_adv['autoplaySpeed_1']) ? $cws_ytp_slider_adv['autoplaySpeed_1']: "";
    $speed_adv1             = isset($cws_ytp_slider_adv['speed_1']) ? $cws_ytp_slider_adv['speed_1']: "";
    $duration_adv1          = isset($cws_ytp_slider['duration_1']) ? $cws_ytp_slider['duration_1']: "";
    $playIcon_adv1          = isset($cws_ytp_slider['playIcon_1']) ? $cws_ytp_slider['playIcon_1']: "";
    $title_adv1             = isset($cws_ytp_slider['title_1']) ? $cws_ytp_slider['title_1']: "";

    // Create Slider Advanced 2 vars with values stored in db
    // these cannot be overridden in shortcode, it will get unmanagable
    $breakpoint_adv2        = isset($cws_ytp_slider_adv['breakpoint_2']) ? $cws_ytp_slider_adv['breakpoint_2']: "";
    $slidesToShow_adv2      = isset($cws_ytp_slider_adv['slidesToShow_2']) ? $cws_ytp_slider_adv['slidesToShow_2']: "";
    $slidesToScroll_adv2    = isset($cws_ytp_slider_adv['slidesToScroll_2']) ? $cws_ytp_slider_adv['slidesToScroll_2']: "";
    $dots_adv2              = isset($cws_ytp_slider_adv['dots_2']) ? $cws_ytp_slider_adv['dots_2']: "";
    $arrows_adv2            = isset($cws_ytp_slider_adv['arrows_2']) ? $cws_ytp_slider_adv['arrows_2']: "";
    $autoplay_adv2          = isset($cws_ytp_slider_adv['autoplay_2']) ? $cws_ytp_slider_adv['autoplay_2']: "";
    $infinite_adv2          = isset($cws_ytp_slider_adv['infinite_2']) ? $cws_ytp_slider_adv['infinite_2']: "";
    $autoplaySpeed_adv2     = isset($cws_ytp_slider_adv['autoplaySpeed_2']) ? $cws_ytp_slider_adv['autoplaySpeed_2']: "";
    $speed_adv2             = isset($cws_ytp_slider_adv['speed_2']) ? $cws_ytp_slider_adv['speed_2']: "";
    $duration_adv2          = isset($cws_ytp_slider['duration_2']) ? $cws_ytp_slider['duration_2']: "";
    $playIcon_adv2          = isset($cws_ytp_slider['playIcon_2']) ? $cws_ytp_slider['playIcon_2']: "";
    $title_adv2             = isset($cws_ytp_slider['title_2']) ? $cws_ytp_slider['title_2']: "";

    // Create Slider Advanced 3 vars with values stored in db
    // these cannot be overridden in shortcode, it will get unmanagable
    $breakpoint_adv3        = isset($cws_ytp_slider_adv['breakpoint_3']) ? $cws_ytp_slider_adv['breakpoint_3']: "";
    $slidesToShow_adv3      = isset($cws_ytp_slider_adv['slidesToShow_3']) ? $cws_ytp_slider_adv['slidesToShow_3']: "";
    $slidesToScroll_adv3    = isset($cws_ytp_slider_adv['slidesToScroll_3']) ? $cws_ytp_slider_adv['slidesToScroll_3']: "";
    $dots_adv3              = isset($cws_ytp_slider_adv['dots_3']) ? $cws_ytp_slider_adv['dots_3']: "";
    $arrows_adv3            = isset($cws_ytp_slider_adv['arrows_3']) ? $cws_ytp_slider_adv['arrows_3']: "";
    $autoplay_adv3          = isset($cws_ytp_slider_adv['autoplay_3']) ? $cws_ytp_slider_adv['autoplay_3']: "";
    $infinite_adv3          = isset($cws_ytp_slider_adv['infinite_3']) ? $cws_ytp_slider_adv['infinite_3']: "";
    $autoplaySpeed_adv3     = isset($cws_ytp_slider_adv['autoplaySpeed_3']) ? $cws_ytp_slider_adv['autoplaySpeed_3']: "";
    $speed_adv3             = isset($cws_ytp_slider_adv['speed_3']) ? $cws_ytp_slider_adv['speed_3']: ""; 
    $duration_adv3          = isset($cws_ytp_slider['duration_3']) ? $cws_ytp_slider['duration_3']: "";
    $playIcon_adv3          = isset($cws_ytp_slider['playIcon_3']) ? $cws_ytp_slider['playIcon_3']: "";
    $title_adv3             = isset($cws_ytp_slider['title_3']) ? $cws_ytp_slider['title_3']: "";

    // get options from shortcode 
    $args = shortcode_atts( array(
                                    'vid' => isset($vid) ? $vid: '',
                                    'view' => $strView,
                                    'slidestoshow' => $slidesToShow,
                                    'slidestoscroll' => $slidesToScroll,
                                    'dots' => (bool) $dots,
                                    'arrows' => $arrows,
                                    'infinite' => $infinite,
                                    'autoplay' => $autoplay,
                                    'channelid' => isset($channelId) ? $channelId: '',
                                    'username' => isset($username) ? $username: '',
                                    'channelheader' => $showChannelHeader,
                                    'channeldescription' => $showChannelDescription,
                                    'showsocial' => $showSocial, 
                                    'showsubscribe' => $showSubscribe,
                                    'showlikes' => $showLikes,
                                    'showviews' => $showViews,
                                    'showdate' => $showDate,
                                    'maxresults' => $maxResults,
                                    'autoplayspeed' => $autoplaySpeed,  
                                    'speed' => $speed,  
                                    ), $atts );

    if( $plugin->get_isPro() == 1 ){
        $arrAllowedViews = array( 'grid','list','carousel' );
    } else {
        $arrAllowedViews = array( 'grid','list');
        if($args['maxresults'] > 6){
            $args['maxresults'] = 6;
        }
        // split vids by ,
        $vids = explode(',', $args['vid']);
        // $vid = $vids[0];
        $args['vid'] = $vids[0];
    }

    if ( !in_array( $args['view'], $arrAllowedViews ) ) {
        // reset to grid view if value set in shortcode is not recognised
        $args['view'] = 'grid';
    }

    // Debug display array
    if(  isset($_GET['cws_debug'])  == "1" ) { 
        echo '<pre>';
        print_r($args);
        echo '</pre>'; 
    }

    // Load dependencies...
    // Initialize any scripts?
    wp_register_script( 'cws_ytp_init', plugin_dir_url( __FILE__ )  . 'js/init-video.js' );

    // if view is carousel include JavaScript for it
    // not really the best way, as it's loading slick.js when not require by view i.e. not carousel!!!!!
    // need to do it this way as slick is initialized in CWS.YouTube object unless I can work out another way to do it
    //if( $plugin->get_isPro() == 1 ) 
    {   
    
        wp_enqueue_script( 'cws_ytp_slick', plugin_dir_url( __FILE__ )  . '/pro/lib/slick/slick.js', array( 'jquery','my_app' ), false, true  );

        $dataToBePassedtoSlick = array (
                                        // Wrap values in an inner array to protect boolean and integers
                                        'inner' => array(
                                                            'slidesToShow'      => (int) $args['slidestoshow'],
                                                            'slidesToScroll'    => (int) $args['slidestoscroll'],
                                                            'dots'              => (bool) $args['dots'],
                                                            'arrows'            => (bool) $args['arrows'],                                 
                                                            'infinite'          => (bool) $args['infinite'],
                                                            'autoplay'          => (bool) $args['autoplay'],
                                                            'autoplaySpeed'     => (int) $args['autoplayspeed'],
                                                            'speed'             => (int) $args['speed'],

                                                            'breakpoint_adv1'       => (int) $breakpoint_adv1,
                                                            'slidesToShow_adv1'     => (int) $slidesToShow_adv1,
                                                            'slidesToScroll_adv1'   => (int) $slidesToScroll_adv1,
                                                            'dots_adv1'             => (bool) $dots_adv1,
                                                            'arrows_adv1'           => (bool) $arrows_adv1,                                 
                                                            'infinite_adv1'         => (bool) $infinite_adv1,
                                                            'autoplay_adv1'         => (bool) $autoplay_adv1,
                                                            'autoplaySpeed_adv1'    => (int) $autoplaySpeed_adv1,
                                                            'speed_adv1'            => (int) $speed_adv1,  

                                                            'breakpoint_adv2'       => (int) $breakpoint_adv2,
                                                            'slidesToShow_adv2'     => (int) $slidesToShow_adv2,
                                                            'slidesToScroll_adv2'   => (int) $slidesToScroll_adv2,
                                                            'dots_adv2'             => (bool) $dots_adv2,
                                                            'arrows_adv2'           => (bool) $arrows_adv2,                                 
                                                            'infinite_adv2'         => (bool) $infinite_adv2,
                                                            'autoplay_adv2'         => (bool) $autoplay_adv2,
                                                            'autoplaySpeed_adv2'    => (int) $autoplaySpeed_adv2,
                                                            'speed_adv2'            => (int) $speed_adv2,    
                                                            
                                                            'breakpoint_adv3'       => (int) $breakpoint_adv3,
                                                            'slidesToShow_adv3'     => (int) $slidesToShow_adv3,
                                                            'slidesToScroll_adv3'   => (int) $slidesToScroll_adv3,
                                                            'dots_adv3'             => (bool) $dots_adv3,
                                                            'arrows_adv3'           => (bool) $arrows_adv3,                                 
                                                            'infinite_adv3'         => (bool) $infinite_adv3,
                                                            'autoplay_adv3'         => (bool) $autoplay_adv3,
                                                            'autoplaySpeed_adv3'    => (int) $autoplaySpeed_adv3,
                                                            'speed_adv3'            => (int) $speed_adv3,                                                                                                                                                                                  
                                                        ),
                                        );  

        // Debug display array
    if(  isset($_GET['cws_debug'])  == "1" ) { 
            echo '<pre>Slider ';
            var_dump($dataToBePassedtoSlick['inner']);
            echo '</pre>';   
        }

        // Initialize Slick        
        // wp_register_script( 'cws_ytp_init_slick', plugin_dir_url( __FILE__ )  . 'lib/slick/init_slick.js' );       
        wp_register_script( 'cws_ytp_init_slick', plugin_dir_url( __FILE__ )  . '/pro/lib/slick/init_slick.js' );       
        wp_localize_script( 'cws_ytp_init_slick', 'php_vars_slick', $dataToBePassedtoSlick );
        wp_enqueue_script( 'cws_ytp_init_slick', array( 'cws_ytp_slick'), false , true ); 
    }

    $dataToBePassed = array(
                            'vid'                       => $args['vid'],
                            'api_key'                   => $api_key,
                            'channelid'                 => $args['channelid'],
                            'username'                  => $args['username'],
                            'maxResults'                => $args['maxresults'],
                            'showSubscribe'             => $showSubscribe,
                            'showSocial'                => $args['showsocial'],
                            'showSubscribe'             => $args['showsubscribe'],
                            'arrSocial'                 => $arrSocial,
                            'showChannelHeader'         => $args['channelheader'],
                            'showChannelDescription'    => $args['channeldescription'],
                            'view'                      => $args['view'],
                            'showLikes'                 => $args['showlikes'],
                            'showViews'                 => $args['showviews'],
                            'showDate'                  => $args['showdate'],  
                        );

// echo '<pre>';
// print_r($dataToBePassed); 
// echo '</pre>';
//die();

    wp_localize_script( 'cws_ytp_init', 'php_vars', $dataToBePassed );
    wp_enqueue_script( 'cws_ytp_init', plugin_dir_url( __FILE__ )  . 'js/init-video.js', array(), false, true );

    $strOutput = '<div class="cws-ytp"><div id="cws-ytp-header">
            <!-- <a href="#" id="video-stop-button" class="paging-button">×</a> -->
            </div>

        <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
            <div id="cws-ytp-background-image">

            <div id="cws-ytp-video-container-wrapper">

                <a href="#" id="cws-ytp-video-stop-button" class="paging-button">×</a>

                <div id="cws-ytp-video-container">

                    <div id="cws-ytp-video-controls">
                        <a href="#" id="cws-ytp-video-play-button">
                            <i class="paging-button  fa fa-play"></i>
                        </a>
                        <a href="#" id="cws-ytp-video-prev-button" class="paging-button cws-ytp-next-previous-video">
                            <i class="fa fa-angle-left"></i>
                        </a>
                        <a href="#" id="cws-ytp-video-next-button" class="paging-button cws-ytp-next-previous-video">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </div>

                    <div id="cws-ytp-video-placeholder"></div>
                </div>
            </div>      

        </div>

        <div id="cws-ytp-video-description-wrapper">
                <div id="cws-ytp-video-title"></div>
                <div id="cws-ytp-video-meta"></div>
                <div id="cws-ytp-video-description"></div>
                <div id="cws-ytp-video-description-controls"><a href="#" id="cws-ytp-descToggle"><i class="fa fa-caret-down"></i></a></div>         
            </div>    

        <!-- TODO: Could move this into JavaScript file -->
        <div class="cws-ytp-button-container">
            <!-- <div id="cws-ytp-pagination">
                <a href="#" title="Previous page of videos" id="cws-ytp-prev-button" class="paging-button"><i class="fa fa-chevron-left"></i></a>
                <a href="#" title="Next page of videos" id="cws-ytp-next-button" class="paging-button"><i class="fa fa-chevron-right"></i></a>
            </div> -->

           <!-- <div id="view-layout">
                <a href="#" title="Grid View" id="grid-button" class="paging-button"><i class="fa fa-th"></i></a>
                <a href="#" title="List View" id="list-button" class="paging-button"><i class="fa fa-th-list"></i></a>
            </div> -->
        </div>

        <div id="cws_ytp"></div>';

    return $strOutput;

}