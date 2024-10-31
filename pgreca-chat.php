<?php
/*
	Plugin Name: PGreca Chat
	Plugin URI:  http://yoome.altervista.org
	Description: Live Chat Plugin for Wordpress Websites. 100% FREE.
	Version: 0.8
	Author:      PGreca
	Author URI:  http://anpgreca.altervista.org
	License:     GPL2
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
	Text Domain: pgreca_chat
	Domain Path: /languages
*/	
	
	global $pgreca_chat_version;	
	$pgreca_chat_version = '0.8';	
	
	register_activation_hook(__FILE__, 'pgreca_chat_install');
	
	function pgreca_chat_install() {
		global $wpdb;
		global $pgreca_chat_version;
		$table_name = $wpdb->prefix.'pgreca_chat';

		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name(
			chat_id int(11) NOT NULL AUTO_INCREMENT,
			user_id int(11) NOT NULL,
			chat_text text NOT NULL,
			chat_date int(11) NOT NULL, 
			chat_member int(11) NOT NULL,
			chat_status int(1) DEFAULT '0' NOT NULL,
			chat_read int(1) DEFAULT '0' NOT NULL,
			PRIMARY KEY(chat_id)
		) $charset_collate;";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		add_option('pgreca_chat_version', $pgreca_chat_version);	
	}
	
	add_action('plugins_loaded', 'pgreca_chat_update');
	function pgreca_chat_update() {
		global $wpdb;
		global $pgreca_chat_version;
		
		if(get_option('pgreca_chat_version') != $pgreca_chat_version) update_option('pgreca_chat_version', $pgreca_chat_version);
		if(get_option('pgreca_chat_version') == '0.6') {
			$table_name = $wpdb->prefix . 'pgreca_chat';
			$wpdb->query("ALTER TABLE $table_name ADD chat_member int(11) NOT NULL, ADD chat_read int(1) DEFAULT '0' NOT NULL");
			$wpdb->query("ALTER TABLE $table_name CHANGE `chat_date` `chat_date` INT(11) NOT NULL");
		}
	}

	require_once('pgreca-chat_functions.php');
	require_once('pgreca-chat_settings.php');
	require_once('pgreca-chat_admin.php');
	require_once('pgreca-chat_integrate.php');
		
	add_action('plugins_loaded', 'pgreca_chat_ini');
	function pgreca_chat_ini() {	
		load_plugin_textdomain('pgreca_chat', false, dirname(plugin_basename(__FILE__)).'/languages');
	}			
	
	add_action('init', 'pgreca_style');
	function pgreca_style() {	
		if(!is_admin()){
			wp_register_style('pgreca-chat_style', plugins_url('pgreca-chat.css' , __FILE__), $deps = array(), '0.7', $media = 'all');
			wp_enqueue_style('pgreca-chat_style');
			wp_enqueue_script('jquery-ui-tooltip');
			wp_register_script('pgreca-chat-script', plugins_url('pgreca-chat.js', __FILE__), array('jquery'));
			wp_enqueue_script('pgreca-chat-script');
			wp_localize_script( 'pgreca-chat-script', 'pgrecachat_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		} else {
			wp_register_style('pgreca-chat_style', plugins_url('pgreca-chat_admin.css' , __FILE__), $deps = array(), '0.7', $media = 'all');
			wp_enqueue_style('pgreca-chat_style');
		}
	}   
	
	add_action('admin_menu', 'pgrecachat_settingmenu');
	function pgrecachat_settingmenu() {
		add_menu_page('PGreca Chat', 'PGreca Chat', 'administrator', 'pgreca_chat', 'pgrecachat_adminpage', plugins_url('/images/icon.png', __FILE__));
		add_submenu_page('pgreca_chat', 'PGreca Chat &bull; '.__('Messages', 'pgreca_chat'), __('Messages', 'pgreca_chat'), 'manage_options', '/pgreca_chat-messages', 'pgreca_chat_admin_messages');
		add_submenu_page('pgreca_chat', 'PGreca Chat &bull; '.__('Settings', 'pgreca_chat'), __('Settings', 'pgreca_chat'), 'manage_options', '/pgreca_chat-settings', 'pgreca_chat_admin_settings');
		add_action('admin_init', 'pgrecachat_setting');
	}
	
	add_action('wp_ajax_pgreca_chat_ajax_message', 'pgreca_chat_ajax_message');
	add_action('wp_ajax_nopriv_pgreca_chat_ajax_message', 'pgreca_chat_ajax_message');
	
	add_action('wp_ajax_pgreca_chat_ajax_memberonline', 'pgreca_chat_ajax_memberonline');
	add_action('wp_ajax_nopriv_pgreca_chat_ajax_memberonline', 'pgreca_chat_ajax_memberonline');
	
	
	add_action('wp_ajax_pgreca_chat_ajax_chat_new', 'pgreca_chat_ajax_chat_new');
	add_action('wp_ajax_pgreca_chat_ajax_newchat', 'pgreca_chat_ajax_newchat');
	
	add_action('wp_ajax_pgreca_chat_ajax_send', 'pgreca_chat_ajax_send');
	add_action('wp_ajax_pgreca_chat_ajax_user_settings', 'pgreca_chat_ajax_user_settings');
		
	add_action('wp_footer', 'pgreca_chat_footer');
	function pgreca_chat_footer() {
		echo pgreca_chat_output();
	}
?>