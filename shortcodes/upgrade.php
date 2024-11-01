<div style="text-align:center;">Sorry, this is only available in WP YouTube Pro</div>
<?php
$plugin = new CWS_YouTube_Pro();
$plugin_admin = new CWS_YouTube_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );
echo $plugin_admin->cws_ytp_upgrade_content(); 