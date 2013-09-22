<?php

function synved_social_provider_settings()
{
	$share_providers = synved_social_service_provider_list('share', true);
	$follow_providers = synved_social_service_provider_list('follow', true);
	$provider_list = array_merge($share_providers, $follow_providers);
	$providers_settings = array();

	foreach ($provider_list as $provider_name => $provider_item)
	{
		$provider_label = ucwords(str_replace(array('-', '_'), ' ', $provider_name));
		$display_set = 'none=None';
		$display_default = 'none';
	
		if (isset($provider_item['label']))
		{
			$provider_label = $provider_item['label'];
		}
		
		if (isset($share_providers[$provider_name]))
		{
			$display_set .= ',share=Share';
			
			if (!isset($share_providers[$provider_name]['default-display']) || $share_providers[$provider_name]['default-display'])
			{
				$display_default = 'share';
			}
		}
		
		if (isset($follow_providers[$provider_name]))
		{
			$display_set .= ',follow=Follow';
			
			if (isset($share_providers[$provider_name]))
			{
				$display_set .= ',both=Share & Follow';
			}
			
			if (!isset($follow_providers[$provider_name]['default-display']) || $follow_providers[$provider_name]['default-display'])
			{
				if ($display_default == 'share')
				{
					$display_default = 'both';
				}
				else
				{
					$display_default = 'follow';
				}
			}
		}
		
		$providers_settings = array_merge($providers_settings, 
			array(
				$provider_name . '_display' => array(
					'default' => $display_default,
					'style' => 'group',
					'set' => $display_set,
					'label' => __($provider_label . ' Service', 'synved-social'), 
					'tip' => __('Decides for what types of services ' . $provider_label . ' will be used by default', 'synved-social')
				),
			)
		);
	
		if (isset($share_providers[$provider_name]))
		{
			$share_item = $share_providers[$provider_name];
			
			$providers_settings = array_merge($providers_settings, 
				array(
					$provider_name . '_share_link' => array(
						'label' => __($provider_label . ' Share Link', 'synved-social'), 
						'tip' => __('The link used by default for sharing content on ' . $provider_label . ' (a standard one will be used if left empty)', 'synved-social'),
						'hint' => $share_item['link']
					),
					$provider_name . '_share_title' => array(
						'label' => __($provider_label . ' Share Title', 'synved-social'), 
						'tip' => __('The title used by default for the ' . $provider_label . ' share button (a standard one will be used if left empty)', 'synved-social'),
						'hint' => $share_item['title']
					),
				)
			);
		}
	
		if (isset($follow_providers[$provider_name]))
		{
			$follow_item = $follow_providers[$provider_name];
			
			$providers_settings = array_merge($providers_settings, 
				array(
					$provider_name . '_follow_link' => array(
						'label' => __($provider_label . ' Follow Link', 'synved-social'), 
						'tip' => __('The link used by default for following you on ' . $provider_label, 'synved-social'),
						'hint' => $follow_item['link']
					),
					$provider_name . '_follow_title' => array(
						'label' => __($provider_label . ' Follow Title', 'synved-social'), 
						'tip' => __('The title used by default for the ' . $provider_label . ' follow button (a standard one will be used if left empty)', 'synved-social'),
						'hint' => $follow_item['title']
					),
				)
			);
		}
	}
	
	return $providers_settings;
}

$synved_social_options = array(
'settings' => array(
	'label' => 'Social Media',
	'title' => 'Social Media Feather',
	'tip' => synved_option_callback('synved_social_page_settings_tip'),
	'link-target' => plugin_basename(synved_plugout_module_path_get('synved-social', 'provider')),
	'sections' => array(
		'section_general' => array(
			'label' => __('General Settings', 'synved-social'), 
			'tip' => __('Settings affecting the general behaviour of the plugin', 'synved-social'),
			'settings' => array(
				'use_shortlinks' => array(
					'default' => false, 'label' => __('Use Shortlinks', 'synved-social'), 
					'tip' => __('Allows for shortened URLs to be used when sharing content if a shortening plugin is installed', 'synved-social')
				),
				'share_full_url' => array(
					'default' => false, 'label' => __('Share Full URL', 'synved-social'), 
					'tip' => __('Determines whether to always share the full URL or just the post permalink. You can override this for individual posts by setting the "synved_social_share_full_url" custom field to either "yes" or "no"', 'synved-social')
				),
				'shortcode_widgets' => array(
					'default' => true, 'label' => __('Shortcodes In Widgets', 'synved-social'), 
					'tip' => __('Allow shortcodes in Text widgets', 'synved-social')
				),
				'show_credit' => array(
					'default' => true, 'label' => __('Show Credit', 'synved-social'), 
					'tip' => __('Display a small icon with a link to the Social Media Feather page', 'synved-social')
				),
				'share_message_default' => array(
					'default' => __('Hey check this out', 'synved-social'), 'label' => __('Default Message', 'synved-social'), 
					'tip' => __('Specify the default message to use when sharing content, this is what gets replaced into the %%message%% variable', 'synved-social')
				),
			)
		),
		'section_automatic_display' => array(
			'label' => __('Automatic Display', 'synved-social'), 
			'tip' => __('Settings affecting automating appending of social buttons to post contents', 'synved-social'),
			'settings' => array(
				'automatic_share' => array(
					'default' => false, 'label' => __('Display Sharing Buttons', 'synved-social'), 
					'tip' => __('Tries to automatically append sharing buttons to your posts (disable for specific posts by setting custom field synved_social_exclude or synved_social_exclude_share to yes)', 'synved-social')
				),
				'automatic_share_position' => array(
					'default' => 'after_post',
					'set' => 'after_post=After Post,before_post=Before Post',
					'label' => __('Share Buttons Position', 'synved-social'), 
					'tip' => __('Select where the sharing buttons should be placed. Note: placing buttons Before Post might not work in all themes.', 'synved-social')
				),
				'automatic_share_single' => array(
					'default' => false, 'label' => __('Sharing Single Posts', 'synved-social'), 
					'tip' => __('Sharing buttons are only displayed on single posts/pages and not on archive pages like blog/category/tag/author pages', 'synved-social')
				),
				'automatic_share_post_types' => array(
					'type' => 'custom',
					'default' => 'post',
					'set' => synved_option_callback('synved_social_automatic_append_post_types_set', array('post', 'page')),
					'label' => __('Share Post Types', 'synved-social'), 
					'tip' => __('Post types for which automatic appending for share buttons should be attempted (CTRL + click to select multiple ones)', 'synved-social'),
					'render' => 'synved_social_automatic_append_post_types_render'
				),
				'automatic_share_prefix' => array(
					'default' => '', 'label' => __('Share Prefix Markup', 'synved-social'), 
					'tip' => __('When automatically appending, place this markup before the share buttons markup', 'synved-social')
				),
				'automatic_share_postfix' => array(
					'default' => '', 'label' => __('Share Postfix Markup', 'synved-social'), 
					'tip' => __('When automatically appending, place this markup after all of the share buttons markup', 'synved-social')
				),
				'automatic_follow' => array(
					'default' => false, 'label' => __('Display Follow Buttons', 'synved-social'), 
					'tip' => __('Tries to automatically append follow buttons to your posts (disable for specific posts by setting custom field synved_social_exclude or synved_social_exclude_follow to yes)', 'synved-social')
				),
				'automatic_follow_position' => array(
					'default' => 'after_post',
					'set' => 'after_post=After Post,before_post=Before Post',
					'label' => __('Follow Buttons Position', 'synved-social'), 
					'tip' => __('Select where the follow buttons should be placed. Note: placing buttons Before Post might not work in all themes.', 'synved-social')
				),
				'automatic_follow_single' => array(
					'default' => false, 'label' => __('Follow Single Posts', 'synved-social'), 
					'tip' => __('Follow buttons are only displayed on single posts/pages and not on archive pages like blog/category/tag/author pages', 'synved-social')
				),
				'automatic_follow_post_types' => array(
					'type' => 'custom',
					'default' => 'post',
					'set' => synved_option_callback('synved_social_automatic_append_post_types_set', array('post', 'page')),
					'label' => __('Follow Post Types', 'synved-social'), 
					'tip' => __('Post types for which automatic appending for follow buttons should be attempted (CTRL + click to select multiple ones)', 'synved-social'),
					'render' => 'synved_social_automatic_append_post_types_render'
				),
				'automatic_follow_before_share' => array(
					'default' => false, 'label' => __('Follow Before Share', 'synved-social'), 
					'tip' => __('When automatically appending, place follow buttons before share buttons. Only valid when share and follow buttons positions are the same.', 'synved-social')
				),
				'automatic_follow_prefix' => array(
					'default' => '', 'label' => __('Follow Prefix Markup', 'synved-social'), 
					'tip' => __('When automatically appending, place this markup before the follow buttons markup', 'synved-social')
				),
				'automatic_follow_postfix' => array(
					'default' => '', 'label' => __('Follow Postfix Markup', 'synved-social'), 
					'tip' => __('When automatically appending, place this markup after all of the follow buttons markup', 'synved-social')
				),
				'automatic_append_prefix' => array(
					'default' => '', 'label' => __('Prefix Markup', 'synved-social'), 
					'tip' => __('When automatically appending, place this markup before the buttons markup', 'synved-social')
				),
				'automatic_append_separator' => array(
					'default' => '<br/>', 'label' => __('Separator Markup', 'synved-social'), 
					'tip' => __('When automatically appending both, use this markup as separator between the set of share buttons and the set of follow buttons. Only valid when share and follow buttons positions are the same.', 'synved-social')
				),
				'automatic_append_postfix' => array(
					'default' => '', 'label' => __('Postfix Markup', 'synved-social'), 
					'tip' => __('When automatically appending, place this markup after all of the buttons markup', 'synved-social')
				),
			)
		),
		'section_customize_look' => array(
			'label' => __('Customize Look', 'synved-social'), 
			'tip' => synved_option_callback('synved_social_section_customize_look_tip', __('Customize the look & feel of Social Media Feather', 'synved-social')),
			'settings' => array(
				'icon_skin' => array(
					'default' => 'regular',
					'set' => synved_option_callback('synved_social_icon_skin_set', 'regular=Regular'),
					'label' => __('Icon Skin', 'synved-social'), 
					'tip' => synved_option_callback('synved_social_setting_icon_skin_tip',__('Select the default skin to use for the icons', 'synved-social')),
					'render' => 'synved_social_icon_skin_render'
				),
				'addon_extra_icons' => array(
					'type' => 'addon',
					'target' => SYNVED_SOCIAL_ADDON_PATH,
					'folder' => 'extra-icons',
					'module' => 'synved-social',
					'style' => 'addon-important',
					'label' => __('Extra Icon Skins', 'synved-social'), 
					'tip' => synved_option_callback('synved_social_option_addon_extra_icons_tip', __('Click the button to install the "Extra Social Icons" addon, get it <a target="_blank" href="http://synved.com/product/feather-extra-social-icons/">here</a>.', 'synved-social'))
				),
				'addon_grey_fade' => array(
					'type' => 'addon',
					'target' => SYNVED_SOCIAL_ADDON_PATH,
					'folder' => 'grey-fade',
					'module' => 'synved-social',
					'style' => 'addon-important',
					'label' => __('Grey Fade Effect', 'synved-social'), 
					'tip' => synved_option_callback('synved_social_option_addon_grey_fade_tip', __('Click the button to install the "Grey Fade" addon, get it <a target="_blank" href="http://synved.com/product/feather-grey-fade/">here</a>.', 'synved-social'))
				),
				'addon_light_prompt' => array(
					'type' => 'addon',
					'target' => SYNVED_SOCIAL_ADDON_PATH,
					'folder' => 'light-prompt',
					'module' => 'synved-social',
					'style' => 'addon-important',
					'label' => __('Light Prompt Overlay', 'synved-social'), 
					'tip' => synved_option_callback('synved_social_option_addon_light_prompt_tip', __('Click the button to install the "Light Prompt" addon, get it <a target="_blank" href="http://synved.com/product/feather-light-prompt/">here</a>.', 'synved-social'))
				),
				'icon_size' => array(
					'default' => 48,
					'set' => '16=16x16,24=24x24,32=32x32,48=48x48,64=64x64,96=96x96',
					'label' => __('Icon Size', 'synved-social'), 
					'tip' => __('Select the size in pixels for the icons. Note: for high resolution displays like Retina the maximum size is 64x64.', 'synved-social')
				),
				'icon_resolution' => array(
					'default' => 'single',
					'set' => 'single=Single,double=Double',
					'label' => __('Icon Resolution', 'synved-social'), 
					'tip' => __('Select what icon resolutions will be used. Single might make the icons slightly blurry on low resolution displays. Double will always look the best but will consume more bandwidth.', 'synved-social')
				),
				'icon_spacing' => array(
					'default' => 5,
					'label' => __('Icon Spacing', 'synved-social'), 
					'tip' => __('Select the spacing in pixels between the icons', 'synved-social')
				),
				'custom_style' => array(
					'type' => 'style',
					'label' => __('Extra Styles', 'synved-social'), 
					'tip' => __('Any CSS styling code you type in here will be loaded after all of the Social Media Feather styles.', 'synved-social')
				),
			)
		),
		'section_service_providers' => array(
			'label' => __('Service Providers', 'synved-social'), 
			'tip' => __('Customize social sharing and following providers', 'synved-social'),
			'settings' => synved_social_provider_settings()
		)
	)
)
);


synved_option_register('synved_social', $synved_social_options);

synved_option_include_module_addon_list('synved-social');


function synved_social_provider_option_value_sanitize($value, $name, $id, $item)
{
	$default = synved_option_item_default($item);
	
	if ($value == $default)
	{
		
	}
}

function synved_social_page_settings_tip($tip, $item)
{
	if (!function_exists('synved_shortcode_version'))
	{
		$tip .= ' <div style="background:#f2f2f2;font-size:110%;color:#444;padding:10px 15px;"><b>' . __('Note', 'synved-social') . '</b>: ' . __('The Social Media Feather plugin is fully compatible with our free <a target="_blank" href="http://synved.com/wordpress-shortcodes/">WordPress Shortcodes</a> plugin!</span>', 'synved-social') . '</div>';
	}
	
	if (function_exists('synved_connect_support_social_follow_render'))
	{
		$tip .= synved_connect_support_social_follow_render();
	}
	
	return $tip;
}

function synved_social_section_customize_look_tip($tip, $item)
{
	return $tip;
}

function synved_social_icon_skin_set($set, $item) 
{
	if ($set != null && !is_array($set))
	{
		$set = synved_option_item_set_parse($item, $set);
	}
	
	$set = array();
	$icons = synved_social_icon_skin_list();

	foreach ($icons as $icon_name => $icon_meta)
	{
		$set[][$icon_name] = $icon_meta['label'];
	}
	
	return $set;
}

function synved_social_setting_icon_skin_tip($tip, $item)
{
	$uri = synved_social_path_uri();
	
	if (!synved_option_addon_installed('synved_social', 'addon_extra_icons'))
	{
		$tip .= '<div style="clear:both"><p style="font-size:120%;"><b>Get all 8 extra icon skins you see below with the <a target="_blank" href="http://synved.com/product/feather-extra-social-icons/">Extra Social Icons addon</a></b>:</p> <a target="_blank" href="http://synved.com/product/feather-extra-social-icons/"><img src="' . $uri . '/image/social-feather-extra-icons.png" /></a></div>';
	}
	
	return $tip;
}

function synved_social_icon_skin_render($value, $params, $id, $name, $item) 
{
	$uri = synved_social_path_uri();
	$icons = synved_social_icon_skin_list();
	
	$out = null;
	$out_name = $params['output_name'];
	$set = $params['set'];
	
	$out .= '<div>';

	foreach ($set as $set_it)
	{
		$set_it_keys = array_keys($set_it);
		$selected = $set_it_keys[0] == $value ? ' checked="checked"' : null;
		$img_src = '';
		
		if (isset($icons[$set_it_keys[0]]))
		{
			$img_src = $icons[$set_it_keys[0]]['image'];
		}
		
		$out .= '<div style="text-align:center; width:260px; float:left; margin-right:20px;"><label title="Use skin=&quot;' . esc_attr($set_it_keys[0]) . '&quot; in shortcodes"><img src="' . esc_url($img_src) . '" style="border:solid 1px #bbb" /><p><input type="radio" name="' . esc_attr($out_name) . '" value="' . esc_attr($set_it_keys[0]) . '"' . $selected . '/> ' . $set_it[$set_it_keys[0]] . '</p></label></div>';
	}
	
	$out .= '</div>';
	
	return $out;
}


function synved_social_automatic_append_post_types_set($set, $item) 
{
	if ($set != null && !is_array($set))
	{
		$set = synved_option_item_set_parse($item, $set);
	}
	
	$set = array();
	$types = get_post_types(array('public' => true));

	foreach ($types as $type_name)
	{
		$set[][$type_name] = $type_name;
	}
	
	return $set;
}

function synved_social_automatic_append_post_types_render($value, $params, $id, $name, $item) 
{
	$uri = synved_social_path_uri();
	$icons = synved_social_icon_skin_list();
	
	if (!is_array($value))
	{
		if ($value != null)
		{
			$value = array($value);
		}
		else
		{
			$value = array();
		}
	}
	
	$out = null;
	$out_name = $params['output_name'];
	$set = $params['set'];
	
	$out .= '<select multiple="multiple" name="' . esc_attr($out_name . '[]') . '">';

	foreach ($set as $set_it)
	{
		$set_it_keys = array_keys($set_it);
		$selected = in_array($set_it_keys[0], $value) ? ' selected="selected"' : null;
		
		$out .= '<option value="' . esc_attr($set_it_keys[0]) . '"' . $selected . '>' . $set_it[$set_it_keys[0]] . '</option>';
	}
	
	$out .= '</select>';
	
	return $out;
}

function synved_social_option_addon_extra_icons_tip($tip, $item)
{
	if (synved_option_addon_installed('synved_social', 'addon_extra_icons'))
	{
		// missing icons for installed extra-icons addon
		if (!function_exists('synved_social_addon_extra_icons_version'))
		{
			$tip .= ' <span style="background:#ecc;padding:5px 8px;">' . __('The "Extra Social Icons" addon is already installed but requires an update for recently added providers, please use your download link or <a href="http://synved.com/about/contact/?subject=Feather%20Extra%20Icons%20new%20link">request a new one</a>', 'synved-social') . '</span>';
		}
		else
		{
			$tip .= ' <span style="background:#eee;padding:5px 8px;">' . __('The "Extra Social Icons" addon is already installed! You can use the button to re-install it.', 'synved-social') . '</span>';
		}
	}
	
	return $tip;
}

function synved_social_option_addon_grey_fade_tip($tip, $item)
{
	$uri = synved_social_path_uri();
	
	if (synved_option_addon_installed('synved_social', 'addon_grey_fade'))
	{
		$tip .= ' <span style="background:#eee;padding:5px 8px;">' . __('The "Grey Fade" addon is already installed! You can use the button to re-install it.', 'synved-social') . '</span>';
	}
	else
	{
		$tip .= '<div style="clear:both"><p style="font-size:120%;"><b>The <a target="_blank" href="http://synved.com/product/feather-grey-fade/">Grey Fade addon</a> allows you to achieve the effect below, <a target="_blank" href="http://synved.com/product/feather-grey-fade/">get it now</a>!</b></p> <a target="_blank" href="http://synved.com/product/feather-grey-fade/"><img src="' . $uri . '/image/social-feather-grey-fade-demo.png" /></a></div>';
	}
	
	return $tip;
}

function synved_social_option_addon_light_prompt_tip($tip, $item)
{
	$uri = synved_social_path_uri();
	
	if (synved_option_addon_installed('synved_social', 'addon_light_prompt'))
	{
		$tip .= ' <span style="background:#eee;padding:5px 8px;">' . __('The "Light Prompt" addon is already installed! You can use the button to re-install it.', 'synved-social') . '</span>';
	}
	else
	{
		$tip .= '<div style="clear:both"><p style="font-size:120%;"><b>The <a target="_blank" href="http://synved.com/product/feather-light-prompt/">Light Prompt addon</a> allows you to achieve the nice overlay below when users click on a share button, <a target="_blank" href="http://synved.com/product/feather-light-prompt/">get it now</a>!</b></p> <a target="_blank" href="http://synved.com/product/feather-light-prompt/"><img src="' . $uri . '/image/social-feather-light-prompt-demo.png" /></a></div>';
	}
	
	return $tip;
}

function synved_social_path($path = null)
{
	$root = dirname(__FILE__);
	
	if ($root != null)
	{
		if (substr($root, -1) != '/' && $path[0] != '/')
		{
			$root .= '/';
		}
		
		$root .= $path;
	}
	
	$root = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $root);
	
	return $root;
}

function synved_social_path_uri($path = null)
{
	$uri = plugins_url('/social-media-feather') . '/synved-social';
	
	if (function_exists('synved_plugout_module_uri_get'))
	{
		$mod_uri = synved_plugout_module_uri_get('synved-social');
		
		if ($mod_uri != null)
		{
			$uri = $mod_uri;
		}
	}
	
	if ($path != null)
	{
		if (substr($uri, -1) != '/' && $path[0] != '/')
		{
			$uri .= '/';
		}
		
		$uri .= $path;
	}
	
	return $uri;
}

function synved_social_wp_register_common_scripts()
{
	$uri = synved_social_path_uri();
	
	//wp_register_style('synved-social-style', $uri . '/style/style.css', false, '1.0');
}

function synved_social_enqueue_scripts()
{
	$uri = synved_social_path_uri();
	
	synved_social_wp_register_common_scripts();
	
	//wp_enqueue_style('synved-social-style');
}

function synved_social_print_styles()
{
	echo "\r\n" . '<style type="text/css">';
	
	echo '
.synved-social-resolution-single {
display: inline-block;
}
.synved-social-resolution-normal {
display: inline-block;
}
.synved-social-resolution-hidef {
display: none;
}

@media only screen and (min--moz-device-pixel-ratio: 2),
only screen and (-o-min-device-pixel-ratio: 2/1),
only screen and (-webkit-min-device-pixel-ratio: 2),
only screen and (min-device-pixel-ratio: 2),
only screen and (min-resolution: 2dppx),
only screen and (min-resolution: 192dpi) {
	.synved-social-resolution-normal {
	display: none;
	}
	.synved-social-resolution-hidef {
	display: inline-block;
	}
}
';
	
	echo '</style>' . "\r\n";
}

function synved_social_admin_enqueue_scripts()
{
	$uri = synved_social_path_uri();
	
	synved_social_wp_register_common_scripts();
}

function synved_social_admin_print_styles()
{
}

function synved_social_wp_tinymce_plugin($plugin_array)
{
	$plugin_array['synved_social'] = synved_social_path_uri() . '/script/tinymce_plugin.js';

	return $plugin_array;
}

function synved_social_wp_tinymce_button($buttons) 
{
	array_push($buttons, '|', 'synved_social');
	
	return $buttons;
}

function synved_social_ajax_callback()
{
	check_ajax_referer('synved-social-submit-nonce', 'synvedSecurity');

	if (!isset($_POST['synvedAction']) || $_POST['synvedAction'] == null) 
	{
		return;
	}

	$action = $_POST['synvedAction'];
	$params = isset($_POST['synvedParams']) ? $_POST['synvedParams'] : null;
	$response = null;
	$response_html = null;
	
	if (is_string($params))
	{
		$parms = json_decode($params, true);
		
		if ($parms == null)
		{
			$parms = json_decode(stripslashes($params), true);
		}
		
		$params = $parms;
	}
	
	switch ($action)
	{
		case 'load-ui':
		{
			$uri = synved_social_path_uri();
			
			if (current_user_can('edit_posts') || current_user_can('edit_pages'))
			{
			}
			
			break;
		}
		case 'preview-code':
		{
			if (current_user_can('edit_posts') || current_user_can('edit_pages'))
			{
			}
			
			break;
		}
	}

	while (ob_get_level() > 0) 
	{
		ob_end_clean();
	}

	if ($response != null) 
	{
		$response = json_encode($response);

		header('Content-Type: application/json');

		echo $response;
	}
	else if ($response_html != null) 
	{
		header('Content-Type: text/html');

		echo $response_html;
	}
	else 
	{
		header('HTTP/1.1 403 Forbidden');
	}

	exit();
}

function synved_social_register_widgets() 
{
	register_widget('SynvedSocialShareWidget');
	register_widget('SynvedSocialFollowWidget');
}

function synved_social_wp_the_content($content, $id = null)
{
	$exclude = false;
	$exclude_share = false;
	$exclude_follow = false;
	
	$extra_after = null;
	$extra_before = null;
	$separator_after = null;
	$separator_before = null;
	
	if ($id == null)
	{
		$id = get_the_ID();
		
		if ($id == null)
		{
			global $post;
		
			$id = $post->ID;
		}
	}

	if ($id != null)	
	{
		$exclude = get_post_meta($id, 'synved_social_exclude', true) == 'yes' ? true : false;
		$exclude_share = get_post_meta($id, 'synved_social_exclude_share', true) == 'yes' ? true : false;
		$exclude_follow = get_post_meta($id, 'synved_social_exclude_follow', true) == 'yes' ? true : false;
		
		if (!$exclude_share && synved_option_get('synved_social', 'automatic_share_single'))
		{
			$exclude_share = !is_singular(synved_option_get('synved_social', 'automatic_share_post_types'));
		}
		
		if (!$exclude_follow && synved_option_get('synved_social', 'automatic_follow_single'))
		{
			$exclude_follow = !is_singular(synved_option_get('synved_social', 'automatic_follow_post_types'));
		}
	}
	
	if ($exclude == false)
	{
		if ($exclude_share == false && synved_option_get('synved_social', 'automatic_share'))
		{
			$post_type = get_post_type();
			$type_list = synved_option_get('synved_social', 'automatic_share_post_types');
		
			if (in_array($post_type, $type_list))
			{
				$position = synved_option_get('synved_social', 'automatic_share_position');
				$markup = synved_social_share_markup();
				$prefix = synved_option_get('synved_social', 'automatic_share_prefix');
				$postfix = synved_option_get('synved_social', 'automatic_share_postfix');
				$markup = $prefix . $markup . $postfix;
				
				switch ($position)
				{
					case 'after_post':
					{
						$extra_after .= $markup;
						
						break;
					}
					case 'before_post':
					{
						$extra_before .= $markup;
						
						break;
					}
				}
			}
		}
		
		$separator = synved_option_get('synved_social', 'automatic_append_separator');
	
		if ($extra_after != null)
		{
			$separator_after = $separator;
		}
	
		if ($extra_before != null)
		{
			$separator_before = $separator;
		}
	
		if ($exclude_follow == false && synved_option_get('synved_social', 'automatic_follow'))
		{
			$post_type = get_post_type();
			$type_list = synved_option_get('synved_social', 'automatic_follow_post_types');
		
			if (in_array($post_type, $type_list))
			{
				$position = synved_option_get('synved_social', 'automatic_follow_position');
				$markup = synved_social_follow_markup();
				$prefix = synved_option_get('synved_social', 'automatic_follow_prefix');
				$postfix = synved_option_get('synved_social', 'automatic_follow_postfix');
				$markup = $prefix . $markup . $postfix;
				
				switch ($position)
				{
					case 'after_post':
					{
						if (synved_option_get('synved_social', 'automatic_follow_before_share'))
						{
							$extra_after = $markup . $separator_after . $extra_after;
						}
						else
						{
							$extra_after .= $separator_after . $markup;
						}
						
						break;
					}
					case 'before_post':
					{
						if (synved_option_get('synved_social', 'automatic_follow_before_share'))
						{
							$extra_before = $markup . $separator_before . $extra_before;
						}
						else
						{
							$extra_before .= $separator_before . $markup;
						}
						
						break;
					}
				}
			}
		}
	
		$prefix = synved_option_get('synved_social', 'automatic_append_prefix');
		$postfix = synved_option_get('synved_social', 'automatic_append_postfix');
			
		if ($extra_after != null)
		{
			$content .= $prefix . $extra_after . $postfix;
		}
	
		if ($extra_before != null)
		{
			$content = $prefix . $extra_before . $postfix . $content;
		}
	}
	
	return $content;
}

function synved_social_init()
{
	if (current_user_can('edit_posts') || current_user_can('edit_pages'))
	{
		if (get_user_option('rich_editing') == 'true')
		{
			//add_filter('mce_external_plugins', 'synved_social_wp_tinymce_plugin');
			//add_filter('mce_buttons', 'synved_social_wp_tinymce_button');
		}
	}

	$priority = defined('SHORTCODE_PRIORITY') ? SHORTCODE_PRIORITY : 11;
	
	if (synved_option_get('synved_social', 'shortcode_widgets'))
	{
		remove_filter('widget_text', 'do_shortcode', $priority);
		add_filter('widget_text', 'do_shortcode', $priority);
	}
	
	if (function_exists('synved_shortcode_add'))
	{
  	synved_shortcode_add('feather_share', 'synved_social_share_shortcode');
  	synved_shortcode_add('feather_follow', 'synved_social_follow_shortcode');
  	
  	$size_set = '16,24,32,48,64,96';
  	$size_item = synved_option_item('synved_social', 'icon_size');
  	
  	if ($size_item != null)
  	{
  		$item_set = synved_option_item_set($size_item);
  		
  		if ($item_set != null)
  		{
  			$set_items = array();
  			
  			foreach ($item_set as $set_item)
  			{
  				$item_keys = array_keys($set_item);
  				
  				$set_items[] = $item_keys[0];
  			}
  			
  			$size_set = implode(',', $set_items);
  		}
  	}
  	
  	$providers_share = array_keys(synved_social_service_provider_list('share'));
  	$providers_follow = array_keys(synved_social_service_provider_list('follow'));
  	
  	$providers_params = array(
			'show' => __('Specify a comma-separated list of %1$s providers to show and their order, possible values are %2$s', 'synved-social'),
			'hide' => __('Specify a comma-separated list of %1$s providers to hide, possible values are %2$s', 'synved-social'),
		);
  	
  	$common_params = array(
			'skin' => __('Specify which skin to use for the icons', 'synved-social'),
			'size' => sprintf(__('Specify the size for the icons, possible values are %s', 'synved-social'), $size_set),
			'spacing' => __('Determines how much blank space there will be between the buttons, in pixels', 'synved-social'),
			'class' => __('Select additional CSS classes for the buttons, separated by spaces', 'synved-social'),
		);
		
  	$share_params = array(
			'url' => __('URL to use for the sharing buttons, default is the current post URL', 'synved-social'),
			'title' => __('Title to use for the sharing buttons, default is the current post title', 'synved-social'),
		);
		
		$follow_params = array(
		);
		
		$share_params = array_merge($common_params, $share_params);
		$follow_params = array_merge($common_params, $follow_params);
		
		foreach ($providers_params as $param_name => $param_value)
		{
			$share_params[$param_name] = sprintf($param_value, 'share', implode(', ', $providers_share));
			$follow_params[$param_name] = sprintf($param_value, 'follow', implode(', ', $providers_follow));
		}
	
		synved_shortcode_item_help_set('feather_share', array(
			'tip' => __('Creates a list of buttons for social sharing as selected in the Social Media options', 'synved-social'),
			'parameters' => $share_params
		));
		synved_shortcode_item_help_set('feather_follow', array(
			'tip' => __('Creates a list of buttons for social following as selected in the Social Media options', 'synved-social'),
			'parameters' => $follow_params
		));
	}
	else
	{
  	add_shortcode('feather_share', 'synved_social_share_shortcode');
  	add_shortcode('synved_feather_share', 'synved_social_share_shortcode');
  	add_shortcode('feather_follow', 'synved_social_follow_shortcode');
  	add_shortcode('synved_feather_follow', 'synved_social_follow_shortcode');
	}
	
  //add_action('wp_ajax_synved_social', 'synved_social_ajax_callback');
  //add_action('wp_ajax_nopriv_synved_social', 'synved_social_ajax_callback');

	if (!is_admin())
	{
		add_action('wp_enqueue_scripts', 'synved_social_enqueue_scripts');
		add_action('wp_head', 'synved_social_print_styles');
	}
	
	if (synved_option_get('synved_social', 'automatic_share') || synved_option_get('synved_social', 'automatic_follow'))
	{
  	add_filter('the_content', 'synved_social_wp_the_content', 10, 2);
	}
}

add_action('init', 'synved_social_init');
add_action('admin_enqueue_scripts', 'synved_social_admin_enqueue_scripts');
add_action('admin_print_styles', 'synved_social_admin_print_styles', 1);

add_action('widgets_init', 'synved_social_register_widgets');

