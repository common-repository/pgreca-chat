<?php
	if(!defined('ABSPATH')) exit;
	
	function pgrecachat_setting() {
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingbackground', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingtext', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingurllink', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingtextlink', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingtextme', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingbackgroundmessage', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingbackgroundmessageme', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingbackgroundsend', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingadminads', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingbuddypressfriendchat', '');
		register_setting('pgrecachat_settinggroup', 'pgrecachat_settingbuddypressprivacychat', '');
	}

	function pgreca_chat_admin_settings() {
?>
	<div class="wrap">
		<h1>PGreca Chat &bull; <?php echo __('Settings', 'pgreca_chat'); ?></h1>
<?php
		if(isset($_GET['settings-updated'])){
			echo '<div id="moderated" class="updated notice is-dismissible">
					<p>
						'.__('Settings updated successfully', 'pgreca_chat').'
					</p>
					<button type="button" class="notice-dismiss">
						<span class="screen-reader-text">
							'.__('Hide this notification', 'pgreca_chat').'
						</span>
					</button>
				</div>';
		}
?>
		<form method="post" action="options.php">
			<?php settings_fields('pgrecachat_settinggroup'); ?>
			<?php do_settings_sections('pgrecachat_settinggroup'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row" style="width:30%;"><?php echo __('Head - Color Text', 'pgreca_chat'); ?></th>
					<td><input type="color" name="pgrecachat_settingbackground" value="<?php echo esc_attr(get_option('pgrecachat_settingbackground')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Body - Color Text Message', 'pgreca_chat'); ?></th>
					<td><input type="color" name="pgrecachat_settingtext" value="<?php echo esc_attr(get_option('pgrecachat_settingtext')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Transform URL in Link', 'pgreca_chat'); ?></th>
					<td><input type="checkbox" name="pgrecachat_settingurllink" value="transform" <?php checked(esc_attr(get_option('pgrecachat_settingurllink')), 'transform');?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Body - Color Link Message', 'pgreca_chat'); ?></th>
					<td><input type="color" name="pgrecachat_settingtextlink" value="<?php echo esc_attr(get_option('pgrecachat_settingtextlink')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Body - Color Text Message Personal', 'pgreca_chat'); ?></th>
					<td><input type="color" name="pgrecachat_settingtextme" value="<?php echo esc_attr(get_option('pgrecachat_settingtextme')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Body - Color Background Message', 'pgreca_chat'); ?></th>
					<td><input type="color" name="pgrecachat_settingbackgroundmessage" value="<?php echo esc_attr(get_option('pgrecachat_settingbackgroundmessage')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Body - Color Background Message Personal', 'pgreca_chat'); ?></th>
					<td><input type="color" name="pgrecachat_settingbackgroundmessageme" value="<?php echo esc_attr(get_option('pgrecachat_settingbackgroundmessageme')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Body - Color Background Send Message', 'pgreca_chat'); ?></th>
					<td><input type="color" name="pgrecachat_settingbackgroundsend" value="<?php echo esc_attr(get_option('pgrecachat_settingbackgroundsend')); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Hide ads on Admin Page', 'pgreca_chat'); ?></th>
					<td>
						<input type="checkbox" name="pgrecachat_settingadminads" value="hide" <?php checked(esc_attr(get_option('pgrecachat_settingadminads')), 'hide');?> />
						<p class="description"><?php echo __('You aren\' t obligated but I would be grateful if you not active it', 'pgreca_chat'); ?></p>
					</td>
				</tr>
<?php
	if(is_plugin_active("buddypress/bp-loader.php") && bp_is_active('friends')) {
?>
				<tr>
					<th colspan="2">
						<h2>Buddypress</h2>
					</th>
				</tr>
				<tr>
					<th scope="row">
						<?php echo __('Show the private chat button in the profiles', 'pgreca_chat') ;?>
					</th>
					<td>
						<input type="checkbox" name="pgrecachat_settingbuddypressfriendchat" value="show" <?php checked(esc_attr(get_option('pgrecachat_settingbuddypressfriendchat')), 'show');?>/>
					</td>
				</tr>
<?php
	}
?>
				<tr>
					<th scope="row">
			<?php echo __('Enable private chat for', 'pgreca_chat') ;?>: 
					</th>
					<td>
						<select name="pgrecachat_settingbuddypressprivacychat">
							<option <?php if(esc_attr(get_option('pgrecachat_settingbuddypressprivacychat')) == "nothing") echo 'selected="selected"'; ?> value="nothing"><?php echo __('None', 'pgreca_chat') ;?></option>
<?php
	if(is_plugin_active("buddypress/bp-loader.php") && bp_is_active('friends')) {
?>
							<option <?php if(esc_attr(get_option('pgrecachat_settingbuddypressprivacychat')) == "friends") echo 'selected="selected"'; ?> value="friends"><?php echo __('Friends', 'pgreca_chat') ;?></option>
<?php
	}
?>
							<option <?php if(esc_attr(get_option('pgrecachat_settingbuddypressprivacychat')) == "all") echo 'selected="selected"'; ?> value="all"><?php echo __('All', 'pgreca_chat') ;?></option>
						</select>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
<?php } ?>
