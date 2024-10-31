<?php
	if (!defined('WP_UNINSTALL_PLUGIN')) {
		die;
	}
	
	$option_name = 'pgrecachat_settinggroup';
	 
	delete_option($option_name);
	
	delete_site_option($option_name);
	
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pgreca_chat");
?>