<?php
	if(!defined('ABSPATH')) exit;

	function pgreca_chat_output() {
		if(!isset($_COOKIE['pgreca_chat']) || $_COOKIE['pgreca_chat'] != 1) $stato = ""; else $stato = ' close';
		$out = '<div id="pgreca_chat_box">';
		$out .= '<div id="pgreca_chat" class="pgreca_chat_chat'.$stato.'" data-chat_member="0">';
		$out .= '<div class="pgreca_chat-head" style="background-color:'.esc_attr(get_option('pgrecachat_settingbackground')).'">';	
		if(is_user_logged_in()) $out .= '<div id="pgreca_chat_user_status" class="user_'.get_user_meta(get_current_user_id(), 'pgreca_chat_user_status', true).'"></div>';
		$out .= '<span id="pgreca_chat-show">'.__('Open', 'pgreca_chat').'</span><span id="pgreca_chat-hide">'.__('Close', 'pgreca_chat').'</span>';
		$out .= '</div>';
		$out .= '<div id="pgreca_chat-widget_show">';
		$out .= '<div id="pgreca_chat-memberonline"></div>';
		$out .= '</div>';
		if(is_user_logged_in()) {
			$out .= '<div class="pgreca_chat-widget_gadget">';
			$out .= '<div class="pgreca_chat-widget_emoticon"></div>';
			$out .= '<div id="pgreca_chat-widget_settings"></div>';
			$out .= '<ul class="pgreca_chat_widget_emoticon_panel">';	
			$emoticons = array(':)', ':D', ':(', ':o', '8O', ':?', '8-)', ':x', ':P', ':|', ';)', ':lol:', ':oops:', ':cry:', ':evil:', ':twisted:', ':roll:', ':!:', ':?:', ':idea:', ':arrow:', ':mrgreen:');
			foreach($emoticons as $emoticon) {
				$out .= '<li data-emoticon="'.$emoticon.'">'.convert_smilies($emoticon).'</li>';
			}		
			$out .= '</ul>';
			$out .= '<ul class="pgreca_chat_widget_settings_panel">';	
			$out .= '<li>'.__('Status', 'pgreca_chat').': <select class="pgreca_chat_user_status" name="pgreca_chat_user_status">';
			if(get_user_meta(get_current_user_id(), 'pgreca_chat_user_status', true) == 'online') $sel = 'selected'; else $sel = '';
			$out .= '<option value="offline">Offline</option>';
			$out .= '<option value="online" '.$sel.'>Online</option>';
			$out .= '</select></li>';
			$out .= '<li>'. __('Notification sound', 'pgreca_chat').': <select class="pgreca_chat_user_sound" name="pgreca_chat_user_sound">';
			if(get_user_meta(get_current_user_id(), 'pgreca_chat_user_sound', true) == 'enable') $dis = 'selected'; else $dis = '';
			$out .= '<option value="disable">'.__('Disable', 'pgreca_chat').'</option>';
			$out .= '<option value="enable" '.$dis.'>'.__('Enable', 'pgreca_chat').'</option>';
			$out .= '</select></li>';
			$out .= '<li><input type="submit" id="pgreca_chat_user_setting" value="'.__('Update', 'pgreca_chat').'" /></li>';
			$out .= '</ul>';
			$out .= '</div>';
		}
		$out .= '<div class="pgreca_chat-message"><ol>';
		$out .= '</ol></div>';
		$out .= '<div class="pgreca_chat-sendbox" style="background-color:'.esc_attr(get_option('pgrecachat_settingbackgroundsend')).'">';
		if(is_user_logged_in()) {
			if(get_user_meta(get_current_user_id(), 'pgreca_chat_user_status', true) == 'offline') $disa = 'disabled'; else $disa = '';
			$out .= '<input type="text" class="pgreca_chat-send" placeholder="'.__('Write a message', 'pgreca_chat').'" maxlength="50" '.$disa.' />';
		} else {
			$out .= '<input type="text" id="pgreca_chat-senddisable" value="'.__('Login for write!', 'pgreca_chat').'" disabled />';
		}
		$out .= '<input type="hidden" id="pgreca_chat-hidden" name="pgreca_chat_hidden" />';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '</div>';
		return $out;
	}
	
	function pgreca_chat_ajax_message() {
		global $wpdb;		
		$array = array();
		if(is_user_logged_in()) {
			pgreca_chat_update_online();
			$sound = get_user_meta(get_current_user_id(), 'pgreca_chat_user_sound', true);
		}
		
		if($_POST['last'] != "") {
			foreach($_POST['last'] as $key=>$ultimo) {
				$a = str_replace("last_message", "", $key);
				if($a == $_POST['chat_member']) $las = $ultimo;		
			}
		}
		if($las == "") $last = 0; else $last = $las;
		if($_POST['chat_member'] == '0') {
			$where = "chat_member = '0'";
		} else {
			$where = "((chat_member = '".$_POST['chat_member']."' AND user_id = '".get_current_user_id()."') OR (chat_member = '".get_current_user_id()."' AND user_id = '".$_POST['chat_member']."'))";
		}					
		$cont .= $where;
		$query = "SELECT * FROM ".$wpdb->prefix."pgreca_chat WHERE chat_id > '".$last."' AND chat_id != '".$last."' AND chat_status = '0' AND ".$where." ORDER BY chat_id DESC LIMIT 0, 10";
		$chat_message = $wpdb->get_results($wpdb->prepare($query, ''));
					
		$cont = "";
		
		foreach($chat_message as $message) {
			$user = get_userdata($message->user_id);
			if(get_current_user_id()  == $message->user_id) {
				$message_align = ' message_me'; 
				$message_style = 'background-color: '.esc_attr(get_option('pgrecachat_settingbackgroundmessageme')).'; color: '.esc_attr(get_option('pgrecachat_settingtextme')); 
			} else {
				$message_align = ''; 
				$message_style = 'background-color: '.esc_attr(get_option('pgrecachat_settingbackgroundmessage')).'; color: '.esc_attr(get_option('pgrecachat_settingtext'));
			}		
			$message_text = esc_html(convert_smilies(str_rot13($message->chat_text)));
			$yt = pgreca_chat_youtube($message_text);
			if(esc_attr(get_option('pgrecachat_settingurllink') == 'transform'))  $message_text = pgreca_chat_link($message_text);
			$cont .= '<li class="chat_message'.$message_align.'" data-message="'.$message->chat_id.'">'.get_avatar($user->ID, 25, "", $user->display_name).'<div class="message_text" style="'.$message_style.'">'.$message_text.$yt.'</div>
			<audio id="buzzer" src="'.esc_attr(plugins_url("pgreca-chat_notification.mp3", __FILE__)).'" type="audio/ogg">'.__('Your browser does not support the audio element.', 'pgreca-chat').'</audio>
			</li>';				
		}		
		
		$array = array(
			"cont"	=> $cont,
			"sound"	=> $sound,
		);
		echo json_encode($array);
		wp_die();
	}
	
	function pgreca_chat_update_online() {
		update_user_meta(get_current_user_id(), 'pgreca_chat_user_last_activity', time());
		if(get_user_meta(get_current_user_id(), 'pgreca_chat_user_status', true) == '') update_user_meta(get_current_user_id(), 'pgreca_chat_user_status', 'online');
	}
	
	function pgreca_chat_ajax_send() {
		global $wpdb;
		$control = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."pgreca_chat WHERE (user_ID = %d AND chat_date > (".(time() - 10).")) LIMIT 0, 1", get_current_user_id()), ARRAY_A);
		if($control == null) {
			if($control['chat_id'] == "") {
				$message = str_rot13(wp_unslash(sanitize_textarea_field($_POST['message'])));
				$chat_member = sanitize_text_field($_POST['chat_member']);
				if($chat_member != 0) $read = 1; else $read = 0;
				$wpdb->insert(
					$wpdb->prefix."pgreca_chat",
					array(
						'chat_id'	=> '',
						'user_id'	=> get_current_user_id(),
						'chat_text'	=> $message,
						'chat_date'	=> time(),
						'chat_status'	=> 0,
						'chat_read'		=> $read,
						'chat_member'	=> $chat_member
					),
					array(
						'%d',
						'%d',
						'%s',
						'%d',
						'%d',
						'%d'
					)
				);
				echo "ok";
			} 
		} else {
			echo "spam";
		}
		wp_die();
	}
	
	function pgreca_chat_ajax_memberonline() {
		global $wpdb;
		$memberonlines = new WP_User_Query(array('meta_query' => array(
			'relation' => 'AND',
			array('key' => 'pgreca_chat_user_status', 'value' => 'online', 'compare' => '='),
			array('key' => 'pgreca_chat_user_last_activity', 'value' => (time() - 10), 'compare' => '>=')
		)));
		$memberonline = $memberonlines->get_results();
		$cont = "";
		if(empty($memberonline)) {
			if(get_user_meta(get_current_user_id(), 'pgreca_chat_user_status', true) == 'online' || get_current_user_id() == '') {
				$cont .= '<span>'.__('Nobody is online', 'pgreca_chat').'</span>';
			} elseif(get_user_meta(get_current_user_id(), 'pgreca_chat_user_status', true) == 'offline') {
				$cont .= '<span>'.__('You are offline', 'pgreca_chat').'</span>';
			}
		} else {
			if(get_user_meta(get_current_user_id(), 'pgreca_chat_user_status', true) == 'offline') {
				$cont .= '<span>'.__('You are offline', 'pgreca_chat').'</span>';
			} elseif(get_user_meta(get_current_user_id(), 'pgreca_chat_user_status', true) == 'online' || get_current_user_id() == '') {
				foreach($memberonline as $user) {
					$user = get_userdata($user->ID);
					if(get_current_user_id() != $user->ID) {
						if(esc_attr(get_option('pgrecachat_settingbuddypressprivacychat')) == 'all' || (esc_attr(get_option('pgrecachat_settingbuddypressprivacychat')) == 'friends' && (friends_check_friendship_status(get_current_user_id(),$user->ID) == 'is_friend'))) $new_chat = 'class="pgreca_chat_newchat" data-chatmember="'.$user->ID.'"'; else $new_chat = '';
					} else {
						$new_chat = '';
					}
					$cont .= '<a alt="'.$user->display_name.'" title="'.$user->display_name.'" '.$new_chat.'>'.get_avatar($user->ID, 25, "", $user->display_name).'</a>';
				}
			}
		}
		echo $cont;
		wp_die();
	}
	
	function pgreca_chat_ajax_chat_new() {
		$member = $_POST['chat_member'];
		echo pgreca_chat_chat($member);
	}
	
	function pgreca_chat_ajax_newchat() {
		global $wpdb;
		$array = array();
		$query = "SELECT * FROM ".$wpdb->prefix."pgreca_chat WHERE chat_read = '1' AND chat_member = '".get_current_user_id()."' GROUP BY user_id LIMIT 0,1";
		$newmessage = $wpdb->get_results($wpdb->prepare($query, ''));
		if($newmessage != null) {
			foreach($newmessage as $message) {
				if(get_current_user_id() == $message->chat_member) {
					$chat_member = $message->user_id;
					$cont = pgreca_chat_chat($message->user_id);
					$array = array(
						"cont"	=> $cont,
						"chat_member"	=> $chat_member,
					);
					$wpdb->update(
						$wpdb->prefix."pgreca_chat",
						array(
							'chat_read' => '0'
						),
						array(
							'chat_member' => $message->chat_member
						)
					);
				}
				break;
			}			
		}
		echo json_encode($array);		
		wp_die();
	}
	
	function pgreca_chat_chat($member) {
		$user = get_userdata($member);
		$cont = '<div class="pgreca_chat_member pgreca_chat_chat" data-chat_member="'.$member.'">';
		$cont .= '<div class="pgreca_chat-head" style="background-color:'.esc_attr(get_option('pgrecachat_settingbackground')).'"><span class="pgreca_chat-head_member">'.$user->display_name.'</span><span class="pgreca_chat-remove">x</span></div>';
		$cont .= '<div class="pgreca_chat-widget_gadget">';
		if(is_user_logged_in()) {
			$cont .= '<div class="pgreca_chat-widget_emoticon"></div>';
			$cont .= '<ul class="pgreca_chat_widget_emoticon_panel">';	
			$emoticons = array(':)', ':D', ':(', ':o', '8O', ':?', '8-)', ':x', ':P', ':|', ';)', ':lol:', ':oops:', ':cry:', ':evil:', ':twisted:', ':roll:', ':!:', ':?:', ':idea:', ':arrow:', ':mrgreen:');
			foreach($emoticons as $emoticon) {
				$cont .= '<li data-emoticon="'.$emoticon.'">'.convert_smilies($emoticon).'</li>';
			}			
			$cont .= '</ul>';
		}
		$cont .= '</div>';		
		$cont .= '<div class="pgreca_chat-message" style="height:250px"><ol></ol></div>';
		$cont .= '<div class="pgreca_chat-sendbox" style="background-color:'.esc_attr(get_option('pgrecachat_settingbackgroundsend')).'">';
		if(get_user_meta(get_current_user_id(), 'pgreca_chat_user_status', true) == 'offline') $disa = 'disabled'; else $disa = '';
		$cont .= '<input type="text" class="pgreca_chat-send" placeholder="'.__('Write a message', 'pgreca_chat').'" maxlength="50" '.$disa.'  />';
		$cont .= '<input type="hidden" id="pgreca_chat-hidden" name="pgreca_chat_hidden"/>';
		$cont .= '</div>';
		$cont .= '</div>';
		return $cont;
	}
	
	function pgreca_chat_ajax_user_settings() {
		update_user_meta(get_current_user_id(), 'pgreca_chat_user_status', $_POST['user_status']);	
		update_user_meta(get_current_user_id(), 'pgreca_chat_user_sound', $_POST['user_sound']);
	}
	
	function pgreca_chat_youtube($text) {
		if(strstr($text, 'youtube.com/watch?v=') !== false) {
			$domain = strstr($text, 'youtube.com/watch?v=');
			$domain = str_replace("youtube.com/watch?v=", "", $domain);
			$domain = explode('&', $domain);
			$youtube = '<br /><iframe src="https://www.youtube.com/embed/'.$domain[0].'" allowfullscreen></iframe>';
		}
		return $youtube;
	}
	
	function pgreca_chat_link($text) {
		$search = '/([(http|https|ftp)]+:\/\/[\w-?&:;#!~=\.\/\@]+[\w\/])/i';
		$replace = '<a href="$1" style="color:'.esc_attr(get_option('pgrecachat_settingtextlink')).'" target="_blank">$1</a>';
		return preg_replace($search, $replace, $text);
	}
?>
