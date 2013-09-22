// Synved WP Options
// Custom jquery code
// Version: 1.0
//
// Copyright (c) 2011 Synved Ltd.
// All rights reserved

var synvedOptionmediaUploadInput = null;

var SynvedOption = {
	
	performRequest: function (action, params) 
	{
		if (params == undefined || params == null) 
		{
			params = {}
		}
		
		jQuery.ajax(
			SynvedOptionVars.ajaxurl,
			{
				type : 'POST',
				data : {
					action : 'synved_option',
					synvedSecurity : SynvedOptionVars.synvedSecurity,
					synvedAction : action,
					synvedParams : params
				},
				success : function( response ) {
					SynvedOption.actionStarted(action, params, response, this);
				},
				error : function( jqXHR, textStatus, errorThrown ) {
					SynvedOption.actionFailed(action, params, errorThrown, this);
				}
			}
		);
	},
	
	actionStarted: function (action, params, response, request) 
	{
		
	},
	
	actionFailed: function (action, params, error, request) 
	{
		
	},
	
	handleOverlay: function (markup)
	{
	
	}
};

jQuery(document).ready(function() {

	jQuery('.synved-option-upload-button').click(function() {
	 var formfield = jQuery(this).prevAll('input[type="text"]');
	 var type = jQuery(this).prevAll('input[type="hidden"]').attr('value');
	 synvedOptionmediaUploadInput = formfield;
	 tb_show('', 'media-upload.php?type=' + type + '&amp;TB_iframe=true');
	 return false;
	});
	
	var oldSendToEditor = null;
	
	if (window.send_to_editor)
	{
		oldSendToEditor = window.send_to_editor;
	}
	
	window.send_to_editor = function(html) {
		if (oldSendToEditor != null)
		{
			oldSendToEditor(html);
		}
		
	 imgurl = jQuery('img',html).attr('src');
	 jQuery(synvedOptionmediaUploadInput).val(imgurl);
	 tb_remove();
	}
	
  jQuery('.synved-option-color-input-picker').each(function () {
  	var it = jQuery(this);
  	var input = it.prev('input.color-input');
	  it.farbtastic(input);
	  
	  it.stop().css({opacity: 0, display: 'none'});
	  
	  input.focus(function (){
	  	jQuery(it).stop().css({display: 'block'}).animate({opacity: 1});
	  })
	  .blur(function () {
	  	jQuery(it).stop().animate({opacity: 0}).css({display: 'none'});
	  });
  });
	
	jQuery('.synved-option-tag-selector').suggest(ajaxurl + '?action=ajax-tag-search&tax=post_tag', {multiple: true, multipleSep: ','});
	
	jQuery('.synved-option-reset-button').click(function (e) {
		var jthis = jQuery(this);
		var input = jthis.parentsUntil('tr').find('input, textarea');
		
		if (input.size() > 0)
		{
			var placeholder = input.attr('placeholder');
			
			if (placeholder != null)
			{
				input.val(placeholder);
			}
		}
		
		e.preventDefault();
		
		return false;
	});
});

