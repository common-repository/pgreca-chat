<?php
	add_filter('bp_before_member_header_meta', 'pgreca_chat_ifbuddypress' );
	function pgreca_chat_ifbuddypress() {
		if(is_plugin_active("buddypress/bp-loader.php") && bp_is_active('friends') || esc_attr(get_option('pgrecachat_settingbuddypressprivacychat')) == 'all') {					
			if(esc_attr(get_option('pgrecachat_settingbuddypressfriendchat')) == 'show') {
				if(get_current_user_id() != bp_displayed_user_id() && get_current_user_id() != "0") {
					if((friends_check_friendship_status(get_current_user_id(),bp_displayed_user_id()) == 'is_friend' && esc_attr(get_option('pgrecachat_settingbuddypressprivacychat')) == 'friends') || esc_attr(get_option('pgrecachat_settingbuddypressprivacychat')) == 'all') {
						$button = '<button class="pgreca_chat_newchat" data-chatmember="'.bp_displayed_user_id().'" />Chat</button>';
						$cont .= $button;
					}
				} else {
					$cont = '';
				}
				echo $cont;
			}
		}
	}
?>