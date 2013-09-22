// Synved WP Options - Addon Type
// Custom jquery code
// Version: 1.0
//
// Copyright (c) 2011 Synved Ltd.
// All rights reserved

jQuery(document).ready(function() {

	jQuery('.synved-option-overlay-button').click(function() {
		tb_show(this.value, SynvedOptionVars.ajaxurl);
		var tb = jQuery("#TB_window");

		if (tb)
		{
			var tbCont = tb.find('#TB_ajaxContent');
			tbCont.css({ width : tbCont.parent().width(), height : '100%' });

			if (!tbCont /*|| !tbCont.is(":visible")*/)
			{
				//tbCont = tb.find('#TB_iframeContent');
			}
			
			var jthis = jQuery(this);
	 		var info = jthis.prevAll('.synved-option-item-info:first').html();
			 		
			var infoObj = null;
			
			try	
			{
				infoObj = jQuery.parseJSON(info);
			}
			catch (ex) 
			{
				infoObj = jQuery.parseJSON(jQuery('<div/>').html(info).text());
			}
			
	 		var markup = jthis.prevAll('.synved-option-overlay-markup');
	 		
	 		if (markup)
	 		{
	 			markup = markup.clone();
	 			var progressBar = markup.find('.overlay-progress');
	 			var containerId = markup.find('.overlay-container').attr('id');
	 			var buttonId = markup.find('.overlay-button').attr('id');
	 			var fileField = jthis.attr('name');
	 			
	 			var counter = 0;
	 			containerId += '_tb';
	 			buttonId += '_tb';
	 			
	 			while (jQuery('#' + containerId + counter.toString()).size() > 0)
	 			{
	 				counter += 1;
	 			}
	 			
	 			containerId += counter.toString();
	 			buttonId += counter.toString();
	 			
	 			markup.find('.overlay-container').attr('id', containerId);
	 			markup.find('.overlay-button').attr('id', buttonId);
	 			
	 			markup.append(jQuery('<div class="overlay-message overlay-status" />'));
	 			markup.append(jQuery('<button class="button-primary overlay-close">Close and continue</button>').hide().click(function () {
	 				tb_remove();
	 				window.location = window.location;
	 			}));
	 			
	 			tbCont.html(markup);
	 			markup.show();
	 			
//	 			markup.append(containerId);
//	 			markup.append('<br/>');
//	 			markup.append(buttonId);
//	 			markup.append('<br/>');
//	 			markup.append(fileField);
	 			
	 			progressBar.progressbar();
	 			
	 			var finishInstall = function (message, error) 
	 			{
	 				var status = markup.find('.overlay-status');
	 				
	 				if (status != null)
	 				{
	 					if (error != undefined && error == true) 
	 					{
	 						status.css('color', '#b06066');
	 					}
	 					else
	 					{
	 						status.css('color', '#66b060');
	 					}
	 					
	 					status.html(message);
	 				}
	 				
	 				markup.find('#' + buttonId).hide();
	 				markup.find('.overlay-close').css('display', 'inline');
	 			};
	 			
	 			var params = infoObj;
	 			params = JSON.stringify(params);
	 			
	 			var uploader = new plupload.Uploader({
		  		runtimes: 'html5,flash,silverlight,html4',   
    			autostart : true,
		  		browse_button: buttonId,
		  		container: containerId,
		  		//drop_element: '',
		  		file_data_name: fileField,
		  		max_file_size: '8mb',
		  		max_file_count: 1,
					flash_swf_url: SynvedOptionVars.flash_swf_url,
					silverlight_xap_url: SynvedOptionVars.silverlight_xap_url,
		  		url: SynvedOptionVars.ajaxurl,
		  		multipart: true,
					urlstream_upload: true,
		  		multipart_params : 
		  		{
						action : 'synved_option',
						synvedSecurity : SynvedOptionVars.synvedSecurity,
						synvedAction : 'install-addon',
						synvedParams : params
		  		},
					filters : [
						{title : "Zip files", extensions : "zip"}
					],
					multiple_queues: true,
		  		debug: true
    		});
    		
				uploader.bind('Init', function(up) {
					up.bind('FilesAdded', function(up, files) {
						
	 					progressBar.progressbar('option', 'value', 0);
	 					
	 					if (files.length > 1)
	 					{
	 						up.splice(1);
	 					}

						plupload.each(files, function(file){
						});

						up.refresh();
						up.start();
					});
				});
				
				uploader.bind('UploadProgress', function(up, file) {
	 				progressBar.progressbar('option', 'value', file.percent);
				});
				
				uploader.bind('FileUploaded', function(up, file, info) {
					var response = info.response;
					var responseObj = null;
					
					try	
					{
						responseObj = jQuery.parseJSON(response);
					}
					catch (ex) 
					{
						responseObj = jQuery.parseJSON(jQuery('<div/>').html(response).text());
					}
					
					if (responseObj != null)
					{
						//tbCont.append('FileUploaded ' + JSON.stringify(file) + ' INFO: ' + jQuery('<div/>').text(JSON.stringify(info)).html());
						
						var result = responseObj['result'];
						
						if (result == 'ERROR')
						{
	 						progressBar.progressbar('option', 'value', 0);
	 						
							var error = null;
							
							if ('error' in responseObj)
							{
								error = responseObj['error'];
							}
							
							if (error == 'NO_CREDS')
							{
								var form = jQuery('<div>' + responseObj['creds_form'] + '</div>').find('form');
								var submitData = up.settings.multipart_params;
						
								if (submitData)
								{
									for (var i in submitData)
									{
										form.find('[name=' + i + ']').val(submitData[i]);
									}
								}
						
								form.data('submit-ok', false);
								form.submit(function () {
									form.data('submit-ok', true);
									form.dialog('close');
							
									return false;
								});
						
								form.find('.wrap .icon32').attr('id', 'icon-index');
						
								form.dialog({
								  dialogClass : 'wp-dialog synved-option-creds-form',
									title: 'Connection Details',
									width: 550,
									modal: true,
									resizable: false,
									close: function() {
										if (form.data('submit-ok') == true)
										{
											var formData = form.serializeArray();
											var submitData = {};
									
											for (var i in formData)
											{
												submitData[formData[i].name] = formData[i].value;
											}
											
											for (var i in up.settings.multipart_params)
											{
												submitData[i] = up.settings.multipart_params[i];
											}
											
											up.settings.multipart_params = submitData;
											
											for (var f in up.files)
											{
												up.files[f].loaded = 0;
												up.files[f].percent = 0;
												up.files[f].status = plupload.STOPPED;
											}
											
											up.refresh();
											up.start();
										}
										else
										{
											finishInstall('Installation was canceled.', true);
										}
									}
								});
							}
							else
							{
								finishInstall('An error occurred.', true);
							}
						}
						else
						{
							if (result == 'OK')
							{
								finishInstall('Installation was successful.', false);
							}
							
	 						progressBar.progressbar('option', 'value', file.percent);
						}
					}
				});
				
				uploader.bind('Error', function(up, err) {
					console.log(err);
					finishInstall(err.message, true);
				});
    		
    		uploader.init();
	 		}
	 		else
	 		{
	 			tbCont.html('Error');
	 		}
		}

		return false;
	});
});

