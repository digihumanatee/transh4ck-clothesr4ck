<?php

define('SYNVED_PLUGOUT_LOADED', true);
define('SYNVED_PLUGOUT_VERSION', 100000001);
define('SYNVED_PLUGOUT_VERSION_STRING', '1.0.1');


$synved_plugout = array();


function synved_plugout_version()
{
	return SYNVED_PLUGOUT_VERSION;
}

function synved_plugout_version_string()
{
	return SYNVED_PLUGOUT_VERSION_STRING;
}

function synved_plugout_path_default($path_id)
{
	switch ($path_id)
	{
		case 'module':
		{
			return dirname(dirname(__FILE__));
		}
	}
	
	return null;
}

function synved_plugout_path_get($path_id)
{
	global $synved_plugout;
	
	if (isset($synved_plugout['path'][$path_id]))
	{
		return $synved_plugout['path'][$path_id];
	}
	
	return synved_plugout_path_default($path_id);
}

function synved_plugout_path_set($path_id, $path)
{
	global $synved_plugout;
	
	$synved_plugout['path'][$path_id] = $path;
}

function synved_plugout_get_module_list()
{
	global $synved_plugout;
	
	return array_keys($synved_plugout['module-list']);
}

function synved_plugout_module_register($module_id, $module_prefix = null, $module_name = null)
{
	global $synved_plugout;
	
	if (!isset($synved_plugout['module-list'][$module_id]))
	{
		$synved_plugout['module-list'][$module_id] = array(
			'id' => $module_id,
			'name' => $module_name,
			'prefix' => $module_prefix,
			'location' => null,
			'callback-list' => array()
		);
		
		return true;
	}
	
	return false;
}

function synved_plugout_module_path_add($module_id, $type, $path, $meta = null)
{
	global $synved_plugout;
	
	if (isset($synved_plugout['module-list'][$module_id]))
	{
		$path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
		
		$path_object = $meta ? $meta : array();
		$path_object['path'] = $path;
		
		$synved_plugout['module-list'][$module_id]['path-list'][$type][] = $path_object;
		
		return true;
	}
	
	return false;
}

function synved_plugout_module_path_get($module_id, $type)
{
	$path_list = synved_plugout_module_path_list_get($module_id, $type, 'first');
	
	if ($path_list != null)
	{
		return $path_list[0]['path'];
	}
	
	return null;
}

function synved_plugout_module_path_list_get($module_id, $type, $criteria = null)
{
	global $synved_plugout;
	
	if (isset($synved_plugout['module-list'][$module_id]))
	{
		$path_list = $synved_plugout['module-list'][$module_id]['path-list'];
		
		if ($path_list != null)
		{
			$return_list = array();
		
			foreach ($path_list as $path_type => $path_type_list)
			{
				if ($type == null || $type == $path_type)
				{
					foreach ($path_type_list as $path_object)
					{
						$return_item = array();
				
						$return_item['type'] = $path_type;
						$return_item['path'] = $path_object['path'];
						$return_item['meta'] = $path_object;
					
						$return_list[] = $return_item;
					}
				}
			}
		
			return $return_list;
		}
	}
	
	return null;
}

function synved_plugout_module_get($module_id)
{
	global $synved_plugout;
	
	if (isset($synved_plugout['module-list'][$module_id]))
	{
		return $synved_plugout['module-list'][$module_id];
	}
	
	return null;
}

function synved_plugout_module_exists($module_id)
{
	$module = synved_plugout_module_get($module_id);
	
	if ($module !== null)
	{
		return true;
	}
	
	return false;
}

function synved_plugout_module_version($module_id)
{
	$module = synved_plugout_module_get($module_id);
	
	if ($module !== null)
	{
		$module_cb = isset($module['callback-list']) ? $module['callback-list'] : null;
		$version_cb = str_replace('-', '_', $module_id) . '_version';
		
		if (isset($module_cb['version']))
		{
			$version_cb = $module_cb['version'];
		}
		
		if (is_callable($version_cb))
		{
			return $version_cb();
		}
	}
	
	return false;
}

function synved_plugout_module_location_get($module_id)
{
	$module = synved_plugout_module_get($module_id);
	
	if ($module != null)
	{
		if (isset($module['location']))
		{
			return $module['location'];
		}
	}
	
	return null;
}

function synved_plugout_module_directory_get($module_id)
{
	$location = synved_plugout_module_location_get($module_id);

	if ($location != null)
	{
		return dirname($location);
	}
	
	return null;
}

function synved_plugout_module_uri_get($module_id)
{
	$directory = synved_plugout_module_directory_get($module_id);

	if ($directory != null)
	{
		$directory = strtolower($directory);
		$content_dir = strtolower(WP_CONTENT_DIR);
		$base_len = strlen($content_dir);
		
		if (substr($directory, 0, $base_len) == $content_dir)
		{
			return content_url(substr($directory, $base_len));
		}
	}
	
	return null;
}

function synved_plugout_module_callback_set($module_id, $callback_id, $callback)
{

}

function synved_plugout_module_import($module_id)
{
	// XXX better way to check if the plugin/module is being activated?
	// This is needed because on activation the plugin's code is included *after* the theme code
	if (strpos($_SERVER['REQUEST_URI'], '/plugins.php?') !== false &&
			isset($_GET['action']) && $_GET['action'] == 'activate')
	{
		return false;
	}
				
	global $synved_plugout;
	
	if (isset($synved_plugout['module-list'][$module_id]))
	{
		$lib_path = synved_plugout_module_path_get($module_id, 'library');
		$core_path = synved_plugout_module_path_get($module_id, 'core');
		
		if ($lib_path == null)
		{
			if ($core_path == null)
			{
				$module_path = synved_plugout_path_get('module');
			
				if (substr($module_path, -1) != DIRECTORY_SEPARATOR)
				{
					$module_path .= DIRECTORY_SEPARATOR;
				}
			
				$core_path = $module_path . $module_id;
			}
		
			if (is_dir($core_path))
			{
				if (substr($core_path, -1) != DIRECTORY_SEPARATOR)
				{
					$core_path .= DIRECTORY_SEPARATOR;
				}
				
				$lib_path = $core_path . $module_id;
		
				if (file_exists($lib_path . '.php'))
				{
					$lib_path .= '.php';
				}
				else if (file_exists($lib_path . '.inc.php'))
				{
					$lib_path .= '.inc.php';
				}
				else
				{
					$lib_path = null;
				}
			}
		}
		
		if ($lib_path != null && file_exists($lib_path))
		{
			$version = synved_plugout_module_version($module_id);
		
			if ($version === false)
			{
				$synved_plugout['module-list'][$module_id]['location'] = $lib_path;
				
				include_once($lib_path);
			}
			else
			{
				// XXX undefine old module, include new one
			}
		
			return true;
		}
	}
	
	return false;
}

function synved_plugout_module_addon_scan_path($path, $filter = null)
{
	if ($filter == null)
	{
		$filter = '*';
	}
	
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	
	if (substr($path, -1) != DIRECTORY_SEPARATOR)
	{
		$path .= DIRECTORY_SEPARATOR;
	}
	
	if (is_dir($path))
	{
		$list = glob($path . '*', GLOB_ONLYDIR);
		$addon_list = array();
		$filter_regex = '/' . str_replace(array('*'), array('.*'), $filter) . '/';
		
		if ($list != null)
		{
			foreach ($list as $addon_dir)
			{
				$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $addon_dir);
				$path = rtrim($path, DIRECTORY_SEPARATOR);
				$base = basename($addon_dir);
			
				if (preg_match($filter_regex, $base))
				{
					$filename = $addon_dir . DIRECTORY_SEPARATOR . $base . '.php';
			
					if (file_exists($filename))
					{
						$addon_list[$base] = $filename;
					}
				}
			}
		}
		
		return $addon_list;
	}
}

function synved_plugout_module_addon_list($module_id, $filter = null)
{
	$addon_list = array();
	$path_list = synved_plugout_module_path_list_get($module_id, 'addon');
	
	if ($path_list != null)
	{
		foreach ($path_list as $path_item)
		{
			$path = $path_item['path'];
			$extra_list = synved_plugout_module_addon_scan_path($path, $filter);
			
			if ($extra_list != null)
			{
				$addon_list = array_merge($addon_list, $extra_list);
			}
		}
	}
#	
#	$path_list = synved_plugout_module_path_list_get($module_id, 'addon-plugin');
#	
#	if ($path_list != null)
#	{
#		foreach ($path_list as $path_item)
#		{
#			$path = $path_item['path'];
#			$addon_plugin = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'addon-plugin.php';
#			$extra_list = null;
#			
#			if (file_exists($addon_plugin))
#			{
#				$content = file_get_contents($addon_plugin);
#				$match = null;
#				
#  			if (preg_match('/\\/(?:\\*)+\\s*(?:synved-)?addon-plugin\\s*\\{\\s*Name\\s*:\\s*(?P<name>[\\w_-]+)(?:\\s*,\\s*\\w+\\s*:\\s*(?:[\\w_-]+))*\\s*\\}/m', $content, $match) > 0)
#  			{
#					$addon_name = $match['name'];
#					$extra_list = array($addon_name => $addon_plugin);
#  			}
#			}
#			
#			if ($extra_list != null)
#			{
#				$addon_list = array_merge($addon_list, $extra_list);
#			}
#		}
#	}
#	
	$path_list = synved_plugout_module_path_list_get($module_id, 'addon-file');
	
	if ($path_list != null)
	{
		foreach ($path_list as $path_item)
		{
			$path = $path_item['path'];
			$extra_list = null;
			
			if (file_exists($path) && strtolower(substr($path, -4)) == '.php')
			{
				$addon_name = isset($path_item['path']['meta']['addon-name']) ? $path_item['path']['meta']['addon-name'] : basename($path, '.php');
				$extra_list = array($addon_name => $path);
			}
			
			if ($extra_list != null)
			{
				$addon_list = array_merge($addon_list, $extra_list);
			}
		}
	}
	
	return $addon_list;
}

?>
