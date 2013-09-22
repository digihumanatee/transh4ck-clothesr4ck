<?php
/*
Module Name: Synved Option
Description: Easily add options to your themes or plugins with as little or as much coding as you want. Just create an array of your options, the rest is automated. If you need extra flexibility you can then use the powerful API provided to achieve any level of customization.
Author: Synved
Version: 1.4.5
Author URI: http://synved.com/
License: GPLv2

LEGAL STATEMENTS

NO WARRANTY
All products, support, services, information and software are provided "as is" without warranty of any kind, express or implied, including, but not limited to, the implied warranties of fitness for a particular purpose, and non-infringement.

NO LIABILITY
In no event shall Synved Ltd. be liable to you or any third party for any direct or indirect, special, incidental, or consequential damages in connection with or arising from errors, omissions, delays or other cause of action that may be attributed to your use of any product, support, services, information or software provided, including, but not limited to, lost profits or lost data, even if Synved Ltd. had been advised of the possibility of such damages.
*/


include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-option-item.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-option-page.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-option-section.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-option-render.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-option-setting.php');


define('SYNVED_OPTION_LOADED', true);
define('SYNVED_OPTION_VERSION', 100040005);
define('SYNVED_OPTION_VERSION_STRING', '1.4.5');


$synved_option = array();
$synved_option_list = array();


class SynvedOptionCallback
{
	private $_Object;
	private $_Callback;
	private $_Default;
	private $_Params;
	
	public function __construct($callback, $object = null, $default = null, array $callback_parameters = null)
	{
		$this->_Object = $object;
		$this->_Callback = $callback;
		$this->_Default = $default;
		$this->_Params = $callback_parameters;
	}
	
	public function __invoke($arguments = null)
	{
		if (!is_array($arguments) || func_num_args() > 1)
		{
			$arguments = func_get_args();
		}
		
		return $this->InvokeInternal($arguments);
	}
	
	public function Invoke($arguments = null)
	{
		if (!is_array($arguments) || func_num_args() > 1)
		{
			$arguments = func_get_args();
		}
		
		return $this->InvokeInternal($arguments);
	}
	
	protected function InvokeInternal(array $arguments = null)
	{
		$func = $this->_Callback;
		
		if ($this->_Object != null)
		{
			$func = array($this->_Object, $func);
		}
		
		$parameters = $this->_Params;
		
		if ($parameters != null)
		{
			$parameter_keys = array_keys($parameters);
			$count = count($parameter_keys);
			$argument_list = array();
		
			for ($i = 0; $i < $count; $i++)
			{
				$key = $parameter_keys[$i];
				$parameter = $parameters[$key];
				$value = isset($parameter['default']) ? $parameter['default'] : null;
			
				if (isset($arguments[$key]))
				{
					$value = $arguments[$key];
				}
				else if (isset($arguments[$i]))
				{
					$value = $arguments[$i];
				}
				
				$argument_list[$i] = $value;
			}
			
			$arguments = $argument_list;
		}
		
		if (!isset($arguments[0]) || $arguments[0] === null)
		{
			$arguments[0] = $this->_Default;
		}
		
		if (is_callable($func))
		{
			return call_user_func_array($func, $arguments);
		}
		
		return $arguments[0];
	}
}

function synved_option_version()
{
	return SYNVED_OPTION_VERSION;
}

function synved_option_version_string()
{
	return SYNVED_OPTION_VERSION_STRING;
}

function synved_option_callback($callback, $default = null, $callback_parameters = null)
{
	$object = null;
	$func = $callback;
	
	if (is_array($callback))
	{
		$object = $callback[0];
		$func = $callback[1];
	}
	
	return new SynvedOptionCallback($func, $object, $default, $callback_parameters);
}

function synved_option_callback_create($callback_code, $callback_parameters = null)
{
	if ($callback_parameters === null)
	{
		$callback_parameters = array(
			'value' => array(),
			'item' => array('default' => null), 
			'name' => array('default' => null), 
			'id' => array('default' => null)
		);
	}
	else if (!is_array($callback_parameters))
	{
		$parameters = explode(',', $callback_parameters);
		$callback_parameters = array();
		
		foreach ($parameters as $param)
		{
			$param = trim($param);
			$param_info = preg_split('/\\s+/', $param, -1, PREG_SPLIT_NO_EMPTY);
			
			if (count($param_info) > 1)
			{
				if ($param_info[1] == '=')
				{
					array_unshift($param_info, null);
				}
				
				$param_type = $param_info[0];
				$param_name = ltrim($param_info[1], '$');
				$param_manifest = array('type' => $param_type);
				
				if (count($param_info) > 2)
				{
					if ($param_info[2] == '=' && isset($param_info[3]))
					{
						$param_default = trim($param_info[3]);
						
						if ($param_default == 'null')
						{
							$param_default = null;
						}
						else if (in_array($param_default[0], array('\'', '"')))
						{
							$param_default = trim($param_default, '"\'');
						}
						else if (strpos($param_default, 'array()') === 0)
						{
							$param_default = array();
						}
						else if (strpos($param_default, 'array(') === 0)
						{
							// No array support
							$param_default = null;
						}
						else if (is_string($param_default))
						{
							// int or double (float)
							if (((string)((int) $param_default)) == $param_default)
							{
								$param_default = (int) $param_default;
							}
							else
							{
								$param_default = (double) $param_default;
							}
						}
					
						$param_manifest['default'] = $param_default;
					}
				}
				
				$callback_parameters[$param_name] = $param_manifest;
			}
			else
			{
				$param_name = ltrim($param_info[0], '$');
				
				$callback_parameters[$param_name] = array();
			}
		}
	}
	
	$callback_code = trim($callback_code);
	$callback = null;
	
	if ($callback_code != null)
	{
		if (substr($callback_code, -1) != ';')
		{
			$callback_code .= ';';
		}

		if (strpos($callback_code, 'return') === false)
		{
			$result = preg_split('/(?:([\'"])([^\\1]*\\1))|(;)/i', $callback_code, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			$count = count($result);
			$partial = null;
			$string = null;
			$lines = array();
			
			for ($i = 0; $i < $count; $i++)
			{
				$split = $result[$i];
				
				if ($string != null)
				{
					$partial .= $string . $split;
					$string = null;
				}
				else if ($split == ';')
				{
					$split = $partial;
					$partial = null;
				}
				else if ($split == '\'')
				{
					$string = $split;
				}
				else
				{
					$partial .= $split;
				}
				
				if ($split != null && $partial == null)
				{
					$lines[] = $split;
				}
			}
			
			$count = count($lines);
			$lines[$count - 1] = 'return ' . $lines[$count - 1];
		
			$callback_code = implode(';', $lines) . ';';
		}
		
		$function_params = null;
		
		foreach ($callback_parameters as $param_name => $callback_param)
		{
			$param_type = isset($callback_param['type']) ? $callback_param['type'] : null;
			$param_default = isset($callback_param['default']) ? $callback_param['default'] : null;
				
			if ($function_params != null)
			{
				$function_params .= ', ';
			}
			
			if ($param_type != null)
			{
				$function_params .= $param_type;
			}
			
			$function_params .= '$' . $param_name;
			
			if ($param_default != null)
			{
				$function_params .= ' = ' . $param_default;
			}
		}
		
		$callback = create_function($function_params, $callback_code);
		
		return synved_option_callback($callback, null, $callback_parameters);
	}
	
	return null;
}

function synved_option_register($id, array $options)
{
	global $synved_option_list;
	
	$synved_option_list[$id] = array('options' => $options, 'items' => array(), 'names' => array(), 'groups' => array(), 'pages' => array(), 'sections' => array(), 'outputs' => array());
}

function synved_option_item_list($id)
{
	global $synved_option_list;
	
	if (isset($synved_option_list[$id]))
	{
		$list = $synved_option_list[$id]['items'];
		
		if ($list == null)
		{
			$list = synved_option_prepare_list($id);
			$synved_option_list[$id]['items'] = $list;
		}
		
		return $list;
	}
	
	return null;
}

function synved_option_prepare_list($id)
{
	global $synved_option_list;
	
	if (isset($synved_option_list[$id]))
	{
		$options = $synved_option_list[$id]['options'];
		$options = apply_filters('synved_option_init_list', $options, $id);
		$options = apply_filters('synved_option_init_list_' . $id, $options, $id);
		
		$final_list = array();
		$default_page = null;
		$default_section = null;
		
		foreach ($options as $name => $item)
		{
			$type = synved_option_item_type($item);
			
			if ($type == 'options-page')
			{
				$item = synved_option_prepare_list_item($id, null, null, $name, $item);
				
				if ($item != null)
				{
					$final_list[$name] = $item;
				}
			}
			else
			{
				if ($default_page == null)
				{
					$default_page = synved_option_page_default($id);
					$default_page = synved_option_prepare_list_item($id, null, null, $default_page['name'], $default_page);
					
					$final_list[$default_page['name']] = &$default_page;
				}
			
				if ($type == 'options-section')
				{
					$item = synved_option_prepare_list_item($id, $default_page['name'], null, $name, $item);
				
					if ($item != null)
					{
						$default_page['sections'][$name] = $item;
					}
				}
				else
				{
					if ($default_section == null)
					{
						$default_section = synved_option_section_default($id, $default_page['name']);
						$default_section = synved_option_prepare_list_item($id, $default_page['name'], null, $default_section['name'], $default_section);
						
						$default_page['sections'][$default_section['name']] = &$default_section;
					}
					
					$default_section['settings'][$name] = $item;
				}
			}
		}
		
		if ($default_page != null)
		{
			$item = $default_page;
			$name = $item['name'];
			$item = synved_option_prepare_list_item($id, null, null, $name, $item);
		
			if ($item != null)
			{
				$final_list[$name] = $item;
			}
		}
		
		return $final_list;
	}
	
	return null;
}

function synved_option_prepare_list_item($id, $page, $section, $name, array $item)
{
	global $synved_option_list;
	
	$type = synved_option_item_type($item);
	$sections = isset($item['sections']) ? $item['sections'] : null;
	$settings = isset($item['settings']) ? $item['settings'] : null;
	
	$item['_synved_option_id'] = $id;
	$item['_synved_option_name'] = $name;
	
	if ($type == 'options-page')
	{
		if ($sections != null)
		{
			$list = $sections;
			
			foreach ($list as $child_name => $child_item)
			{
				$child_item = synved_option_prepare_list_item($id, $name, null, $child_name, $child_item);
				
				if ($child_item != null)
				{
					$list[$child_name] = $child_item;
				}
			}
			
			$item['sections'] = $list;
		}
		
		$synved_option_list[$id]['pages'][$name] = $item;
	}
	else if ($type == 'options-section')
	{
		if ($settings != null)
		{
			$list = $settings;
			
			foreach ($list as $child_name => $child_item)
			{
				$child_item = synved_option_prepare_list_item($id, $page, $name, $child_name, $child_item);
				
				if ($child_item != null)
				{
					$list[$child_name] = $child_item;
				}
			}
			
			$item['settings'] = $list;
		}
		
		$synved_option_list[$id]['sections'][$name] = $item;
	}
	else if (in_array($type, array('style', 'script')))
	{
		$synved_option_list[$id]['outputs'][$name] = $item;
	}
	
	return $item;
}

function synved_option_value_list($id)
{
	global $synved_option_list;
	
	if (!isset($synved_option_list[$id]['values']) || $synved_option_list[$id]['values'] == null)
	{
		$options = get_option(synved_option_name_default($id));
		
		if ($options != null && is_array($options))
		{
			$synved_option_list[$id]['values'] = $options;
		}
		else
		{
			return array();
		}
	}
	
	return $synved_option_list[$id]['values'];
}

function synved_option_get($id, $name, $default = null)
{
	$options = synved_option_value_list($id);
	$value = isset($options[$name]) ? $options[$name] : null;
	$item = synved_option_item($id, $name);
	
	if (!isset($options[$name]) && $default !== null)
	{
		$value = $default;
	}
	
	if ($item != null)
	{
		$value = synved_option_item_sanitize_value($id, $name, $value, $item);
	}
	else if ($default !== null)
	{
		$value = $default;
	}
	
	return $value;
}

function synved_option_set($id, $name, $value)
{
	global $synved_option_list;
	
	$options_name = synved_option_name_default($id);
	$options = get_option($options_name);
	$options[$name] = synved_option_item_sanitize_value($id, $name, $value);
	
	update_option($options_name, $options);
	
	unset($synved_option_list[$id]['values']);
}

function synved_option_label_from_id($id)
{
	return ucwords(str_replace('_', ' ', $id));
}

function synved_option_name_default($id)
{
	global $synved_option_list;
	
	$name = $id . '_settings';
	
	if (!isset($synved_option_list[$id]['names'][$name]))
	{
		$synved_option_list[$id]['names'][$name] = array('type' => 'name', 'label' => synved_option_label_from_id($id));
	}
	
	return $name;
}

function synved_option_group_default($id)
{
	global $synved_option_list;
	
	$group = $id . '_settings_group';
	
	if (!isset($synved_option_list[$id]['groups'][$group]))
	{
		$synved_option_list[$id]['groups'][$group] = array('type' => 'group', 'label' => synved_option_label_from_id($id));
	}
	
	return $group;
}

function synved_option_wp_handle_setting($id, $page, $section, $name, $item)
{
	$type = synved_option_item_type($item);
	$hidden = synved_option_item_hidden($item);
	$label = synved_option_item_label($item);
	$sections = isset($item['sections']) ? $item['sections'] : null;
	$settings = isset($item['settings']) ? $item['settings'] : null;
	
	if ($hidden)
	{
		return;
	}
	
	if ($type == 'options-page')
	{
		if ($sections != null)
		{
			$page_slug = synved_option_page_slug($id, $name, $item);
			
			foreach ($sections as $child_name => $child_item)
			{
				synved_option_wp_handle_setting($id, $page_slug, null, $child_name, $child_item);
			}
		}
	}
	else if ($type == 'options-section')
	{
		add_settings_section($name, $label,
			create_function('', 'return synved_option_settings_section_cb(\'' . $name . '\', synved_option_item_find(\'' . $id . '\', \'' . $name . '\'));'),
			$page);
		
		if ($settings != null)
		{
			foreach ($settings as $child_name => $child_item)
			{
				synved_option_wp_handle_setting($id, $page, $name, $child_name, $child_item);
			}
		}
	}
	else
	{
		add_settings_field($name, $label,
			'synved_option_call_array',
			$page, $section, 
			array('synved_option_setting_cb', array($id, $name, $item)));
	}
}

function synved_option_addon_installed($id, $name, $item = null)
{
	$item = synved_option_item($id, $name);
	
	if ($item != null)
	{
		return synved_option_item_addon_is_installed($item);
	}
	
	return false;
}

function synved_option_include_addon_list($path, $filter = null)
{
	$addon_list = synved_plugout_module_addon_scan_path($path, $filter);

	if ($addon_list != null)
	{
		foreach ($addon_list as $addon_name => $addon_file)
		{
			if (file_exists($addon_file))
			{
				include_once($addon_file);
			}
		}
	}
}

function synved_option_include_module_addon_list($module_id, $filter = null)
{
	global $synved_option;
	
	$synved_option['module-addon-list'][] = array('module-id' => $module_id, 'filter' => $filter);
}

function synved_option_init()
{
	global $synved_option_list;
	
	if ($synved_option_list != null)
	{
		foreach ($synved_option_list as $id => $list)
		{
			$items = synved_option_item_list($id);
		}
	}
	
	if ((isset($_POST['action']) && $_POST['action'] == 'synved_option'))
	{
		ob_start();
	}
}

function synved_option_call_array($args)
{
	call_user_func_array($args[0], $args[1]);
}

function synved_option_path_uri($path = null)
{
	$uri = plugins_url('/synved-options') . '/synved-option';
	
	if (function_exists('synved_plugout_module_uri_get'))
	{
		$mod_uri = synved_plugout_module_uri_get('synved-option');
		
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

function synved_option_print_head_outputs()
{
	global $synved_option_list;
	
	foreach ($synved_option_list as $id => $list)
	{
		$items = synved_option_item_list($id);
		$outputs = $list['outputs'];
		
		foreach ($outputs as $name => $item)
		{
			$type = synved_option_item_type($item);
			$mode = synved_option_item_mode($item);
			
			if (in_array('manual', $mode))
			{
				continue;
			}
			
			$content = synved_option_get($id, $name);
			$tag = null;
			$attrs = null;
			
			if ($type == 'style')
			{
				$tag = $type;
				$attrs['type'] = 'text/css';
			}
			else if ($type == 'script')
			{
				$tag = $type;
				$attrs['type'] = 'text/javascript';
				
				$content = '/* <![CDATA[ */' . "\r\n" . $content . "\r\n" . '/* ]]> */';
			}
			
			if ($tag != null)
			{
				echo "\r\n" . '<' . $tag;
				
				foreach ($attrs as $attr_name => $attr_value)
				{
					echo ' ' . $attr_name . '="' . esc_attr($attr_value) . '"';
				}
				
				echo '>';
				echo $content;
				echo '</' . $tag . '>' . "\r\n";
			}
		}
	}
}

function synved_option_wp_after_setup_theme()
{
	global $synved_option;
	
	foreach ($synved_option['module-addon-list'] as $module_addon_load)
	{
		$module_id = $module_addon_load['module-id'];
		$filter = $module_addon_load['filter'];
		
		$addon_list = synved_plugout_module_addon_list($module_id, $filter);
	
		if ($addon_list != null)
		{
			foreach ($addon_list as $addon_name => $addon_file)
			{
				if (file_exists($addon_file))
				{
					include_once($addon_file);
				}
			}
		}
	}
}

function synved_option_wp_init()
{
	synved_option_init();

	if (!is_admin())
	{
		add_action('wp_head', 'synved_option_print_head_outputs');
	}
}

function synved_option_wp_admin_menu()
{
	synved_option_page_add_cb();
}

function synved_option_wp_admin_init()
{
	global $synved_option_list;
	
	if ($synved_option_list != null)
	{
		foreach ($synved_option_list as $id => $list)
		{
			$dbname = synved_option_name_default($id);
			$group = synved_option_group_default($id);

			register_setting($group, $dbname, create_function('$value', 'return synved_option_setting_sanitize_cb(\'' . $id . '\', $value);'));
		
			$items = synved_option_item_list($id);
		
			foreach ($items as $name => $item)
			{
				synved_option_wp_handle_setting($id, null, null, $name, $item);
			}
		}
	}
}

function synved_option_wp_upgrader_source_selection($source, $remote_source, $object = null)
{
	if (is_wp_error($source))
	{
		return $source;
	}

	if ($object != null && $object instanceof Plugin_Upgrader && method_exists($object, 'check_package'))
	{
		$result = $object->check_package($source);
		
		if (is_wp_error($result))
		{
			$folder_name = basename($source);
			$addon_item = synved_option_item_query(null, array(array('type' => 'addon'), array('folder' => $folder_name)));
			
			if ($addon_item != null)
			{
				// XXX fix this $id/$name retrieval...ugly
				$id = $addon_item['_synved_option_id'];
				$name = $addon_item['_synved_option_name'];
				$addon_page = synved_option_item_page($id, $name);
				$page_item = synved_option_item($id, $addon_page);
				$page_label = synved_option_item_label($page_item);
				$page_url = synved_option_item_page_link_url($id, $name);
				
				$source = new WP_Error('synved_option_invalid_plugin_is_addon', sprintf(__('<b>This addon must be installed through the <a href="%s">%s settings page</a>.</b>'), $page_url, $page_label), '');
			}
		}
	}

	return $source;
}

function synved_option_wp_upgrader_pre_install($perform, $extra)
{
	$upgrade_transfer = get_option('synved_option_wp_upgrade_addon_transfer');
	
	if ($upgrade_transfer != null)
	{
		$upgrade_transfer_time = get_option('synved_option_wp_upgrade_addon_transfer_time');
		
		if ($upgrade_transfer_time == null || (time() - $upgrade_transfer_time > (60 * 60 * 1)))
		{
			$upgrade_transfer = null;
			
			update_option('synved_option_wp_upgrade_addon_transfer', '');
		}
	}

	$module_list = array();
	
	if (function_exists('synved_plugout_get_module_list'))
	{
		$module_list = synved_plugout_get_module_list();
	}
	else 
	{
		global $synved_plugout;
	
		$module_list = array_keys($synved_plugout['module-list']);
	}
	
	$plugins_dir = WP_PLUGIN_DIR;
	$plugins_dir = rtrim(str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, realpath($plugins_dir)), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	
	$plugin = $extra['plugin'];
	$plugin_dir = rtrim(str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, realpath(dirname($plugins_dir . $plugin))), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	
	$dir = get_temp_dir();
	$name = time();
	$dir = $dir . wp_unique_filename($dir, $name) . DIRECTORY_SEPARATOR;
	$list = array();
	
	foreach ($module_list as $module_id)
	{
		$addon_list = synved_plugout_module_addon_list($module_id);
	
		if ($addon_list != null)
		{
			foreach ($addon_list as $addon_name => $addon_file)
			{
				if (file_exists($addon_file))
				{
					$addon_dir = dirname($addon_file);
					$parent_dir = dirname($addon_dir);
					
					// clean names for comparison
					$addon_dir = rtrim(str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, realpath($addon_dir)), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
					$parent_dir = rtrim(str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, realpath($parent_dir)), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
					
					if (strtolower($parent_dir) != strtolower($plugins_dir) && strpos(strtolower($addon_dir), strtolower($plugin_dir)) !== false)
					{
						$path = $dir;
						$diff = substr($addon_dir, strlen($plugins_dir));
						$path .= $diff;
						
						wp_mkdir_p($path);
						
						copy_dir($addon_dir, $path);
						
						$list[] = array('original' => $addon_dir, 'temporary' => $path);
					}
				}
			}
		}
	}
	
	if ($list != null)
	{
		update_option('synved_option_wp_upgrade_addon_transfer', array('directory' => $dir, 'list' => $list));
		update_option('synved_option_wp_upgrade_addon_transfer_time', time());
	}
	
	return $perform;
}

function synved_option_wp_upgrader_post_install($perform, $extra, $result = null)
{
	$upgrade_transfer = get_option('synved_option_wp_upgrade_addon_transfer');
	
	if ($upgrade_transfer != null)
	{
		$list = $upgrade_transfer['list'];
		
		foreach ($list as $upgrade_item)
		{
			$original = $upgrade_item['original'];
			$temporary = $upgrade_item['temporary'];
			
			wp_mkdir_p($original);
			
			copy_dir($temporary, $original);
		}
		
		global $wp_filesystem;
		
		if ($wp_filesystem != null)
		{
			$directory = $upgrade_transfer['directory'];
			
			$wp_filesystem->delete($directory, true);
		}
		
		update_option('synved_option_wp_upgrade_addon_transfer', '');
	}
		
	return $perform;
}

function synved_option_wp_plugin_action_links($links, $file)
{
	global $synved_option_list;
	
	if ($synved_option_list != null)
	{
		foreach ($synved_option_list as $id => $list)
		{
			$items = synved_option_item_list($id);
			$pages = $synved_option_list[$id]['pages'];
			
			foreach ($pages as $name => $page)
			{
				$link_label = synved_option_item_property($page, 'link-label');
				$link_target = synved_option_item_property($page, 'link-target');
				$link_url = synved_option_page_link_url($id, $name, $page);
				
				if ($link_label == null)
				{
					$link_label = __('Settings');
				}
				
				if ($file == $link_target) 
				{
					$links[] = '<a href="' . $link_url . '">' . $link_label . '</a>';
				}
			}
		}
	}

	return $links;
}

function synved_option_admin_enqueue_scripts()
{
	$uri = synved_option_path_uri();
	
	wp_register_style('synved-option-jquery-ui', $uri . '/jqueryUI/css/snvdopt/jquery-ui-1.9.2.custom.min.css', false, '1.9.2');
	wp_register_style('synved-option-admin', $uri . '/style/admin.css', array('wp-jquery-ui-dialog', 'synved-option-jquery-ui'), '1.0');
	
	wp_register_script('synved-option-script-custom', $uri . '/script/custom.js', array('jquery', 'suggest', 'media-upload', 'thickbox', 'jquery-ui-core', 'jquery-ui-progressbar', 'jquery-ui-dialog'), '1.0.0');
	wp_localize_script('synved-option-script-custom', 'SynvedOptionVars', array('flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'), 'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'), 'ajaxurl' => admin_url('admin-ajax.php'), 'synvedSecurity' => wp_create_nonce('synved-option-submit-nonce')));
	
	$page = isset($_GET['page']) ? $_GET['page'] : null;
	$enqueue = false;
	
	global $synved_option_list;
	
	if ($synved_option_list != null)
	{
		foreach ($synved_option_list as $id => $list)
		{
			if (isset($list['pages']) && $list['pages'] != null)
			{
				$page_list = $list['pages'];
				
				foreach ($page_list as $name => $page_object)
				{
					if ($page == synved_option_page_slug($id, $name))
					{
						$enqueue = true;
				
						break;
					}
				}
			}
		}
	}
	
	if ($enqueue)
	{
		wp_enqueue_style('thickbox');
		wp_enqueue_style('farbtastic');
		wp_enqueue_style('wp-pointer');
		wp_enqueue_style('synved-option-jquery-ui');
		wp_enqueue_style('synved-option-admin');
	
		wp_enqueue_script('plupload-all');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('suggest');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('farbtastic');
		wp_enqueue_script('synved-option-script-custom');
	}
}

function synved_option_ajax()
{
	check_ajax_referer('synved-option-submit-nonce', 'synvedSecurity');

	if (!isset($_POST['synvedAction']) || $_POST['synvedAction'] == null) 
	{
		return;
	}

	$action = $_POST['synvedAction'];
	$params = isset($_POST['synvedParams']) ? $_POST['synvedParams'] : null;
	$response = null;
	
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
		case 'install-addon':
		{
			if (current_user_can('upload_files') && current_user_can('install_plugins'))
			{
				if (function_exists('synved_option_ajax_type_addon'))
				{
					$response = synved_option_ajax_type_addon($action, $params);
				}
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
	else 
	{
		header('HTTP/1.1 403 Forbidden');
	}

	exit();
}

add_action('after_setup_theme', 'synved_option_wp_after_setup_theme');
add_action('init', 'synved_option_wp_init');
add_filter('upgrader_source_selection', 'synved_option_wp_upgrader_source_selection', 9, 3);
add_filter('upgrader_pre_install', 'synved_option_wp_upgrader_pre_install', 6, 2);
add_filter('upgrader_post_install', 'synved_option_wp_upgrader_post_install', 10, 3);
add_filter('plugin_action_links', 'synved_option_wp_plugin_action_links', 10, 2);

if (is_admin())
{
	add_action('admin_init', 'synved_option_wp_admin_init');
	add_action('admin_menu', 'synved_option_wp_admin_menu');
	add_action('admin_enqueue_scripts', 'synved_option_admin_enqueue_scripts');
	add_action('wp_ajax_synved_option', 'synved_option_ajax');
}

synved_option_include_module_addon_list('synved-option');

?>
