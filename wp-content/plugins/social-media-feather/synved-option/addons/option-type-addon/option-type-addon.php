<?php

if (!function_exists('synved_option_render_type_addon'))
{
	
function synved_option_render_type_addon($id, $name, $item, $out_name, $extra = null)
{
	$type = synved_option_item_type($item);
	$folder = synved_option_item_property($item, 'folder');
	$out_id = isset($extra['out_id']) ? $extra['out_id'] : null;
	$label = isset($extra['label']) ? $extra['label'] : null;
	$out = null;
	
	$out .= '<div class="synved-option-overlay-markup snvdopt" style="display:none;"><div class="overlay-ui"><div id="' . $out_id . '_overlay_container' . '" class="overlay-container">&nbsp;</div><div class="overlay-message">' . __('Click "Upload" and select the addon file, "' . $folder . '.zip". Only 1 file can be selected.', 'synved-option') . '</div>' . __('Progress', 'synved-option') . ': <div class="overlay-progress"></div></div><input id="' . $out_id . '_overlay_button' . '" type="button" class="overlay-button button-primary" value="' . __('Upload', 'synved-option') . '"/></div>';
	$out .= '<div class="synved-option-item-info" style="display:none;">' . "\n" . json_encode(array('id' => $id, 'name' => $name)) . "\n" . '</div>';
	$out .= '<input type="hidden" name="' . synved_option_render_field_name($id, $name . '_info_') . '" value="' . $type . '" />';
	$out .= '<input name="' . $out_name . '" id="' . $out_id . '" type="button" value="' . $label .'" class="button-secondary synved-option-overlay-button" />';
	$out .= '<input type="hidden" name="synved_option_addon_uploaded" value="0" />';
	
	return $out;
}

function synved_option_item_addon_install($id, $name, $item)
{
	$return = null;
	$type = synved_option_item_type($item);
	$target = synved_option_item_property($item, 'target');
	$folder = synved_option_item_property($item, 'folder');
	$field_name = synved_option_name_default($id);
	$path = null;
	
	if (file_exists($target))
	{
		$path = $target;
	}

	if ($type != 'addon' || $path == null)
	{
		return false;
	}
	
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	
	if (substr($path, -1) != DIRECTORY_SEPARATOR)
	{
		$path .= DIRECTORY_SEPARATOR;
	}
	
	if (isset($_FILES[$field_name]))
	{
		foreach ($_FILES[$field_name]["error"] as $key => $error) 
		{
			if ($key == $name && $error == UPLOAD_ERR_OK) 
			{
				$tmp_name = $_FILES[$field_name]["tmp_name"][$key];
				$name = $_FILES[$field_name]["name"][$key];
				$tmpfname = wp_tempnam($name . '.zip');
				
				if (move_uploaded_file($tmp_name, $tmpfname))
				{
					global $wp_filesystem;
  			
					$dirs = glob($path . '*', GLOB_ONLYDIR);
					
					$return = unzip_file($tmpfname, $path);
					
					if ($wp_filesystem != null)
					{
						$wp_filesystem->delete($tmpfname);
		  		}
					
					$dirs_new = glob($path . '*', GLOB_ONLYDIR);
					$dirs_diff = array_values(array_diff($dirs_new, $dirs));
					$addon_path = $path;
					
					if ($dirs_diff != null)
					{
						$folder_path = null;
						
						foreach ($dirs_diff as $dir) 
						{
							if (basename($dir) == $folder)
							{
								$folder_path = $dir;
							}
						}
						
						// XXX no correct path, was unzip successful?
						if ($folder_path == null)
						{
							$folder_path = $dirs_diff[0];
						}
						
						$addon_path = $folder_path;
					}
					
					synved_option_set($id, $name, $addon_path);
				}
			}
		}
	}
	
	return $return;
}

function synved_option_ajax_type_addon($action, $params)
{
	$response = null;
	
	if (current_user_can('upload_files') && current_user_can('install_plugins'))
	{
		$id = isset($params['id']) ? $params['id'] : null;
		$name = isset($params['name']) ? $params['name'] : null;
	
		if ($id != null && $name != null)
		{
			$item = synved_option_item_find($id, $name);
		
			if ($item != null)
			{
				$return = null;
				$error_list = array();
			
				try 
				{
					$page_name = synved_option_item_page($id, $name);
					$page = synved_option_item($id, $page_name);
					$parent = synved_option_item_parent($page);
					$url = wp_nonce_url($parent . '?page=' . $page_name, 'synved-option');
		
					ob_start();
					$old_err = error_reporting(0);
					$credentials = request_filesystem_credentials($url);
					$form = ob_get_clean();
					error_reporting($old_err);
				
					ob_start();
					var_dump($_POST);
					$posted = ob_get_clean();
				
					if ($credentials === false) 
					{
						$response['result'] = 'ERROR';
						$response['error'] = 'NO_CREDS';
						$response['creds_form'] = $form;
						$response['posted'] = $posted;
					}
					else if (WP_Filesystem($credentials)) 
					{
						$return = synved_option_item_addon_install($id, $name, $item);
					}
				}
				catch (Exception $ex) 
				{
					$return = null;
				}
			
				if ($return != null)
				{
					$response['result'] = 'OK';
				}
			}
		}
	}
	
	return $response;
}

function synved_option_enqueue_scripts_type_addon()
{
	$uri = synved_option_path_uri('addons/' . basename(dirname(__FILE__)));
	
	wp_register_style('synved-option-type-addon-admin', $uri . '/style/admin.css', array('jquery-ui', 'wp-jquery-ui-dialog'), '1.0');
	
	wp_register_script('synved-option-type-addon-script-custom', $uri . '/script/custom.js', array('jquery', 'thickbox', 'jquery-ui-core', 'jquery-ui-progressbar', 'jquery-ui-dialog'), '1.0.0');
	
	//wp_enqueue_style('synved-option-type-addon-admin');
	
	wp_enqueue_script('plupload-all');
	wp_enqueue_script('synved-option-type-addon-script-custom');
}

if (is_admin())
{
	add_action('admin_enqueue_scripts', 'synved_option_enqueue_scripts_type_addon');
}

}

?>
