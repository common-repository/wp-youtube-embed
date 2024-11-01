<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://cheshirewebsolutions.com/
 * @since      1.0.0
 *
 * @package    CWS_YouTube_Pro
 * @subpackage CWS_YouTube_Pro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class CWS_YouTube_Pro_Admin {

    var $debug = false;
    
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
	 * The is user authenticated with Google.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $authenticated    Check is user authenticated with Google.
	 */    
    private $authenticated = 0;

    /**
     * The check is this a Pro version.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $isPro    Check is if this is pro version.
     */    
    private $isPro ;  

    /**
     * User Id used to check ignore upgrade notice.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $userId    User Id of loggedin user.
     */ 
    private $userId;
    
    //var $client; // Used for Google Client
    
	/**
	 * Initialize the class and set its properties.
	 *
     * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $isPro ) {

        $this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->isPro = $isPro;
     
        // Include required files
		$this->includes();
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function includes() {

		if( $this->debug ) error_log( 'Inside: CWS_YTPro::includes()' );
		
		if ( is_admin() ) $this->admin_includes();
            
        include_once( dirname(__FILE__) . '/../cws-ytp-functions.php' );				// TODO: split file out in admin and non-admin functions
		include_once( dirname(__FILE__) . '/../shortcodes/shortcode-init.php' );		// Init the shortcodes
        //include_once( dirname(__FILE__) . '/../widgets/widget-init.php' );				// Widget classes		

        if( $this->isPro == 1 ) {
            // TODO: change this into an include for Pro shortcodes...
            add_shortcode( 'cws_ytp_images_by_albumid', 'cws_ytp_shortcode_images_in_album' );  // new one, shortcode provides album id
        }
	}


	public function admin_includes() {
	
		if( $this->debug ) error_log( 'Inside: CWS_YTPro::admin_includes()' );

		include_once( dirname(__FILE__) . '/../cws-ytp-functions.php' );				// TODO: split file out in admin and non-admin functions
	}    
    

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cws-youtube-pro-admin.css', array(), $this->version, 'all' );
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cws-youtube-pro-admin.js', array( 'jquery' ), $this->version, false );
	}
    

    /**
     * Check if the plugin is a Pro version.
     *
     * @since    1.0.0
     */  
    private function get_Pro( $isPro ) {

        if( $isPro == 1 ){ return "Pro"; }
        return;
    }


    /**
     * Register the Top Level Menu Page for the admin area.
     *
     * @since    1.0.0
     */    
    public function add_menu_page() {

        $strIsPro = $this->get_Pro( $this->isPro );

        add_menu_page( 'Page Title', 'YouTube ' . $strIsPro, 'manage_options', 'cws_ytp', array( $this, 'ytp_options_page' ), 'dashicons-video-alt3' );
        add_submenu_page( 'cws_ytp', 'YouTube Pro Settings', 'Settings', 'manage_options', 'cws_ytp', array( $this, 'ytp_options_page' ) );
    }    


    /**
     * Register the Options for the admin area.
     *
     * @since    1.0.0
     */    
    public function add_options_ytp_sc_page() {

        add_submenu_page( 'cws_ytp', 'YouTube Shortcodes', 'Shortcode Examples', 'manage_options', 'cws_sc', array( $this, 'ytp_options_page_sc' ) );
    }
 

    /**
     * Register the Getting Started Options for the admin area.
     *
     * @since    1.0.0
     */    
    public function add_options_gs_page() {
        add_submenu_page( 'cws_ytp', 'YouTube Getting Started', 'Getting Started', 'manage_options', 'cws_gs', array( $this, 'ytp_options_page_gs' ) );
    }


    /**
     * Draw the Options page for the admin area. This contains simple shortcode snippets
     *
     * @since    1.0.0
     */
    public function ytp_options_page_gs() {

        $strIsPro = $this->get_Pro( $this->isPro );
 ?>
        <div class="wrap">
        <?php screen_icon(); ?>
            <h2> YouTube <?php echo $strIsPro ?> Getting Started</h2>

                <div style="width: 95%;" class="postbox-container">
                    <h2>1. Get API KEY</h2>
                    <ol>
                        <li>Visit <a href="https://code.google.com/apis/console" target="_blank">Google API Console</a></li> <!-- https://www.youtube.com/watch?v=Im69kzhpR3I -->
                        <li>Click 'Create Project' and give it a name</li>
                        <li><a href="https://console.developers.google.com/apis/library/youtubeanalytics.googleapis.com" target="_blank">Enable YouTube Data API v3</a></li>
                        <li>Click Credentials -> Create Credentials -> API Key</a></li>
                        <?php $url = admin_url() . 'admin.php?page=cws_ytp&tab=api_key'; ?>
                        <li>Copy and paste API Key into <a href="<?php echo $url; ?>">here</a> and click 'Save'</li>
                    </ol>

                    <br/>                 
                    
                    <h3>Embed YouTube Channel</h3>
                    <p><strong>Shortcode to embed YouTube Channel</strong></p>
                    <p>Below is an example of the shortcode to embed YouTube Channel. Place the shortcode on a page and update the <span class="sc-highlight"><i>channelid='UC4lp9Emg1ci8eo2eDkB-Tag'</i></span> with the channel id you want to embed. OR <span class="sc-highlight"><i>username='bbc'</i></span> with the username you want to embed.</p>
                    <p><strong>[cws_ytp_channel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</strong></p>
                    <p><strong>[cws_ytp_channel username=bbc]</strong></p>
                    <?php $scUrl = admin_url() . 'admin.php?page=cws_sc'; ?>
                    <p>For information on shortcode options please see <a href="<?php echo $scUrl; ?>">Shortcode Example</a> page</p>

                    <br/> 

                    <h3>Embed YouTube Playlist</h3>
                    <p><strong>Shortcode to embed YouTube Playlist</strong></p>
                    <p>Below is an example of the shortcode to embed YouTube Playlist. Place the shortcode on a page and update the <span class="sc-highlight"><i>plid='PLdhB2hC90YEt-jqYvDcStJyFLjHeLJVHd'</i></span> with the playlist id you want to embed.</p>
                    <p><strong>[cws_ytp_playlistitems plid='PLdhB2hC90YEt-jqYvDcStJyFLjHeLJVHd']</strong></p>
                    <p>For information on shortcode options please see <a href="<?php echo $scUrl; ?>">Shortcode Example</a> page</p>
                    <br/> 

                    <h3>Embed YouTube Video</h3>
                    <p><strong>Shortcode to embed YouTube Videos</strong></p>
                    <p>Below is an example of the shortcode to embed YouTube Videos. Place the shortcode on a page and update the <span class="sc-highlight"><i>vid=8wkTc5yy2-M,knx7guByH4I,8wkTc5yy2-M,knx7guByH4I,8wkTc5yy2-M,knx7guByH4I,8wkTc5yy2-M,knx7guByH4I,8wkTc5yy2-M,knx7guByH4I]</i></span> with the playlist id you want to embed.</p>
                    <p><strong>[cws_ytp_video vid=8wkTc5yy2-M,knx7guByH4I,8wkTc5yy2-M,knx7guByH4I,8wkTc5yy2-M,knx7guByH4I,8wkTc5yy2-M,knx7guByH4I,8wkTc5yy2-M,knx7guByH4I]</strong></p>

                    <p>For information on shortcode options please see <a href="<?php echo $scUrl; ?>">Shortcode Example</a> page</p>
                </div> <!-- / .postbox-container -->
        <?php
        } // end function ytp_options_page_gs()

    
    /**
     * Draw the Options page for the admin area. This contains simple shortcode snippets.
     *
     * @since    1.0.0
     */
    public function ytp_options_page_sc() {
 ?>
        <div class="wrap">
        <?php screen_icon(); ?>
            <h2>YouTube <?php echo $this->get_Pro( $this->isPro );?> Shortcode Examples</h2>

            <div style="width: 95%;" class="postbox-container">
                    <div class="metabox-holder">
          
                        <div class="postbox" id="settings">
                            <p>More working examples can be found on the <a href="http://wordpress-plugin-youtube.cheshirewebsolutions.com/" target="_blank">Demo site</a></p>
                        </div>

                            <h2>YouTube Channel</h2>
                            <p>Use this shortcode to display a YouTube Channel specified by the Channel ID.</p>
                            <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_ytp_channel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]" readonly="readonly" onfocus="this.select();">       
                               
                            <h2>YouTube Playlist</h2>
                            <p>Use this shortcode to display a YouTube Playlist the Playlist ID.</p>                            
                            <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_ytp_playlistitems plid='PLdhB2hC90YEt-jqYvDcStJyFLjHeLJVHd']" readonly="readonly" onfocus="this.select();">       

                            <h2>YouTube Videos</h2>
                            <p>Use this shortcode to display a YouTube Playlist the Playlist ID.</p>   
                            <input size="80" type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="[cws_ytp_video vid=8wkTc5yy2-M,knx7guByH4I,8wkTc5yy2-M,knx7guByH4I]" readonly="readonly" onfocus="this.select();">       

                            <h2>View Options</h2>
                            <p>Many of the Default Options can be overridden on a individual shortcode basis by adding options to the shortcode.</p>
                            <p>There a 3 view options <code>grid|list|carousel</code>. Carousel is available only in the <a href="http://wordpress-plugin-youtube.cheshirewebsolutions.com/" target="_blank">Pro Version</a></p>

                                        <!-- VIEW OPTIONS -->
                                        <table class="wp-list-table widefat fixed posts">
                                            <thead>
                                             <tr valign="top">
                                                  <th scope="row" width="200">Description</th>
                                                  <th scope="row" width="150">Option</th>
                                                  <th scope="row">Example Shortcode</th>
<!--                                                   <th scope="row" width="150">See Live Example</th>
 -->                                             </tr>
                                            </thead>                            
                                                <tbody data-wp-lists="list:post" id="the-list">
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Display Videos in Grid</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>view=grid</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel view=grid channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td><a href="http://wordpress-plugin-youtube.cheshirewebsolutions.com/" target="_blank">View on Demo Site</a></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Display Videos in List</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>view=list</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel view=list channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td><a href="http://wordpress-plugin-youtube.cheshirewebsolutions.com/" target="_blank">View on Demo Site</a></td>
 -->                                                    <tr>
                                                    </tr>                                                          
                                                        <td class="title column-title">
                                                            <strong>Display Videos in Carousel</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>view=carousel</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td><a href="http://wordpress-plugin-youtube.cheshirewebsolutions.com/" target="_blank">View on Demo Site</a></td>                                                  
 -->                                                    </tr>
                                                    </tr>                                                          
                                                        <td class="title column-title">
                                                            <strong>Number of Videos per Page</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>maxresults=12</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel maxresults=12 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>                                                  
 -->                                                    </tr>                                                    
                                                 </tbody>
                                            <foot>
                                             <tr valign="top">
                                                  <th scope="row" width="100">Description</th>
                                                  <th scope="row" width="150">Option </th>
                                                  <th scope="row">Example Shortcode</th>                                                 
<!--                                                   <th scope="row" width="150">See Live Example</th>
 -->                                             </tr>
                                            </foot>                                  
                                        </table>  

                            <h2>Header Options</h2>
                            <p>Many of the Default Options can be overridden on a individual shortcode basis by adding options to the shortcode.</p>
                            <p>There a 3 view options <code>grid|list|carousel</code>. Social and Subscribe Option is available only in the <a href="http://wordpress-plugin-youtube.cheshirewebsolutions.com/" target="_blank">Pro Version</a></p>

                                        <!-- HEADER OPTIONS -->
                                        <table class="wp-list-table widefat fixed posts">
                                            <thead>
                                             <tr valign="top">
                                                  <th scope="row" width="200">Description</th>
                                                  <th scope="row" width="200">Option</th>
                                                  <th scope="row">Example Shortcode</th>
<!--                                                   <th scope="row" width="150">See Live Example</th>
 -->                                             </tr>
                                            </thead>                            
                                                <tbody data-wp-lists="list:post" id="the-list">
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Show Channel Logo</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>channelheader=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel channelheader=1 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Hide Channel Logo</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>channelheader=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel channelheader=0 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>

                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Show Channel Description</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>channeldescription=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel channeldescription=1 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Hide Channel Description</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>channeldescription=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel channeldescription=0 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>

                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Show Social Icons</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showsocial=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showsocial=1 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Hide Social Icons</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showsocial=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showsocial=0 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>

                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Show YouTube Subscribe Icon</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showsubscribe=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showsubscribe=1 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Show YouTube Subscribe Icon</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showsubscribe=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showsubscribe=0 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>                                                    

                                                        
                                                    </tr>
                                                 </tbody>
                                            <foot>
                                             <tr valign="top">
                                                  <th scope="row" width="100">Description</th>
                                                  <th scope="row" width="200">Option </th>
                                                  <th scope="row">Example Shortcode</th>                                                 
<!--                                                   <th scope="row" width="150">See Live Example</th>
 -->                                             </tr>
                                            </foot>                                  
                                        </table>  


                            <h2>Video Meta Options</h2>
                            <p>Many of the video meta Options can be overridden on a individual shortcode basis by adding options to the shortcode.</p>

                                        <!-- VIDEO META OPTIONS -->
                                        <table class="wp-list-table widefat fixed posts">
                                            <thead>
                                             <tr valign="top">
                                                  <th scope="row" width="200">Description</th>
                                                  <th scope="row" width="200">Option</th>
                                                  <th scope="row">Example Shortcode</th>
<!--                                                   <th scope="row" width="150">See Live Example</th>
 -->                                             </tr>
                                            </thead>                            
                                                <tbody data-wp-lists="list:post" id="the-list">
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Show Views</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showviews=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showviews=1 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Hide Views</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showviews=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showviews=0 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Show Likes</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showlikes=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showlikes=1 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Hide Likes</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showlikes=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showlikes=0 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>                                                 
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Show Date</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showdate=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showdate=1 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Hide Date</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>showdate=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel showdate=0 channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>  
                                                        
                                                    </tr>
                                                 </tbody>
                                            <foot>
                                             <tr valign="top">
                                                  <th scope="row" width="100">Description</th>
                                                  <th scope="row" width="200">Option </th>
                                                  <th scope="row">Example Shortcode</th>                                                 
<!--                                                   <th scope="row" width="150">See Live Example</th>
 -->                                             </tr>
                                            </foot>                                  
                                        </table>  

                            <h2>Slider Options</h2>
                            <p>Many of the Slider Options can be overridden on a individual shortcode basis by adding options to the shortcode.</p>

                                        <!-- SLIDER OPTIONS -->
                                        <table class="wp-list-table widefat fixed posts">
                                            <thead>
                                             <tr valign="top">
                                                  <th scope="row" width="200">Description</th>
                                                  <th scope="row" width="200">Option</th>
                                                  <th scope="row">Example Shortcode</th>
<!--                                                   <th scope="row" width="150">See Live Example</th>
 -->                                             </tr>
                                            </thead>                            
                                                <tbody data-wp-lists="list:post" id="the-list">
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Slides to Show</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>slidestoshow=4</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel slidestoshow=4 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Slides to Scroll</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>slidestoscroll=4</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel slidestoscroll=4 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Show Arrows</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>arrows=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel arrows=1 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr> 
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Hide Arrows</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>arrows=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel arrows=0 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr> 
                                                    <tr>
                                                    <td class="title column-title">
                                                            <strong>Show Dots</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>dots=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel dots=1 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr> 
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Hide Dots</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>dots=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel dots=0 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr> 
                                                    <tr>
                                                    <td class="title column-title">
                                                            <strong>Autoplay Enabled </strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>autoplay=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel autoplay=1 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr> 
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Autoplay Disabled</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>autoplay=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel autoplay=0 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>                                                      
                                                    <td class="title column-title">
                                                            <strong>Infinite Scroll Enabled </strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>infinite=1</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel infinite=1 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr> 
                                                    <tr>
                                                        <td class="title column-title">
                                                            <strong>Infinite Scroll Disabled</strong>
                                                        </td>
                                                        <td class="shortcode column-description"><code>infinite=0</code></td>
                                                        <td class="shortcode column-shortcode"><code>[cws_ytp_channel infinite=0 view=carousel channelid=UC4lp9Emg1ci8eo2eDkB-Tag]</code></td>
<!--                                                         <td></td>
 -->                                                    </tr>                                                          
                                                 </tbody>
                                            <foot>
                                             <tr valign="top">
                                                  <th scope="row" width="100">Description</th>
                                                  <th scope="row" width="200">Option </th>
                                                  <th scope="row">Example Shortcode</th>                                                 
<!--                                                   <th scope="row" width="150">See Live Example</th>
 -->                                             </tr>
                                            </foot>                                  
                                        </table>  

                    </div>
            </div>
        </div>

        <?php
        } // end function ytp_options_page_sc()


	/**
	 * Draw the Options page for the admin area.
	 *
	 * @since    1.0.0
	 */
    public function ytp_options_page() {
        /*
        if( $this->deauthorizeGoogleAccount() ) {
            // TODO: finsish this delete_option unset()
            delete_option( 'cws_ytp_reset' );
            delete_option( 'cws_ytp_token_expires' );
            delete_option( 'cws_ytp_code' );
            delete_option( 'cws_ytp_access_token' );
        } 
        */
        ?>
        
        <div class="wrap">
        <?php screen_icon(); ?>
            <h2>YouTube <?php echo $this->get_Pro( $this->isPro );?> Settings</h2>

            <!-- https://code.tutsplus.com/tutorials/the-wordpress-settings-api-part-5-tabbed-navigation-for-settings--wp-24971 -->
            <?php
            $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'default_settings';
            ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=cws_ytp&tab=default_settings" class="nav-tab <?php echo $active_tab == 'default_settings' ? 'nav-tab-active' : ''; ?>">Default Settings</a>
            <?php 
            // Display Social Options and Slider Options ONLY if Pro Version
            if( $this->isPro ){ ?>
                <a href="?page=cws_ytp&tab=social_options" class="nav-tab <?php echo $active_tab == 'social_options' ? 'nav-tab-active' : ''; ?>">Social Options</a>
                <a href="?page=cws_ytp&tab=slider_options" class="nav-tab <?php echo $active_tab == 'slider_options' ? 'nav-tab-active' : ''; ?>">Slider Options</a>
                <a href="?page=cws_ytp&tab=slider_options_adv" class="nav-tab <?php echo $active_tab == 'slider_options_adv' ? 'nav-tab-active' : ''; ?>">Slider Advanced Options</a>

            <?php } ?>

                <a href="?page=cws_ytp&tab=api_key" class="nav-tab <?php echo $active_tab == 'api_key' ? 'nav-tab-active' : ''; ?>">API Key</a>
            </h2>
            <!-- IK Tabs -->

            <div class="widget-liquid-left">

                <form action="options.php" method="post">

                <?php 
                    $code = get_option( 'cws_ytp_code' );
                    $api_key = $code['api_key'];

                    if( $active_tab == 'api_key' ) {
                        // API KEY
                        settings_fields( 'cws_ytp_code' );
                        do_settings_sections( 'cws_ytp_code' );
                    } elseif( $active_tab == 'default_settings' ) {
                        // Defaults                        
                        settings_fields( 'cws_ytp_defaults' );
                        do_settings_sections( 'cws_ytp_defaults' );
                    } elseif( $active_tab == 'social_options' ) {
                        // Social Media links
                        settings_fields( 'cws_ytp_social' );
                        do_settings_sections( 'cws_ytp_social' );                   
                    } elseif( $active_tab == 'slider_options' ) {
                        // Slider Options Advanced
                        settings_fields( 'cws_ytp_slider' );
                        do_settings_sections( 'cws_ytp_slider' );
                    } else {
                        // Slider Options Advanced
                        settings_fields( 'cws_ytp_slider_adv' );
                        do_settings_sections( 'cws_ytp_slider_adv' );
                    }                    

                    ?>

                    <input name="Submit" type="submit" value="Save Changes" />  

                </form> 

            </div><!-- / left -->
                <?php // $this->cws_ytp_meta_box_feedback(); ?>

            <?php
            
                if( !$this->isPro == 1 ) {
                    // Only call for the upgrade meta box if this is not a Pro install
                    // $this->cws_ytp_meta_box_pro(); 
                }
            
            ?>

        </div>
        <?php
    }
    

    /**
     * Draw Feedback Link area. TODO: Insert link to plugin 
     *
     * @since    1.0.0
     */
    private function cws_ytp_meta_box_feedback() {
    ?>

        <div class="widget-liquid-right">
            <div id="widgets-right">    
                <div style="width:20%;" class="postbox-container side">
                    <div class="metabox-holder">
                        <div class="postbox" id="feedback">
                            <h3><span>Please rate the plugin!</span></h3>
                            <div class="inside">                            
                                <p>If you have found this useful please leave us a <a href="http://wordpress.org/extend/plugins/">good rating</a></p>
                                <p>If you have found a bug please email me <a href="mailto:info@cheshirewebsolutions.com?subject=Feedback%20Google%20Picasa%20Viewer">info@cheshirewebsolutions.com</a></p>                               
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>  
        
    <?php
        
    }


    /**
     * Draw Promo Box
     *
     * @since    1.0.0
     */
    private function cws_ytp_meta_box_pro() {
    ?>
    <div class="widget-liquid-right">
        <div id="widgets-right">
            <!-- <div style="width:20%;" class="postbox-container side"> -->
            <div class="postbox-container side">
                <div class="metabox-holder">
                    <div class="postbox" id="donate">
                        <?php echo $this->cws_ytp_upgrade_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div><?php   
    }


    /**
     * Upgrade Content to be used
     * seperate upgrade content from markup so can use content in more places, like shortcode snippets page and pro shortcodes in the frontend
     *
     * @since    1.0.0
     * 
     */
    public function cws_ytp_upgrade_content() {

        $strOutput = "<div style='text-align:center;'><h3><span>Get WP YouTube Pro!</span></h3>
                    <div class=\"inside\">
                        <p>You may like to consider upgrading to the <a href=\"http://www.cheshirewebsolutions.com/wp-youtube-for-wordpress-plugin/\">Pro</a> version of the plugin.                        
                        <a href=\"http://www.cheshirewebsolutions.com/wp-youtube-for-wordpress-plugin/\">Download it here</a> <span><strong>GET 10% OFF!</strong> – use discount code <strong>YTPDisc10</strong> on checkout</span></p>
                        <h3>
                            Included in Pro!
                        </h3>
                        <ol>
                            <li>Increased Maximum results allowed by API</li>
                            <li>Included Carousel slider view with responsive breakpoint settings</li>
                            <li>The ability to include YouTube videos based on keywords</li>
                            <li>The ability to include multiple videos by video id</li>
                            <li>Includes Social Icons</li>
                            <li>Included YouTube Channel Subscription link</li>
                        </ol>

                    </div>";

        return $strOutput;
    }


	/**
	 * Register Settings, Settings Section and Settings Fileds.
     * 
     * @link    https://codex.wordpress.org/Function_Reference/register_setting
     * @link    https://codex.wordpress.org/Function_Reference/add_settings_section
     * @link    https://codex.wordpress.org/Function_Reference/add_settings_field
	 *
	 * @since    1.0.0
	 */    
    public function register_plugin_settings() {

        // API Key Settings
        register_setting( 'cws_ytp_code', 'cws_ytp_code', array( $this, 'validate_options' ) );
        add_settings_section( 'cws_ytp_api_key', 'Put Google API Key here', array( $this, 'section_text' ), 'cws_ytp_code' );
        add_settings_field( 'cws_ytp_api_key', 'Google API Key', array( $this, 'setting_input' ), 'cws_ytp_code', 'cws_ytp_api_key' );

        // Default Settings
        register_setting( 'cws_ytp_defaults', 'cws_ytp_defaults', array( $this, 'validate_options_defaults' ) );
        add_settings_section( 'cws_ytp_defaults_settings', 'Defaults', array( $this, 'section_text3' ), 'cws_ytp_defaults' );
        add_settings_field( 'cws_ytp_defaults_settings3', 'Max Results', array( $this, 'setting_input3' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );        
        // Dont want to expose this to user
        //add_settings_field( 'cws_ytp_defaults_settings3_1', '<span title="Display Icons to change display of videos List of Grid view.">Show Layout Options</span>', array( $this, 'setting_input3_1' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );        
        
        add_settings_field( 'cws_ytp_defaults_settings3_2', '<span title="Display Social Media Icons.">Show Social Icons</span>', array( $this, 'setting_input3_2' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );
        add_settings_field( 'cws_ytp_defaults_settings3_3', '<span title="Display Number of Likes Video has.">Show Likes</span>', array( $this, 'setting_input3_3' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );
        add_settings_field( 'cws_ytp_defaults_settings3_4', '<span title="Display Number of Views Video has.">Show Views</span>', array( $this, 'setting_input3_4' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );
        add_settings_field( 'cws_ytp_defaults_settings3_5', '<span title="Display Date Video wes uploaded">Show Date</span>', array( $this, 'setting_input3_5' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );
        add_settings_field( 'cws_ytp_defaults_settings3_6', '<span title="Display YouTube Subscibe to Channel Icon">Show YouTube Subscribe</span>', array( $this, 'setting_input3_6' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );
        add_settings_field( 'cws_ytp_defaults_settings3_9', '<span title="Display Channel Header">Show Channel Header</span>', array( $this, 'setting_input3_9' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );
        add_settings_field( 'cws_ytp_defaults_settings3_7', '<span title="Display Channel Description when click on Logo">Show Channel Description</span>', array( $this, 'setting_input3_7' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );
        add_settings_field( 'cws_ytp_defaults_settings3_8', '<span title="Default View to be Used (Grid, List, Carsouel) this can be overidden in shortcodes">Default View</span>', array( $this, 'setting_input3_8' ), 'cws_ytp_defaults', 'cws_ytp_defaults_settings' );

        // Social Media Settings
        register_setting( 'cws_ytp_social', 'cws_ytp_social', array( $this, 'validate_options_social' ) );
        add_settings_section( 'cws_ytp_social_settings', 'Social Media Links', array( $this, 'section_text2' ), 'cws_ytp_social' );
        add_settings_field( 'cws_ytp_social_settings', 'Instagram Username', array( $this, 'setting_input2' ), 'cws_ytp_social', 'cws_ytp_social_settings' );
        add_settings_field( 'cws_ytp_social_settings2_1', 'Twitter Username', array( $this, 'setting_input2_1' ), 'cws_ytp_social', 'cws_ytp_social_settings' );
        add_settings_field( 'cws_ytp_social_settings2_2', 'Facebook Page ID', array( $this, 'setting_input2_2' ), 'cws_ytp_social', 'cws_ytp_social_settings' );
        add_settings_field( 'cws_ytp_social_settings2_3', 'Pinterest Username', array( $this, 'setting_input2_3' ), 'cws_ytp_social', 'cws_ytp_social_settings' );
        add_settings_field( 'cws_ytp_social_settings2_4', 'Tumblr Username', array( $this, 'setting_input2_4' ), 'cws_ytp_social', 'cws_ytp_social_settings' );
        add_settings_field( 'cws_ytp_social_settings2_5', 'Flickr Username', array( $this, 'setting_input2_5' ), 'cws_ytp_social', 'cws_ytp_social_settings' );

        // Slider Options
        register_setting( 'cws_ytp_slider', 'cws_ytp_slider', array( $this, 'validate_options_slider' ) );
        add_settings_section( 'cws_ytp_slider_settings', 'Slider Settings', array( $this, 'section_text4' ), 'cws_ytp_slider' );
        add_settings_field( 'cws_ytp_slider_settings', 'Slides To Show', array( $this, 'setting_input4' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );        
        add_settings_field( 'cws_ytp_slider_settings4_1', 'Slides To Scroll', array( $this, 'setting_input4_1' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );        
        add_settings_field( 'cws_ytp_slider_settings4_2', 'Arrows', array( $this, 'setting_input4_2' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );        
        add_settings_field( 'cws_ytp_slider_settings4_3', 'Dots', array( $this, 'setting_input4_3' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );
        add_settings_field( 'cws_ytp_slider_settings4_4', 'Autoplay', array( $this, 'setting_input4_4' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );        
        add_settings_field( 'cws_ytp_slider_settings4_5', 'Infinite', array( $this, 'setting_input4_5' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );
        add_settings_field( 'cws_ytp_slider_settings4_6', 'Autoplay Speed', array( $this, 'setting_input4_6' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );
        add_settings_field( 'cws_ytp_slider_settings4_7', 'Speed', array( $this, 'setting_input4_7' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );

        // hide overlay duration / title / play icon
        add_settings_field( 'cws_ytp_slider_settings4_8', 'Duration Overlay', array( $this, 'setting_input4_8' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );
        add_settings_field( 'cws_ytp_slider_settings4_9', 'Play Icon Overlay', array( $this, 'setting_input4_9' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );
        add_settings_field( 'cws_ytp_slider_settings4_10', 'Title Overlay', array( $this, 'setting_input4_10' ), 'cws_ytp_slider', 'cws_ytp_slider_settings' );


        // Add Advanced Settings for Slider - media query break points
        // Toggle open/close
        // 3 common breakpoints... defaults breakpoints can be overridden by user

        // Slider Advanced Options 1
        register_setting( 'cws_ytp_slider_adv', 'cws_ytp_slider_adv', array( $this, 'validate_options_slider_adv' ) );
        add_settings_section( 'cws_ytp_slider_adv_settings', 'Slider Advanced Settings', array( $this, 'section_text5' ), 'cws_ytp_slider_adv' );
        
        // first set of breakpoints
        add_settings_field( 'cws_ytp_slider_adv_settings5_0', 'Breakpoint 1', array( $this, 'setting_input5_0' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings', 'Slides To Show', array( $this, 'setting_input5' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_1', 'Slides To Scroll', array( $this, 'setting_input5_1' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_2', 'Arrows', array( $this, 'setting_input5_2' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_3', 'Dots', array( $this, 'setting_input5_3' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_4', 'Autoplay', array( $this, 'setting_input5_4' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_5', 'Infinite', array( $this, 'setting_input5_5' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_6', 'Autoplay Speed', array( $this, 'setting_input5_6' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_7', 'Speed', array( $this, 'setting_input5_7' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );

        // hide overlay duration / title / play icon
        add_settings_field( 'cws_ytp_slider_adv_settings5_8a', 'Duration Overlay', array( $this, 'setting_input5_8a' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_9a', 'Play Icon Overlay', array( $this, 'setting_input5_9a' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_10a', 'Title Overlay', array( $this, 'setting_input5_10a' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );

        // trying to use thhis a some sort of seperator....
        add_settings_field( 'cws_ytp_slider_adv_settings5_8', '<hr>', array( $this, 'setting_input5_8' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );

        // second set of breakpoints
        add_settings_field( 'cws_ytp_slider_adv_settings5_9', 'Breakpoint 2', array( $this, 'setting_input5_9' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings_10', 'Slides To Show', array( $this, 'setting_input5_10' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_11', 'Slides To Scroll', array( $this, 'setting_input5_11' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_12', 'Arrows', array( $this, 'setting_input5_12' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_13', 'Dots', array( $this, 'setting_input5_13' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_14', 'Autoplay', array( $this, 'setting_input5_14' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_15', 'Infinite', array( $this, 'setting_input5_15' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_16', 'Autoplay Speed', array( $this, 'setting_input5_16' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_17', 'Speed', array( $this, 'setting_input5_17' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );

        // hide overlay duration / title / play icon
        add_settings_field( 'cws_ytp_slider_adv_settings5_28', 'Duration Overlay', array( $this, 'setting_input5_28' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_29', 'Play Icon Overlay', array( $this, 'setting_input5_29' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_30', 'Title Overlay', array( $this, 'setting_input5_30' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );

        // trying to use thhis a some sort of seperator....
        add_settings_field( 'cws_ytp_slider_adv_settings5_18', '<hr>', array( $this, 'setting_input5_18' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );

        // third set of breakpoints
        add_settings_field( 'cws_ytp_slider_adv_settings5_19', 'Breakpoint 3', array( $this, 'setting_input5_19' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings_20', 'Slides To Show', array( $this, 'setting_input5_20' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_21', 'Slides To Scroll', array( $this, 'setting_input5_21' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_22', 'Arrows', array( $this, 'setting_input5_22' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_23', 'Dots', array( $this, 'setting_input5_23' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_24', 'Autoplay', array( $this, 'setting_input5_24' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );        
        add_settings_field( 'cws_ytp_slider_adv_settings5_25', 'Infinite', array( $this, 'setting_input5_25' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_26', 'Autoplay Speed', array( $this, 'setting_input5_26' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_27', 'Speed', array( $this, 'setting_input5_27' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );

        // hide overlay duration / title / play icon
        add_settings_field( 'cws_ytp_slider_adv_settings5_31', 'Duration Overlay', array( $this, 'setting_input5_31' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_32', 'Play Icon Overlay', array( $this, 'setting_input5_32' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );
        add_settings_field( 'cws_ytp_slider_adv_settings5_33', 'Title Overlay', array( $this, 'setting_input5_33' ), 'cws_ytp_slider_adv', 'cws_ytp_slider_adv_settings' );


    }
    
	/**
	 * Draw the Section Header for the admin area - API Key Settings
	 *
	 * @since    1.0.0
	 */
    public function section_text() {
        echo 'Enter instructions on how to get Google API Key here';
    }


    /**
     * Draw the Section Header for the admin area - Social Media Settings
     *
     * @since    2.0.0
     */
    public function section_text2() {
        // Do a check to see if api key has been set, if not highlight this to user...
        $key= $this->getApiKey();
        if( empty( $key ) ){
            $url = admin_url() . 'admin.php?page=cws_ytp&tab=api_key';
            echo "<p>Looks like you need to set <a href='$url'>API KEY</a></p>";
        }
        
        echo 'Enter instructions social media';
    } 


    /**
     * Draw the Section Header for the admin area - Default Settings
     *
     * @since    2.0.0
     */
    public function section_text3() {
        // Do a check to see if api key has been set, if not highlight this to user...
        $key= $this->getApiKey();
        if( empty( $key ) ){
            $url = admin_url() . 'admin.php?page=cws_ytp&tab=api_key';
            echo "<p>Looks like you need to set <a href='$url'>API KEY</a></p>";
        }

        $scUrl = admin_url() . 'admin.php?page=cws_sc';
        echo 'All of these defaults can be overridden in the shortcodes by using options, see <a href="' . $scUrl . '">Shortcode Examples</a> page for more detail.';
    } 


    /**
     * Draw the Section Header for the admin area - Slider Options
     *
     * @since    2.0.0
     */
    public function section_text4() {
        // Do a check to see if api key has been set, if not highlight this to user...
        $key= $this->getApiKey();
        if( empty( $key ) ){
            $url = admin_url() . 'admin.php?page=cws_ytp&tab=api_key';
            echo "<p>Looks like you need to set <a href='$url'>API KEY</a></p>";
        }
        
        echo 'Enter Slider Settings to apply as defaults. You can override these on an individual shortcode basis';
    } 


    /**
     * Draw the Section Header for the admin area - Slider Advanced Options
     *
     * @since    1.0.0
     */
    public function section_text5() {
        // Do a check to see if api key has been set, if not highlight this to user...
        $key= $this->getApiKey();
        if( empty( $key ) ){
            $url = admin_url() . 'admin.php?page=cws_ytp&tab=api_key';
            echo "<p>Looks like you need to set <a href='$url'>API KEY</a></p>";
        }
        
        echo 'Enter Slider Advanced Settings to apply as defaults.';
    } 


    /*
    function section_main_text() {
        
    }

    //
    function section_reset_text() {
        
    }   

    function section() {

    } 
    */

    // Get api key
    private function getApiKey(){

        $cws_ytp_code = get_option('cws_ytp_code');
        $api_key = $cws_ytp_code['api_key'];

        return $api_key  ;
    }


    
	/**
	 * Get the Access Token stored in db.
	 *
	 * @since    1.0.0
	 */
    /* 
    public function getAccessToken() {
        $token = get_option( 'cws_ytp_access_token' );

        return $token;
    }
    */
    

    /** GOOD WORKFLOW OF STEPS https://www.domsammut.com/code/php-server-side-youtube-v3-oauth-api-video-upload-guide **/   
    /**
     * Get the Reset option stored in db.
     *
     * @since    2.0.0
     */
    /*
    public function deauthorizeGoogleAccount() {
        // get options from db

        if( get_option( 'cws_ytp_reset' ) ){
            return true;
        } 

        return false;
    }
    */


    /**
     * Get current url.
     *
     * @since    1.0.0
     */ 
    // WHERE IS THIS FUNCTION USED ????
    function getUrl() {
        $url  = ( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
        $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
        $url .= $_SERVER["REQUEST_URI"];
        return $url;
    }


	/**
	 * Display and fill the form field - API Key Settings
	 *
	 * @since    1.0.0
	 */    
    public function setting_input() {
		$code = get_option( 'cws_ytp_code' );
        $api_key = $code['api_key'];
        
        echo "<input id='api_key' name='cws_ytp_code[api_key]' type='text' value='$api_key' required>";
    }
    
    
    /**
     * Display and fill the form field - Instagram
     *
     * @since    1.0.0
     */    
    public function setting_input2() {
        $social = get_option( 'cws_ytp_social' );
        $instagram = isset($social['instagram'])? $social['instagram'] : '';
        echo "<input id='instagram' name='cws_ytp_social[instagram]' type='text' value='$instagram'><span class='help'><small>instagram.com/<strong>username</strong></small></span>";
    }


    /**
     * Display and fill the form field - Twitter
     *
     * @since    1.0.0
     */    
    public function setting_input2_1() {
        $social = get_option( 'cws_ytp_social' );
        $twitter = isset($social['twitter'])? $social['twitter'] : '';
        echo "<input id='twitter' name='cws_ytp_social[twitter]' type='text' value='$twitter'><span class='help'><small>twitter.com/<strong>username</strong></small></span>";
    }


    /**
     * Display and fill the form field - Facebook
     *
     * @since    1.0.0
     */    
    public function setting_input2_2() {
        $social = get_option( 'cws_ytp_social' );
        $facebook = isset($social['facebook'])? $social['facebook'] : '';
        echo "<input id='facebook' name='cws_ytp_social[facebook]' type='text' value='$facebook'><span class='help'><small>facebook.com/<strong>page-id</strong></small></span>";
    }


    /**
     * Display and fill the form field - Pinterest
     *
     * @since    1.0.0
     */    
    public function setting_input2_3() {
        $social = get_option( 'cws_ytp_social' );
        $pinterest = isset($social['pinterest'])? $social['pinterest'] : '';
        echo "<input id='pinterest' name='cws_ytp_social[pinterest]' type='text' value='$pinterest'><span class='help'><small>pinterest.com/<strong>page-id</strong></small></span>";
    }


    /**
     * Display and fill the form field - Tumblr
     *
     * @since    1.0.0
     */    
    public function setting_input2_4() {
        $social = get_option( 'cws_ytp_social' );
        $tumblr = isset($social['tumblr'])? $social['tumblr'] : '';
        echo "<input id='tumblr' name='cws_ytp_social[tumblr]' type='text' value='$tumblr'><span class='help'><small><strong>username</strong>.tumblr.com</small></span>";
    }


    /**
     * Display and fill the form field - Flickr
     *
     * @since    1.0.0
     */    
    public function setting_input2_5() {
        $social = get_option( 'cws_ytp_social' );
        $flickr = isset($social['flickr'])? $social['flickr'] : '';
        echo "<input id='flickr' name='cws_ytp_social[flickr]' type='text' value='$flickr'><span class='help'><small>flickr.com/photos/<strong>page-id</strong></small></span>";
    }


    /**
     * Display and fill the form field - Max Results
     *
     * @since    1.0.0
     */    
    public function setting_input3() {
        $defaults = get_option( 'cws_ytp_defaults' );
        $maxResults = $defaults['maxResults'];
        echo "<input id='maxResults' name='cws_ytp_defaults[maxResults]' type='text' value='$maxResults'>";
    }


    /**
     * Display and fill the form field.
     *
     * @since    1.0.0
     */    
    public function setting_input3_1() {

        // set some defaults...
        $checked = '';

        // get option 'show_layout' value from the database
        $defaults = get_option( 'cws_ytp_defaults' );
        $show_layout = $defaults['show_layout'];

        if($defaults['show_layout']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_layout' name='cws_ytp_defaults[show_layout]' type='checkbox' />";
    }


    /**
     * Display and fill the form field.
     *
     * @since    1.0.0
     */    
    public function setting_input3_2() {

        // set some defaults...
        $checked = '';

        // get option 'show_layout' value from the database
        $defaults = get_option( 'cws_ytp_defaults' );
        $show_social = $defaults['show_social'];

        // Disble if not Pro version
        if( !$this->isPro == 1) {
            $disabled = 'disabled';
            $strTitle = 'Available in Pro version';            
        }

        $disabled = isset($disabled)? $disabled : '';
        $strTitle = isset($strTitle)? $strTitle : '';

        if($defaults['show_social']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_social' title='$strTitle' name='cws_ytp_defaults[show_social]' $disabled type='checkbox' />";
    }


    /**
     * Display and fill the form field.
     *
     * @since    1.0.0
     */    
    public function setting_input3_3() {

        // set some defaults...
        $checked = '';

        // get option 'show_layout' value from the database
        $defaults = get_option( 'cws_ytp_defaults' );
        $show_likes = $defaults['show_likes'];

        if($defaults['show_likes']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_likes' name='cws_ytp_defaults[show_likes]' type='checkbox' />";
    }


    /**
     * Display and fill the form field.
     *
     * @since    1.0.0
     */    
    public function setting_input3_4() {

        // set some defaults...
        $checked = '';

        // get option 'show_layout' value from the database
        $defaults = get_option( 'cws_ytp_defaults' );
        $show_views = $defaults['show_views'];

        if($defaults['show_views']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_views' name='cws_ytp_defaults[show_views]' type='checkbox' />";
    }


    /**
     * Display and fill the form field.
     *
     * @since    1.0.0
     */    
    public function setting_input3_5() {

        // set some defaults...
        $checked = '';

        // get option 'show_layout' value from the database
        $defaults = get_option( 'cws_ytp_defaults' );
        $show_date = $defaults['show_date'];

        if($defaults['show_date']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_date' name='cws_ytp_defaults[show_date]' type='checkbox' />";
    }


    /**
     * Display and fill the form field.
     *
     * @since    1.0.0
     */    
    public function setting_input3_6() {

        // set some defaults...
        $checked = '';

        // get option 'show_layout' value from the database
        $defaults = get_option( 'cws_ytp_defaults' );
        $show_subscribe = $defaults['show_subscribe'];

        // Disble if not Pro version
        if( !$this->isPro == 1) {
            $disabled = 'disabled';
            $strTitle = 'Available in Pro version';
        }

        $disabled = isset($disabled)? $disabled : '';
        $strTitle = isset($strTitle)? $strTitle : '';

        if($defaults['show_subscribe']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_subscribe' title='$strTitle' name='cws_ytp_defaults[show_subscribe]' $disabled type='checkbox' />";
    }


    /**
     * Display and fill the form field.
     *
     * @since    1.0.0
     */    
    public function setting_input3_7() {

        // set some defaults...
        $checked = '';

        // get option 'show_layout' value from the database
        $defaults = get_option( 'cws_ytp_defaults' );
        $show_channel_desc = $defaults['show_channel_description'];

        if($defaults['show_channel_description']) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_channel_description' name='cws_ytp_defaults[show_channel_description]' type='checkbox' />";
    }    


    /**
     * Display and fill the form field.
     *
     * @since    1.0.0
     */    
    public function setting_input3_8() {

        // set some defaults...
        $checked = '';

        // get option 'default_view' value from the database
        $defaults = get_option( 'cws_ytp_defaults' );
        $default_view = $defaults['default_view'];

        //if($defaults['default_view']) { $checked = ' checked="checked" '; }
        //echo "<input ".$checked." id='show_channel_description' name='cws_ytp_defaults[show_channel_description]' type='checkbox' />";

        // DROP-DOWN-BOX - Name: plugin_options[dropdown1]
        $items = array("Grid", "List", "Carousel");
        echo "<select id='cws_ytp_default_view' name='cws_ytp_defaults[default_view]'>";
        foreach($items as $item) {

            // Disble if not Pro version
            if( !$this->isPro == 1) {
                $disabled = ($item == 'Carousel') ? 'disabled' : '';
            }

            $selected = ( $default_view == $item ) ? 'selected="selected"' : '';
            echo "<option value='$item' $selected $disabled>$item</option>";
        }
        echo "</select>";
    } 


    /**
     * Display and fill the form field. Show Channel Header
     *
     * @since    1.0.0
     */    
    public function setting_input3_9() {

        $checked = '';

        // get option 'show_layout' value from the database
        $defaults = get_option( 'cws_ytp_defaults' );
        $show_channel_header = $defaults['show_channel_header'];

        if( $defaults['show_channel_header'] ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='show_channel_header' name='cws_ytp_defaults[show_channel_header]' type='checkbox' />";
    }   


    /**
     * Display and fill the form field. Number of Slides to Show
     *
     * @since    1.0.0
     */    
    public function setting_input4() {
        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $slidesToShow = isset($defaultsSlider['slidesToShow'])? $defaultsSlider['slidesToShow']: '';

        echo "<input id='slidesToShow' name='cws_ytp_slider[slidesToShow]' type='text' value='$slidesToShow'>";
    }


    /**
     * Display and fill the form field. Number of Slides to Scroll
     *
     * @since    1.0.0
     */    
    public function setting_input4_1() {
        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $slidesToScroll = isset($defaultsSlider['slidesToScroll'])? $defaultsSlider['slidesToScroll'] : '';

        echo "<input id='slidesToScroll' name='cws_ytp_slider[slidesToScroll]' type='text' value='$slidesToScroll'>";
    }


    /**
     * Display and fill the form field. Show Arrows
     *
     * @since    1.0.0
     */    
    public function setting_input4_2() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $arrows = isset($defaultsSlider['arrows'])? $defaultsSlider['arrows'] : '';

        if( isset($defaultsSlider['arrows']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_arrows' name='cws_ytp_slider[arrows]' type='checkbox' />";
    }


    /**
     * Display and fill the form field. Show Dots
     *
     * @since    1.0.0
     */    
    public function setting_input4_3() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $dots = isset($defaultsSlider['dots'])? $defaultsSlider['dots'] : '';

        if( isset($defaultsSlider['dots']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_dots' name='cws_ytp_slider[dots]' type='checkbox' />";
    }


    /**
     * Display and fill the form field. Show Autoplay
     *
     * @since    1.0.0
     */    
    public function setting_input4_4() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $autoplay = isset($defaultsSlider['autoplay'])? $defaultsSlider['autoplay'] : '';

        if( isset($defaultsSlider['autoplay']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_autoplay' name='cws_ytp_slider[autoplay]' type='checkbox' />";
    }    


    /**
     * Display and fill the form field. Show Infinite
     *
     * @since    1.0.0
     */    
    public function setting_input4_5() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $infinite = isset($defaultsSlider['infinite'])? $defaultsSlider['infinite'] : '';

        if( isset($defaultsSlider['infinite']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_infinite' name='cws_ytp_slider[infinite]' type='checkbox' />";
    }  


    /**
     * Display and fill the form field. Autoplay Speed - autoplaySpeed
     *
     * @since    1.0.0
     */    
    public function setting_input4_6() {
        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $autoplaySpeed = isset($defaultsSlider['autoplaySpeed'])? $defaultsSlider['autoplaySpeed'] : '';

        echo "<input id='autoplaySpeed' name='cws_ytp_slider[autoplaySpeed]' type='text' value='$autoplaySpeed'>";
    }


    /**
     * Display and fill the form field. Autoplay Speed - Speed
     *
     * @since    1.0.0
     */    
    public function setting_input4_7() {
        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $speed = isset($defaultsSlider['speed'])? $defaultsSlider['speed'] : '';

        echo "<input id='speed' name='cws_ytp_slider[speed]' type='text' value='$speed'>";
    }    


    /**
     * Display and fill the form field. Show Duration Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input4_8() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $duration = isset($defaultsSlider['duration'])? $defaultsSlider['duration'] : '';

        if( isset($defaultsSlider['duration']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_duration' name='cws_ytp_slider[duration]' type='checkbox' />";
    } 


    /**
     * Display and fill the form field. Show Play Icon Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input4_9() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $playIcon = isset($defaultsSlider['playIcon'])? $defaultsSlider['playIcon'] : '';

        if( isset($defaultsSlider['playIcon']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_playIcon' name='cws_ytp_slider[playIcon]' type='checkbox' />";
    } 


    /**
     * Display and fill the form field. Show Title Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input4_10() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider' );
        $title = isset($defaultsSlider['title'])? $defaultsSlider['title'] : '';

        if( isset($defaultsSlider['title']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_title' name='cws_ytp_slider[title]' type='checkbox' />";
    } 

    /**
     * Display and fill the form field. Breakpoint
     *
     * @since    1.0.0
     */    
    public function setting_input5_0() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $breakpoint = isset($defaultsSlider['breakpoint_1'])? $defaultsSlider['breakpoint_1'] : '';

        echo "<input id='breakpoint' name='cws_ytp_slider_adv[breakpoint_1]' type='text' value='$breakpoint'>";
    }

//

    /**
     * Display and fill the form field. Number of Slides to Show
     *
     * @since    1.0.0
     */    
    public function setting_input5() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $slidesToShow = isset($defaultsSlider['slidesToShow_1'])? $defaultsSlider['slidesToShow_1'] : '';

        echo "<input id='slidesToShow' name='cws_ytp_slider_adv[slidesToShow_1]' type='text' value='$slidesToShow'>";
    }


    /**
     * Display and fill the form field. Number of Slides to Scroll
     *
     * @since    1.0.0
     */    
    public function setting_input5_1() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $slidesToScroll = isset($defaultsSlider['slidesToScroll_1'])? $defaultsSlider['slidesToScroll_1'] : '';

        echo "<input id='slidesToScroll' name='cws_ytp_slider_adv[slidesToScroll_1]' type='text' value='$slidesToScroll'>";
    }


    /**
     * Display and fill the form field. Show Arrows
     *
     * @since    1.0.0
     */    
    public function setting_input5_2() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $arrows = isset($defaultsSlider['arrows_1'])? $defaultsSlider['arrows_1'] : '';

        if( isset($defaultsSlider['arrows_1']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_arrows' name='cws_ytp_slider_adv[arrows_1]' type='checkbox' />";
    }


    /**
     * Display and fill the form field. Show Dots
     *
     * @since    1.0.0
     */    
    public function setting_input5_3() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $dots = isset($defaultsSlider['dots_1'])? $defaultsSlider['dots_1'] : '';

        if( isset($defaultsSlider['dots_1']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_dots' name='cws_ytp_slider_adv[dots_1]' type='checkbox' />";
    }


    /**
     * Display and fill the form field. Show Autoplay
     *
     * @since    1.0.0
     */    
    public function setting_input5_4() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $autoplay = isset($defaultsSlider['autoplay_1'])? $defaultsSlider['autoplay_1'] : '';

        if( isset($defaultsSlider['autoplay_1']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_autoplay' name='cws_ytp_slider_adv[autoplay_1]' type='checkbox' />";
    }    


    /**
     * Display and fill the form field. Show Infinite
     *
     * @since    1.0.0
     */    
    public function setting_input5_5() {
        
        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $infinite = isset($defaultsSlider['infinite_1'])? $defaultsSlider['infinite_1'] : '';

        if( isset($defaultsSlider['infinite_1']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_infinite' name='cws_ytp_slider_adv[infinite_1]' type='checkbox' />";
    }  


    /**
     * Display and fill the form field. Autoplay Speed - autoplaySpeed
     *
     * @since    1.0.0
     */    
    public function setting_input5_6() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $autoplaySpeed = isset($defaultsSlider['autoplaySpeed_1'])? $defaultsSlider['autoplaySpeed_1'] : '';

        echo "<input id='autoplaySpeed' name='cws_ytp_slider_adv[autoplaySpeed_1]' type='text' value='$autoplaySpeed'>";
    }


    /**
     * Display and fill the form field. Autoplay Speed - Speed
     *
     * @since    1.0.0
     */    
    public function setting_input5_7() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $speed = isset($defaultsSlider['speed_1'])? $defaultsSlider['speed_1'] : '';

        echo "<input id='speed' name='cws_ytp_slider_adv[speed_1]' type='text' value='$speed'>";
    }  


    /**
     * Display and fill the form field. Show Duration Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input5_8a() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $duration_1 = isset($defaultsSlider['duration_1'])? $defaultsSlider['duration_1'] : '';

        if( isset($defaultsSlider['duration_1']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_duration_1' name='cws_ytp_slider_adv[duration_1]' type='checkbox' />";
    } 


    /**
     * Display and fill the form field. Show Play Icon Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input5_9a() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $playIcon_1 = isset($defaultsSlider['playIcon_1'])? $defaultsSlider['playIcon_1'] : '';

        if( isset($defaultsSlider['playIcon_1']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_playIcon_1' name='cws_ytp_slider_adv[playIcon_1]' type='checkbox' />";
    } 


    /**
     * Display and fill the form field. Show Title Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input5_10a() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $title_1 = isset($defaultsSlider['title_1'])? $defaultsSlider['title_1'] : '';

        if( isset($defaultsSlider['title_1']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_title_1' name='cws_ytp_slider_adv[title_1]' type='checkbox' />";
    } 





    /**
     * Display and fill the form field. Autoplay Speed - Speed
     *
     * @since    1.0.0
     */    
    public function setting_input5_8() {

        echo "<hr>";
    }  

//


    // second set of breakpoint fields
    /**
     * Display and fill the form field. Breakpoint
     *
     * @since    1.0.0
     */    
    public function setting_input5_9() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $breakpoint = isset($defaultsSlider['breakpoint_2'])? $defaultsSlider['breakpoint_2'] : '';

        echo "<input id='breakpoint' name='cws_ytp_slider_adv[breakpoint_2]' type='text' value='$breakpoint'>";
    }

//

    /**
     * Display and fill the form field. Number of Slides to Show
     *
     * @since    1.0.0
     */    
    public function setting_input5_10() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $slidesToShow = $defaultsSlider['slidesToShow_2'];

        echo "<input id='slidesToShow' name='cws_ytp_slider_adv[slidesToShow_2]' type='text' value='$slidesToShow'>";
    }


    /**
     * Display and fill the form field. Number of Slides to Scroll
     *
     * @since    1.0.0
     */    
    public function setting_input5_11() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $slidesToScroll = $defaultsSlider['slidesToScroll_2'];

        echo "<input id='slidesToScroll' name='cws_ytp_slider_adv[slidesToScroll_2]' type='text' value='$slidesToScroll'>";
    }


    /**
     * Display and fill the form field. Show Arrows
     *
     * @since    1.0.0
     */    
    public function setting_input5_12() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $arrows = $defaultsSlider['arrows_2'];

        if( $defaultsSlider['arrows_2'] ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_arrows' name='cws_ytp_slider_adv[arrows_2]' type='checkbox' />";
    }


    /**
     * Display and fill the form field. Show Dots
     *
     * @since    1.0.0
     */    
    public function setting_input5_13() {
        
        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $dots = isset($defaultsSlider['dots_2'])? $defaultsSlider['dots_2'] : '';

        if( isset($defaultsSlider['dots_2']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_dots' name='cws_ytp_slider_adv[dots_2]' type='checkbox' />";
    }


    /**
     * Display and fill the form field. Show Autoplay
     *
     * @since    1.0.0
     */    
    public function setting_input5_14() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $autoplay = isset($defaultsSlider['autoplay_2'])? $defaultsSlider['autoplay_2'] : '';

        if( isset($defaultsSlider['autoplay_2']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_autoplay' name='cws_ytp_slider_adv[autoplay_2]' type='checkbox' />";
    }    


    /**
     * Display and fill the form field. Show Infinite
     *
     * @since    1.0.0
     */    
    public function setting_input5_15() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $infinite = isset($defaultsSlider['infinite_2'])? $defaultsSlider['infinite_2'] : '';

        if( isset($defaultsSlider['infinite_2']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_infinite' name='cws_ytp_slider_adv[infinite_2]' type='checkbox' />";
    }  


    /**
     * Display and fill the form field. Autoplay Speed - autoplaySpeed
     *
     * @since    1.0.0
     */    
    public function setting_input5_16() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $autoplaySpeed = isset($defaultsSlider['autoplaySpeed_2'])? $defaultsSlider['autoplaySpeed_2'] : '';

        echo "<input id='autoplaySpeed' name='cws_ytp_slider_adv[autoplaySpeed_2]' type='text' value='$autoplaySpeed'>";
    }


    /**
     * Display and fill the form field. Autoplay Speed - Speed
     *
     * @since    1.0.0
     */    
    public function setting_input5_17() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $speed = isset($defaultsSlider['speed_2'])? $defaultsSlider['speed_2'] : '';

        echo "<input id='speed' name='cws_ytp_slider_adv[speed_2]' type='text' value='$speed'>";
    }  

    /**
     * Display and fill the form field. Show Duration Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input5_28() {
        
        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $duration_2 = isset($defaultsSlider['duration_2'])? $defaultsSlider['duration_2'] : '';

        if( isset($defaultsSlider['duration_2']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_duration_2' name='cws_ytp_slider_adv[duration_2]' type='checkbox' />";
    } 


    /**
     * Display and fill the form field. Show Play Icon Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input5_29() {
        
        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $playIcon_2 = isset($defaultsSlider['playIcon_2'])? $defaultsSlider['playIcon_2'] : '';

        if( isset($defaultsSlider['playIcon_2']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_playIcon_2' name='cws_ytp_slider_adv[playIcon_2]' type='checkbox' />";
    } 


    /**
     * Display and fill the form field. Show Title Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input5_30() {
        
        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $title_2 = isset($defaultsSlider['title_2'])? $defaultsSlider['title_2'] : '';

        if( isset($defaultsSlider['title_2']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_title_2' name='cws_ytp_slider_adv[title_2]' type='checkbox' />";
    } 
    //


    // Third set of breakpoints


    /**
     * Display and fill the form field. Autoplay Speed - Speed
     *
     * @since    1.0.0
     */    
    public function setting_input5_18() {

        echo "<hr>";
    } 

    // second set of breakpoint fields
    /**
     * Display and fill the form field. Breakpoint
     *
     * @since    1.0.0
     */    
    public function setting_input5_19() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $breakpoint = isset($defaultsSlider['breakpoint_3'])? $defaultsSlider['breakpoint_3'] : '';

        echo "<input id='breakpoint' name='cws_ytp_slider_adv[breakpoint_3]' type='text' value='$breakpoint'>";
    }

//

    /**
     * Display and fill the form field. Number of Slides to Show
     *
     * @since    1.0.0
     */    
    public function setting_input5_20() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $slidesToShow = isset($defaultsSlider['slidesToShow_3'])? $defaultsSlider['slidesToShow_3'] : '';

        echo "<input id='slidesToShow' name='cws_ytp_slider_adv[slidesToShow_3]' type='text' value='$slidesToShow'>";
    }


    /**
     * Display and fill the form field. Number of Slides to Scroll
     *
     * @since    1.0.0
     */    
    public function setting_input5_21() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $slidesToScroll = isset($defaultsSlider['slidesToScroll_3'])? $defaultsSlider['slidesToScroll_3'] : '';

        echo "<input id='slidesToScroll' name='cws_ytp_slider_adv[slidesToScroll_3]' type='text' value='$slidesToScroll'>";
    }


    /**
     * Display and fill the form field. Show Arrows
     *
     * @since    1.0.0
     */    
    public function setting_input5_22() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $arrows = isset($defaultsSlider['arrows_3'])? $defaultsSlider['arrows_3'] : '';

        if( isset($defaultsSlider['arrows_3']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_arrows' name='cws_ytp_slider_adv[arrows_3]' type='checkbox' />";
    }


    /**
     * Display and fill the form field. Show Dots
     *
     * @since    1.0.0
     */    
    public function setting_input5_23() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $dots = isset($defaultsSlider['dots_3'])? $defaultsSlider['dots_3'] : '';

        if( isset($defaultsSlider['dots_3']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_dots' name='cws_ytp_slider_adv[dots_3]' type='checkbox' />";
    }


    /**
     * Display and fill the form field. Show Autoplay
     *
     * @since    1.0.0
     */    
    public function setting_input5_24() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $autoplay = isset($defaultsSlider['autoplay_3'])? $defaultsSlider['autoplay_3'] : '';

        if( isset($defaultsSlider['autoplay_3']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_autoplay' name='cws_ytp_slider_adv[autoplay_3]' type='checkbox' />";
    }    


    /**
     * Display and fill the form field. Show Infinite
     *
     * @since    1.0.0
     */    
    public function setting_input5_25() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $infinite = isset($defaultsSlider['infinite_3'])? $defaultsSlider['infinite_3'] : '';

        if( isset($defaultsSlider['infinite_3']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_infinite' name='cws_ytp_slider_adv[infinite_3]' type='checkbox' />";
    }  


    /**
     * Display and fill the form field. Autoplay Speed - autoplaySpeed
     *
     * @since    1.0.0
     */    
    public function setting_input5_26() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $autoplaySpeed = isset($defaultsSlider['autoplaySpeed_3'])? $defaultsSlider['autoplaySpeed_3'] : '';

        echo "<input id='autoplaySpeed' name='cws_ytp_slider_adv[autoplaySpeed_3]' type='text' value='$autoplaySpeed'>";
    }


    /**
     * Display and fill the form field. Autoplay Speed - Speed
     *
     * @since    1.0.0
     */    
    public function setting_input5_27() {
        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $speed = isset($defaultsSlider['speed_3'])? $defaultsSlider['speed_3'] : '';

        echo "<input id='speed' name='cws_ytp_slider_adv[speed_3]' type='text' value='$speed'>";
    }  


    /**
     * Display and fill the form field. Show Duration Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input5_31() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $duration_3 = isset($defaultsSlider['duration_3'])? $defaultsSlider['duration_3'] : '';

        if( isset($defaultsSlider['duration_3']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_duration_3' name='cws_ytp_slider_adv[duration_3]' type='checkbox' />";
    } 


    /**
     * Display and fill the form field. Show Play Icon Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input5_32() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $playIcon_3 = isset($defaultsSlider['playIcon_3'])? $defaultsSlider['playIcon_3'] : '';

        if( isset($defaultsSlider['playIcon_3']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_playIcon_3' name='cws_ytp_slider_adv[playIcon_3]' type='checkbox' />";
    } 


    /**
     * Display and fill the form field. Show Title Overlay
     *
     * @since    1.0.0
     */    
    public function setting_input5_33() {

        // set some defaults...
        $checked = '';

        $defaultsSlider = get_option( 'cws_ytp_slider_adv' );
        $title_3 = isset($defaultsSlider['title_3'])? $defaultsSlider['title_3'] : '';

        if( isset($defaultsSlider['title_3']) ) { $checked = ' checked="checked" '; }
        echo "<input ".$checked." id='cws_ytp_title_3' name='cws_ytp_slider_adv[title_3]' type='checkbox' />";
    } 


//    





	/**
	 * Validate user input (we want text only).
	 *
	 * @since    1.0.0
	 */        
    public function validate_options( $input ) {
        $valid['api_key'] = esc_attr ( $input['api_key'] );
        return $valid;
    }


    /**
     * Validate user input (we want text only).
     *
     * @since    1.0.0
     */        
     public function validate_options_defaults( $input ) {
        //$maxresults = empty(esc_attr($input['maxResults']))? '3' : $input['maxResults'];

        $maxresults = isset($input['maxResults'])? $input['maxResults'] : '3';

        if(empty($maxresults)){
            $maxresults = '3';
        }
        
        $valid['maxResults']                = $maxresults;
        $valid['show_layout']               = ( isset( $input['show_layout'] ) && true == $input['show_layout'] ? true : false );
        $valid['show_social']               = ( isset( $input['show_social'] ) && true == $input['show_social'] ? true : false );
        $valid['show_likes']                = ( isset( $input['show_likes'] ) && true == $input['show_likes'] ? true : false );
        $valid['show_views']                = ( isset( $input['show_views'] ) && true == $input['show_views'] ? true : false );
        $valid['show_date']                 = ( isset( $input['show_date'] ) && true == $input['show_date'] ? true : false );
        $valid['show_subscribe']            = ( isset( $input['show_subscribe'] ) && true == $input['show_subscribe'] ? true : false );
        $valid['show_channel_description']  = ( isset( $input['show_channel_description'] ) && true == $input['show_channel_description'] ? true : false );
        $valid['show_channel_header']       = ( isset( $input['show_channel_header'] ) && true == $input['show_channel_header'] ? true : false );
        $valid['default_view']              = esc_attr( $input['default_view'] );

        return $valid;
    }


    /**
     * Validate Social Media user input (we want text only).
     *
     * @since    1.0.0
     */        
     public function validate_options_social( $input ) {
        $valid['instagram'] = esc_attr( trim( $input['instagram'] ) );
        $valid['twitter']   = esc_attr( trim( $input['twitter'] ) );
        $valid['facebook']  = esc_attr( trim( $input['facebook'] ) );
        $valid['pinterest'] = esc_attr( trim( $input['pinterest'] ) );
        $valid['tumblr']    = esc_attr( trim( $input['tumblr'] ) );
        $valid['flickr']    = esc_attr( trim( $input['flickr'] ) );

        $valid = array_filter( $valid );

        return $valid;
    }   


    /**
     * Validate Slider Options user input 
     *
     * @since    1.0.0
     */        
     public function validate_options_slider( $input ) {

/*
var_dump($input);
echo '<pre>';
print_r($input);
echo '</pre>';
die();
*/
        $valid['slidesToShow']      = esc_attr( trim( $input['slidesToShow'] ) );
        $valid['slidesToScroll']    = esc_attr( trim( $input['slidesToScroll'] ) );
        // Checkboxes
        $valid['arrows']        = ( isset( $input['arrows'] ) && true == $input['arrows'] ? true : false );
        $valid['dots']          = ( isset( $input['dots'] ) && true == $input['dots'] ? true : false );
        $valid['autoplay']      = ( isset( $input['autoplay'] ) && true == $input['autoplay'] ? true : false );
        $valid['infinite']      = ( isset( $input['infinite'] ) && true == $input['infinite'] ? true : false );

        $autoplaySpeed = isset($input['autoplaySpeed'])? $input['autoplaySpeed'] : '3000';

        if(empty($autoplaySpeed)){
            $autoplaySpeed = '3000';
        }

        $valid['autoplaySpeed']    = $autoplaySpeed; //empty(esc_attr($input['autoplaySpeed']))? '3000' : $input['autoplaySpeed']; // if empty set to 3000

        $speed = isset($input['speed'])? $input['speed'] : '1000';

        if(empty($speed)){
            $speed = '1000';
        }

        $valid['speed']            = $speed;// empty(esc_attr($input['speed']))? '1000' : $input['speed']; // if empty set to 1000

        $valid['duration']      = ( isset( $input['duration'] ) && true == $input['duration'] ? true : false );
        $valid['playIcon']      = ( isset( $input['playIcon'] ) && true == $input['playIcon'] ? true : false );
        $valid['title']         = ( isset( $input['title'] ) && true == $input['title'] ? true : false );

        $valid = array_filter( $valid );

        return $valid;
    }  


    /**
     * Validate Advanced Slider Options user input 
     *
     * @since    1.0.0
     */        
     public function validate_options_slider_adv( $input ) {
/*
var_dump($input);
echo '<pre>';
print_r($input);
echo '</pre>';
die();
*/
        // Add advanced settings here...

        // First Breakpoint
        $valid['breakpoint_1']    = esc_attr( trim( $input['breakpoint_1'] ) );

        $valid['slidesToShow_1']      = esc_attr( trim( $input['slidesToShow_1'] ) );
        $valid['slidesToScroll_1']    = esc_attr( trim( $input['slidesToScroll_1'] ) );
        // Checkboxes
        $valid['arrows_1']        = ( isset( $input['arrows_1'] ) && true == $input['arrows_1'] ? true : false );
        $valid['dots_1']          = ( isset( $input['dots_1'] ) && true == $input['dots_1'] ? true : false );
        $valid['autoplay_1']      = ( isset( $input['autoplay_1'] ) && true == $input['autoplay_1'] ? true : false );
        $valid['infinite_1']      = ( isset( $input['infinite_1'] ) && true == $input['infinite_1'] ? true : false );

        $autoplaySpeed = isset($input['autoplaySpeed_1'])? $input['autoplaySpeed_1'] : '3000';

        if(empty($autoplaySpeed_1)){
            $autoplaySpeed_1 = '3000';
        }

        $valid['autoplaySpeed_1']    = $autoplaySpeed_1; // empty(esc_attr($input['autoplaySpeed_1']))? '3000' : $input['autoplaySpeed_1']; // if empty set to 3000

        $speed_1 = isset($input['speed_1'])? $input['speed_1'] : '1000';

        if(empty($speed_1)){
            $speed_1 = '1000';
        }

        $valid['speed_1']    = $speed_1; //empty(esc_attr($input['speed_1']))? '1000' : $input['speed_1']; // if empty set to 1000

        $valid['duration_1']      = ( isset( $input['duration_1'] ) && true == $input['duration_1'] ? true : false );
        $valid['playIcon_1']      = ( isset( $input['playIcon_1'] ) && true == $input['playIcon_1'] ? true : false );
        $valid['title_1']         = ( isset( $input['title_1'] ) && true == $input['title_1'] ? true : false );


        // Second Breakpoint
        $valid['breakpoint_2']    = esc_attr( trim( $input['breakpoint_2'] ) );

        $valid['slidesToShow_2']      = esc_attr( trim( $input['slidesToShow_2'] ) );
        $valid['slidesToScroll_2']    = esc_attr( trim( $input['slidesToScroll_2'] ) );
        // Checkboxes
        $valid['arrows_2']        = ( isset( $input['arrows_2'] ) && true == $input['arrows_2'] ? true : false );
        $valid['dots_2']          = ( isset( $input['dots_2'] ) && true == $input['dots_2'] ? true : false );
        $valid['autoplay_2']      = ( isset( $input['autoplay_2'] ) && true == $input['autoplay_2'] ? true : false );
        $valid['infinite_2']      = ( isset( $input['infinite_2'] ) && true == $input['infinite_2'] ? true : false );
        
        $autoplaySpeed_2 = isset($input['autoplaySpeed_2'])? $input['autoplaySpeed_2'] : '3000';

        if(empty($autoplaySpeed_2)){
            $autoplaySpeed_2 = '3000';
        }

       $valid['autoplaySpeed_2']   = $autoplaySpeed_2; //empty(esc_attr($input['autoplaySpeed_2']))? '3000' : $input['autoplaySpeed_2']; // if empty set to 3000
       
        $speed_2 = isset($input['speed_2'])? $input['speed_2'] : '1000';

        if(empty($speed_2)){
            $speed_2 = '1000';
        }

       $valid['speed_2']           = $speed_2; //empty(esc_attr($input['speed_2']))? '1000' : $input['speed_2']; // if empty set to 1000

       // $valid['autoplaySpeed_2'] = esc_attr( trim( $input['autoplaySpeed_2'] ) );
       // $valid['speed_2']         = esc_attr( trim( $input['speed_2'] ) );  

        $valid['duration_2']      = ( isset( $input['duration_2'] ) && true == $input['duration_2'] ? true : false );
        $valid['playIcon_2']      = ( isset( $input['playIcon_2'] ) && true == $input['playIcon_2'] ? true : false );
        $valid['title_2']         = ( isset( $input['title_2'] ) && true == $input['title_2'] ? true : false );

        // Third Breakpoint
        $valid['breakpoint_3']    = esc_attr( trim( $input['breakpoint_3'] ) );

        $valid['slidesToShow_3']      = esc_attr( trim( $input['slidesToShow_3'] ) );
        $valid['slidesToScroll_3']    = esc_attr( trim( $input['slidesToScroll_3'] ) );
        // Checkboxes
        $valid['arrows_3']        = ( isset( $input['arrows_3'] ) && true == $input['arrows_3'] ? true : false );
        $valid['dots_3']          = ( isset( $input['dots_3'] ) && true == $input['dots_3'] ? true : false );
        $valid['autoplay_3']      = ( isset( $input['autoplay_3'] ) && true == $input['autoplay_3'] ? true : false );
        $valid['infinite_3']      = ( isset( $input['infinite_3'] ) && true == $input['infinite_3'] ? true : false );

        $autoplaySpeed_3 = isset($input['autoplaySpeed_3'])? $input['autoplaySpeed_3'] : '3000';

        if(empty($autoplaySpeed_3)){
            $autoplaySpeed_3 = '3000';
       }

        
        $valid['autoplaySpeed_3']   = $autoplaySpeed_3; // empty(esc_attr($input['autoplaySpeed_3']))? '3000' : $input['autoplaySpeed_3']; // if empty set to 3000
        
        $speed_3 = isset($input['speed_3'])? $input['speed_3'] : '1000';

        if(empty($speed_3)){
            $speed_3 = '1000';
        }

        $valid['speed_3']           = $speed_3; //empty(esc_attr($input['speed_3']))? '1000' : $input['speed_3']; // if empty set to 1000

        //$valid['autoplaySpeed_3'] = esc_attr( trim( $input['autoplaySpeed_3'] ) );
        //$valid['speed_3']         = esc_attr( trim( $input['speed_3'] ) );  

        $valid['duration_3']      = ( isset( $input['duration_3'] ) && true == $input['duration_3'] ? true : false );
        $valid['playIcon_3']      = ( isset( $input['playIcon_3'] ) && true == $input['playIcon_3'] ? true : false );
        $valid['title_3']         = ( isset( $input['title_3'] ) && true == $input['title_3'] ? true : false );

        $valid = array_filter( $valid );

        return $valid;
    }  

    


    /**
     * Dispay upgrade notice
     *
     * @since    1.0.0
     */
    public function cws_ytp_admin_installed_notice( $userObj ) {
        // var_dump($userObj->ID);

        // check if already Pro
        if( !$this->isPro ) {

            // Check if user has dismissed notice previously
            if ( ! get_user_meta( $userObj->ID, 'cws_ytp_ignore_upgrade' ) ) {
                global $pagenow;
                // Only show upgrade notice if on this page
                if ( $pagenow == 'options-general.php' || $pagenow == 'admin.php' ) {
                ?>
                <div id="message" class="updated cws-ytp-message">
                    <div class="squeezer">
                        <h4><?php _e( '<strong>WP YouTube has been installed &#8211; Get the Pro version</strong>', 'cws_ytp_' ); ?></h4>
                        <h4><?php _e( '<strong>GET 10% OFF! &#8211; use discount code YTPDISC10 on checkout</strong>', 'cws_ytp_' ); ?></h4>
                        <p class="submit">
                            <a href="http://www.cheshirewebsolutions.com/" class="button-primary"><?php _e( 'Visit Site', 'cws_ytp_' ); ?></a>
                            <a href="?cws_ytp_ignore_upgrade=0" class="secondary-button">Hide Notice</a>
                        </p>
                    </div>
                </div>
                <?php
                }                
            } // end check if already dismissed

        } // end isPro check

        // Set installed option
        //update_option( 'cws_gpp_installed', 0);
    }

/*  
    // If installed display upgrade notice
    function cws_ytp_admin_notices_styles() {
    
        // Installed notices
        if ( get_option( 'cws_ytp_installed' ) == 1 ) {
            // error_log("****** ADDING ACTION ADMIN NOTICES ********");
            add_action( 'admin_notices', $this->cws_ytp_admin_installed_notice() );  
        }
    }
*/

    /**
     * Allow user to dismiss upgrade notice :)
     *
     * @since    1.0.0
     */
    public function cws_ytp_ignore_upgrade( $userObj2 ) {   

        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset( $_GET['cws_ytp_ignore_upgrade'] ) && '0' == $_GET['cws_ytp_ignore_upgrade'] ) {
            add_user_meta($userObj2->ID, 'cws_ytp_ignore_upgrade', 'true', true);

            // Redirect to plugin settings page
            wp_redirect( admin_url( 'admin.php?page=cws_ytp' ) );
        }
    }   


}


class CWS_YTP_PM_User extends WP_User {

    function getID() {
        return $this->ID;
    }

}


class CWS_YTP_PM {

  protected $user;

  function __construct ( CWS_YTP_PM_User $user = NULL) {
    if ( ! is_null( $user ) && $user->exists() ) $this->user = $user;
  }

  function getUser() {
    return $this->user;
  }

}