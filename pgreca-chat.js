(function($) {
	$(document).ready(function() {
		getChatText();
		setInterval(function() { 
			if(!$("#pgreca_chat-hidden").hasClass("send_block")) {
				getChatText(); 
			}
			memberonline();
			newmessage();
		}, 2000);	
		$("#pgreca_chat").tooltip();		
	});
	
	$(document).on('click', ".pgreca_chat-head", function(e) {
		if($(this).parent().hasClass("close")) {
			$(this).parent().removeClass("close");
			jQuery.cookie('pgreca_chat', '0');
		} else {
			$(this).parent().addClass("close");
		$("#pgreca_chat_widget_emoticon_panel").toggle();
			jQuery.cookie('pgreca_chat', '1');
		}
	});
	
	$(document).on('click', ".pgreca_chat-head span.pgreca_chat-remove", function(e) {
		$(this).parent().parent().remove();		
	});
	
	$(document).on('click', ".pgreca_chat-widget_emoticon", function(e) {
		$(this).parent().find(".pgreca_chat_widget_emoticon_panel").toggle();
		$(this).parent().find(".pgreca_chat_widget_settings_panel").hide();
	});
	
	$(document).on('click', "#pgreca_chat-widget_settings", function(e) {
		$(this).parent().find(".pgreca_chat_widget_settings_panel").toggle();
		$(this).parent().find(".pgreca_chat_widget_emoticon_panel").hide();
	});
	
	$(document).on('click', "ul.pgreca_chat_widget_emoticon_panel li", function(e) {
		var emoticon = $(this).attr("data-emoticon");
		var chat = $(this).parent().parent().parent().attr("data-chat_member");
		conver_emoticon($(".pgreca_chat_chat[data-chat_member='"+chat+"'] .pgreca_chat-send"), emoticon);
			
	});
	
	$(document).on('keypress', ".pgreca_chat-send", function(e) {
		if (e.which == 13 && chatInput != "") {
			$('#pgreca_chat-hidden').addClass("send_block");
			var member = $(this).parent().parent().attr("data-chat_member");
			var chatInput = $(".pgreca_chat_chat[data-chat_member='"+member+"'] .pgreca_chat-send").val();
			$.ajax({
				url: pgrecachat_ajax.ajax_url,
				data: {		
					action: 'pgreca_chat_ajax_send',
					chat_member: member,
					message: chatInput
				},	
				type: 'post',
				success : function(text) {
					if(text == "ok") {
						$(".pgreca_chat_chat[data-chat_member='"+member+"'] .pgreca_chat-send").val("");	
					} else if(text == "spam") {
						alert("STOP SPAM - WAIT 10 SECOND");
					}
				}
			});
		}
		$('#pgreca_chat-hidden').removeClass("send_block");
	});
	
	$(document).on('click', ".pgreca_chat_newchat", function(e) {
		var chat_member = $(this).attr("data-chatmember");
		if($(".pgreca_chat_chat[data-chat_member='"+chat_member+"']").length < 1) {
			$.ajax({
				url: pgrecachat_ajax.ajax_url,
				data: {
					action: 'pgreca_chat_ajax_chat_new',
					chat_member: chat_member,
				},
				type: 'post',
				success: function(text) {
					$("#pgreca_chat_box").prepend(text);
					if(!$("#pgreca_chat-hidden").hasClass("send_block")) {
						getChatText(); 
					}
				}
			});
		}
	});
	
	$(document).on('click', "#pgreca_chat_user_setting", function(e) {
		var pgreca_chat_user_status = $("#pgreca_chat ul.pgreca_chat_widget_settings_panel select.pgreca_chat_user_status").find("option:selected").val();
		var pgreca_chat_user_sound = $("#pgreca_chat ul.pgreca_chat_widget_settings_panel select.pgreca_chat_user_sound").find("option:selected").val();
		
		$.ajax({
			url: pgrecachat_ajax.ajax_url,
			data: {
				action: 'pgreca_chat_ajax_user_settings',
				user_status: pgreca_chat_user_status,
				user_sound: pgreca_chat_user_sound,
			},
			type: 'post',
			success: function(text) {
				memberonline();
				if(pgreca_chat_user_status == 'offline') {
					$(".pgreca_chat-send").prop("disabled", true);
					$(".pgreca_chat_chat .pgreca_chat-head #pgreca_chat_user_status").removeClass("user_online").addClass("user_offline");
				} else {
					$(".pgreca_chat-send").prop("disabled", false);
					$(".pgreca_chat_chat .pgreca_chat-head #pgreca_chat_user_status").removeClass("user_offline").addClass("user_online");
				}
			}		
		});
	});
	
	function newmessage() {
		$.ajax({
			url: pgrecachat_ajax.ajax_url,
			data: {
				action: 'pgreca_chat_ajax_newchat',
			},
			type: 'post',
			dataType: "json",
			success: function(text) {
				if($(".pgreca_chat_chat[data-chat_member='"+text.chat_member+"']").length < 1) {
					$("#pgreca_chat_box").prepend(text.cont);								
				}
			}
		});
	};
	
	function getChatText() {	
		var chats_member = $(".pgreca_chat_chat");
		$.each(chats_member, function(key, value) {
			var member = $(value).attr("data-chat_member");
			var obj = {};
			obj["last_message"+member] = $(".pgreca_chat_chat[data-chat_member='"+member+"']").find(".pgreca_chat-message ol li.chat_message:first-child").attr("data-message");
			$.ajax({
				url: pgrecachat_ajax.ajax_url,
				data: {
					action: 'pgreca_chat_ajax_message',
					chat_member: member,
					last: obj
				},
				type: 'post',
				dataType: "json",
				success: function(text){
					if(text.cont != "") {
						if($(".pgreca_chat_chat[data-chat_member='"+member+"']").find(".pgreca_chat-message ol li.chat_message").length && text.sound == 'enable') $("#buzzer").get(0).play();
						$(".pgreca_chat_chat[data-chat_member='"+member+"'] .pgreca_chat-message ol").prepend(text.cont); 
						$(".pgreca_chat_chat[data-chat_member='"+member+"'] .pgreca_chat-message ol").animate({
							scrollTop: $(".pgreca_chat_chat[data-chat_member='"+member+"'] .pgreca_chat-message ol")[0].scrollHeight
						}, 600);
					}
				}
			});	
		});		
	}
	
	function memberonline() {
		$.ajax({
			url: pgrecachat_ajax.ajax_url,
			data: {
				action: 'pgreca_chat_ajax_memberonline'
			},
			type: 'post',
			success: function(text) {
				if(text != $('#pgreca_chat-memberonline').html()) $('#pgreca_chat-memberonline').html(text);
			}
		});
	}

	function conver_emoticon(textArea, emoticon) {
		var $txt = $(textArea);
        var caretPos = $txt[0].selectionStart;
        var textAreaTxt = $txt.val();
        var txtToAdd = ' '+emoticon+' ';
        $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
		$(textArea).focus();
	}
})(jQuery);
