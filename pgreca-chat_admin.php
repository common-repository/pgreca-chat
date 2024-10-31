<?php
	if(!defined('ABSPATH')) exit;
	
	function pgrecachat_adminpage() {
		if(esc_attr(get_option('pgrecachat_settingadminads')) == 'hide') {} else {
			$ads = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<!-- PGreca Chat -->
				<ins class="adsbygoogle"
					 style="display:block"
					 data-ad-client="ca-pub-6181929637339131"
					 data-ad-slot="7759995137"
					 data-ad-format="auto"></ins>
				<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
				</script>';
		} 
		$cont = '<div class="wrap">
			'.$ads.'
			<div class="pgreca_chat-sez">
				<h1 class="pgreca_chat-sez_head">'.esc_html(get_admin_page_title()).' '.get_option('pgreca_chat_version').'</h1>
				<div class="pgreca_chat-sez_cont">
					<h4>'.__('Version', 'pgreca_chat').' - 0.7</h4>
					<ul>
						<li>'.__('Add status member (online/offline), when a member is offline can\'t send message but it can receive new messages)', 'pgreca_chat').'.</li>
						<li>'.__('Add sound notification for new message.  (in ver. 0.8 will add a option for disable sound)', 'pgreca_chat').'</li>
						<li>'.__('Now the chat scroll bottom when receive new messages', 'pgreca_chat').'.</li>
					</ul>
					<h4>'.__('Version', 'pgreca_chat').' - 0.6</h4>
					<ul>
						<li>'.__('Are you looking for ver. 0.5? It\' s was kidnaped by aliens. ', 'pgreca_chat').'</li>
						<li>'.__('Enable private chat for: None, Friends and All', 'pgreca_chat').'</li>
						<li>'.__('Add private chat for members and add a private chat button to profiles if Buddypress is active', 'pgreca_chat').'</li>
					</ul>
					<h4>'.__('Version', 'pgreca_chat').' - 0.4</h4>
					<ul>
						<li>'.__('Add panel emoticon', 'pgreca_chat').'</li>
						<li>'.__('Add player for Youtube Videos', 'pgreca_chat').'</li>
					</ul>
					<h4>'.__('Version', 'pgreca_chat').' -  0.3</h4>
					<ul>
						<li>'.__('Page of summary of the plugin with the changelog of the latest version and the futures updates.', 'pgreca_chat').'</li>
						<li>'.__('Moderation message (publish, bin, delete permanently)', 'pgreca_chat').'</li>
						<li>'.__('Codify ROT13 for the messages', 'pgreca_chat').'</li>
					</ul>
				</div>
			</div>
		</div>';
		echo $cont;
	}
	
	function pgreca_chat_admin_messages() {
		global $wpdb;
		
		if (!current_user_can('manage_options')) {
			return;
		}
		
		if(isset($_GET['post']) && isset($_GET['action'])) {
			if($_GET['action'] == "publish" || $_GET['action'] == "trash" || $_GET['action'] == "delete") {
				$ifdelete = false;
				switch($_GET['action']) {
					case 'publish':
						$chat_status = 0;
					break;
					case 'trash':
						$chat_status = 1;
					break;	
					case 'delete':
						$chat_status = 2;
					break;	
				}
				if($chat_status == 0 || $chat_status == 1) {
					$wpdb->update(
						$wpdb->prefix."pgreca_chat",
						array(
							'chat_status' => $chat_status
						),
						array(
							'chat_id' => $_GET['post']
						)
					);
				} elseif($chat_status == 2) {
					$wpdb->delete(
						$wpdb->prefix."pgreca_chat",
						array(
							'chat_id' => $_GET['post']
						),
						array('%d')
					);
				}
			}
		}
		
		$chat_message = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."pgreca_chat WHERE chat_member = '0' ORDER BY chat_date DESC", ''));
		$tot_message = count($chat_message);
		//if($tot_message > 1) $element_message = __('elements', 'pgreca_chat'); else $element_message = __('element', 'pgreca_chat');
				
		$cont = '<div class="wrap">
			<h1>'.esc_html(get_admin_page_title()).'</h1>
			<table class="wp-list-table widefat striped plugin">
				<thead>
					<tr>
						<td scope="row" class="manage-column column-cb check-column"></td>
						<th scope="col" id="title" class="manage-column column-title column-primary">'.__('Message', 'pgreca_chat').'</th>
						<th scope="col" id="author" class="manage-column column-author">'.__('Author', 'pgreca_chat').'</th>
						<th scope="col" id="date" class="manage-column column-date" style="width:15%">'.__('Date', 'pgreca_chat').'</th>
						<td scope="row" class="manage-column column-cb check-column"></td>
					</tr>
				</thead>
				<tbody id="the-pgreca_chat_message-list" data-wp-lists="list:pgreca_chat_message">
		';
		if(count($chat_message) == 0) {
			$cont .= '<tr>
				<th colspan="5">
					Nessun messaggio
				</th>
			</tr>';		
		}
		foreach($chat_message as $message) {
			$user = get_userdata($message->user_id);
			$link_post_admin = admin_url('admin.php').'?page=pgreca_chat-messages&post='.$message->chat_id.'&action=';
			switch($message->chat_status) {
				case '0':
					$message_status = 'publish';
					$link_action = '<span class="trash"><a href="'.$link_post_admin.'trash" class="submitdelete" aria-label="'.__('Bin', 'pgreca_chat').'">'.__('Bin', 'pgreca_chat').'</a></span>';
				break;
				case '1':
					$message_status = 'delete';
					$link_action = '<span class="publish"><a href="'.$link_post_admin.'publish" aria-label="'.__('Restore', 'pgreca_chat').'">'.__('Restore', 'pgreca_chat').'</a></span> | ';
					$link_action .= '<span class="delete"><a href="'.$link_post_admin.'delete" aria-label="'.__('Delete Permanently', 'pgreca_chat').'">'.__('Delete Permanently', 'pgreca_chat').'</a></span>';
				break;
				case '2':
					$message_status = 'moderate';
				break;
			}
			$message_text = esc_html(convert_smilies(str_rot13($message->chat_text)));
			$yt = pgreca_chat_youtube($message_text);
			if(esc_attr(get_option('pgrecachat_settingurllink') == 'transform'))  $message_text = pgreca_chat_link($message_text);
			$cont .= '<tr>
				<th scope="row" class="manage-column column-cb check-column"></th>
				<th class="manage-column column-title column-primary">
					'.$message_text.$yt.'
					<div class="row-actions">
						'.$link_action.'
					</div>
				</th>
				<th scope="col" class="manage-column column-author">
					'.$user->display_name.'
				</th>
				<th class="manage-column column-date">
					'.date_i18n('d F Y \<\b\r\> H:i:s', $message->chat_date).'
				</th>
				<th scope="row" class="manage-column column-cb check-column">
					<span class="chat_status chat_'.$message_status.'" title="'.__('Publish', 'pgreca_chat').'"></span>
				</th>
			</tr>';
		}
		$cont .= '					
				</tbody>
				<tfoot>
					<tr>
						<td scope="row" class="manage-column column-cb check-column"></td>
						<th scope="col" id="title" class="manage-column column-title column-primary">'.__('Message', 'pgreca_chat').'</th>
						<th scope="col" id="author" class="manage-column column-author">'.__('Author', 'pgreca_chat').'</th>
						<th scope="col" id="date" class="manage-column column-date" style="width:15%">'.__('Date', 'pgreca_chat').'</th>
						<td scope="row" class="manage-column column-cb check-column"></td>
					</tr>
				</tfoot>
			</table>
		</div>';
	
		echo $cont;
		wp_die();
	}
?>
