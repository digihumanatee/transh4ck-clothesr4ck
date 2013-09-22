<?php

function synved_option_item_query_into($filter, $list)
{
	if ($list == null)
	{
		return null;
	}
	
	foreach ($list as $item_name => $item)	
	{
		if ($filter != null)
		{
			$filter = $filter;
			$filter_keys = array_keys($filter);
			$filter_name = $filter_keys[0];
			$filter_value = $filter[$filter_keys[0]];
			
			if (is_string($filter_name) || !is_array($filter_value))
			{
				$filter = array($filter);
			}
			
			$found_item = true;
			
			foreach ($filter as $filter_index => $filter_list)
			{
				$pass = false;
				
				foreach ($filter_list as $filter_name => $filter_value)
				{
					if ($filter_name == 'name')
					{
						if ($item_name == $filter_value)
						{
							$pass = true;
						}
					}
					else
					{
						$property = synved_option_item_property($item, $filter_name);
					
						if ($property == $filter_value)
						{
							$pass = true;
						}
					}
				}
				
				$found_item = ($found_item && $pass);
				
				if (!$found_item)
				{
					break;
				}
			}
			
			if ($found_item)
			{
				return $item;
			}
		}
		
		$type = synved_option_item_type($item);
		
		if ($type == 'options-page' && isset($item['sections']) && $item['sections'] != null)
		{
			$ret = synved_option_item_query_into($filter, $item['sections']);
			
			if ($ret != null)
			{
				return $ret;
			}
		}
		else if ($type == 'options-section' && isset($item['settings']) && $item['settings'] != null)
		{
			$ret = synved_option_item_query_into($filter, $item['settings']);
			
			if ($ret != null)
			{
				return $ret;
			}
		}
	}
	
	return null;
}

function synved_option_item_query($id, $filter)
{
	global $synved_option_list;
	
	foreach ($synved_option_list as $list_id => $list)
	{
		if ($id == null || $list_id == $id)
		{
			$items = synved_option_item_list($list_id);
		
			$ret = synved_option_item_query_into($filter, $items);
		
			if ($ret != null || $id != null)
			{
				return $ret;
			}
		}
	}
	
	return null;
}

function synved_option_item_find_into($name, $list)
{
	return synved_option_item_query_into(array('name' => $name), $list);
}

function synved_option_item_find($id, $name)
{
	return synved_option_item_query($id, array('name' => $name));
}

function synved_option_item($id, $name)
{
	return synved_option_item_find($id, $name);
}

function synved_option_item_property($item, $property, $default = null)
{
	$prop = isset($item[$property]) ? $item[$property] : $default;
	
	if ($prop instanceof SynvedOptionCallback)
	{
		$prop = $prop->Invoke(array($default, $item));
	}
	
	return $prop;
}

function synved_option_item_type(array $item)
{
	$type = isset($item['type']) ? $item['type'] : null;
	$default = synved_option_item_default($item);
	$callback = null;
	
	if ($type instanceof SynvedOptionCallback)
	{
		$callback = $type;
		
		$type = null;
	}
	
	if ($type == null && isset($item['sections']))
	{
		$type = 'options-page';
	}
	
	if ($type == null && isset($item['settings']))
	{
		$type = 'options-section';
	}
	
	if ($type == null)
	{
		$type = 'text';
		
		if ($default !== null)
		{
			if (is_bool($default))
			{
				$type = 'boolean';
			}
			else if (is_int($default))
			{
				$type = 'integer';
			}
			else if (is_string($default))
			{
				$type = 'text';
			}
			else if (is_float($default))
			{
				$type = 'decimal';
			}
		}
	}
	
	if ($callback != null)
	{
		return $callback->Invoke(array($type, $item));
	}
	
	return $type;
}

function synved_option_item_mode(array $item)
{
	$mode = isset($item['mode']) ? $item['mode'] : null;
	
	if ($mode instanceof SynvedOptionCallback)
	{
		$mode = $mode->Invoke(array(null, $item));
	}
	
	if ($mode != null && is_string($mode))
	{
		$mode = explode(',', $mode);
	}
	
	if ($mode == null)
	{
		$mode = array();
	}
	
	return $mode;
}

function synved_option_item_style(array $item)
{
	$style = isset($item['style']) ? $item['style'] : null;
	
	if ($style instanceof SynvedOptionCallback)
	{
		$style = $style->Invoke(array(null, $item));
	}
	
	if ($style != null && is_string($style))
	{
		$style = explode(',', $style);
	}
	
	if ($style == null)
	{
		$style = array();
	}
	
	return $style;
}

function synved_option_item_hidden(array $item)
{
	$hidden = isset($item['hidden']) ? $item['hidden'] : null;
	
	if ($hidden instanceof SynvedOptionCallback)
	{
		$hidden = $hidden->Invoke(array(null, $item));
	}
	
	return $hidden;
}

function synved_option_item_label(array $item)
{
	$label = isset($item['label']) ? $item['label'] : null;
	
	if ($label instanceof SynvedOptionCallback)
	{
		$label = $label->Invoke(array(null, $item));
	}
	
	return $label;
}

function synved_option_item_title(array $item)
{
	$title = isset($item['title']) ? $item['title'] : null;
	
	if ($title instanceof SynvedOptionCallback)
	{
		$title = $title->Invoke(array(null, $item));
	}
	
	return $title;
}

function synved_option_item_tip(array $item)
{
	$tip = isset($item['tip']) ? $item['tip'] : null;
	
	if ($tip instanceof SynvedOptionCallback)
	{
		$tip = $tip->Invoke(array(null, $item));
	}
	
	return $tip;
}

function synved_option_item_hint(array $item)
{
	$hint = isset($item['hint']) ? $item['hint'] : null;
	
	if ($hint instanceof SynvedOptionCallback)
	{
		$hint = $hint->Invoke(array(null, $item));
	}
	
	return $hint;
}

function synved_option_item_default(array $item)
{
	$default = isset($item['default']) ? $item['default'] : null;
	
	if ($default instanceof SynvedOptionCallback)
	{
		$default = $default->Invoke(array(null, $item));
	}
	
	return $default;
}

function synved_option_item_role(array $item)
{
	$role = isset($item['role']) ? $item['role'] : null;
	
	if ($role instanceof SynvedOptionCallback)
	{
		$role = $role->Invoke(array(null, $item));
	}
	
	if ($role === null)
	{
		$role = 'manage_options';
	}
	
	return $role;
}

function synved_option_item_parent(array $item)
{
	$parent = isset($item['parent']) ? $item['parent'] : null;
	
	if ($parent instanceof SynvedOptionCallback)
	{
		$parent = $parent->Invoke(array(null, $item));
	}
	
	switch ($parent)
	{
		case 'dashboard':
		{
			$parent = 'index.php';
			
			break;
		}
		case 'posts':
		{
			$parent = 'edit.php';
			
			break;
		}
		case 'media':
		{
			$parent = 'upload.php';
			
			break;
		}
		case 'links':
		{
			$parent = 'link-manager.php';
			
			break;
		}
		case 'pages':
		{
			$parent = 'edit.php?post_type=page';
			
			break;
		}
		case 'comments':
		{
			$parent = 'edit-comments.php';
			
			break;
		}
		case 'appearance':
		{
			$parent = 'themes.php';
			
			break;
		}
		case 'plugins':
		{
			$parent = 'plugins.php';
			
			break;
		}
		case 'users':
		{
			$parent = 'users.php';
			
			break;
		}
		case 'tools':
		{
			$parent = 'tools.php';
			
			break;
		}
		case null:
		case 'settings':
		{
			$parent = 'options-general.php';
			
			break;
		}
	}
	
	return $parent;
}

function synved_option_item_page($id, $name)
{
	$items = synved_option_item_list($id);
	
	if ($items != null)
	{
		foreach ($items as $page_name => $page)
		{
			$sections = isset($page['sections']) ? $page['sections'] : array();
			
			foreach ($sections as $section_name => $section)
			{
				if ($section_name == $name || (isset($section['settings']) && array_key_exists($name, $section['settings'])))
				{
					return $page_name;
				}
			}
		}
	}
	
	return null;
}

function synved_option_item_page_link_url($id, $name)
{
	$page_name = synved_option_item_page($id, $name);
	
	if ($page_name != null)
	{
		return synved_option_page_link_url($id, $page_name);
	}
	
	return null;
}

function synved_option_item_set_parse(array $item, $set)
{
	$type = synved_option_item_type($item);
	preg_match_all('/\\s*(?:(\\d+(?:(?:\\.|(?:\\s*-\\s*))\\d+)*)|([^=,]+))\s*(?:=\s*((?:[^,"]+)|(?:"(?:(?:[^"\\\\])|(?:\\.))*")))?(?:,|$)/', $set, $matches, PREG_SET_ORDER);
	
	$set = array();
	
	foreach ($matches as $match)
	{
		$number = isset($match[1]) ? $match[1] : null;
		$value = isset($match[2]) ? $match[2] : null;
		$label = isset($match[3]) ? $match[3] : null;
		
		if ($number != null && $value == null)
		{
			$value = $number;
		}
		
		$label = trim($label, '"');
		$value = array($value => $label);
		
		if ($number != null)
		{
			$range = explode('-', $number);
			$count = count($range);
			
			if ($count > 1)
			{
				$value_range = array();
			
				for ($i = 0; $i < $count; $i++)
				{
					$range_item = $range[$i];
					$range_value = synved_option_item_sanitize_value_basic($item, $range_item, 0);
			
					if ($range_value == 0)
					{
						$range_value = $range_item;
					}
				
					$value_range[$range_value] = $range_value;
				}
		
				$value = $value_range;
			}
		}
		
		$set[] = $value;
	}
	
	return $set;
}

function synved_option_item_set(array $item)
{
	$set = isset($item['set']) ? $item['set'] : null;
	
	if ($set instanceof SynvedOptionCallback)
	{
		$set = $set->Invoke(array(null, $item));
	}
	
	if ($set != null && !is_array($set))
	{
		$set = synved_option_item_set_parse($item, $set);
	}
	
	return $set;
}

function synved_option_item_callback(array $item, $callback_id, $callback_parameters = null)
{
	$callback = isset($item[$callback_id]) ? $item[$callback_id] : null;
	
	if ($callback != null)
	{
		$callback = trim($callback);
		
		if (is_string($callback) && !function_exists($callback))
		{
			$callback = synved_option_callback_create($callback_code, $callback_parameters);
		}
		
		if (!($callback instanceof SynvedOptionCallback))
		{
			if (is_callable($callback))
			{
				$callback = synved_option_callback($callback);
			}
			else
			{
				$callback = null;
			}
		}
	}
	
	return $callback;
}

function synved_option_item_validate(array $item)
{
	return synved_option_item_callback($item, 'validate', '$value, $name, $id, $item');
}

function synved_option_item_render(array $item)
{
	return synved_option_item_callback($item, 'render', '$value, $params, $name, $id, $item');
}

function synved_option_item_sanitize(array $item)
{
	return synved_option_item_callback($item, 'sanitize', '$value, $name, $id, $item');
}

function synved_option_item_sanitize_raw(array $item)
{
	return synved_option_item_callback($item, 'sanitize-raw', '$value, $name, $id, $item');
}

function synved_option_item_set_check_value(array $item, $set, $value)
{
	if ($set == null)
	{
		return true;
	}
	
	foreach ($set as $set_it)
	{
		if (!is_array($set_it))
		{
			$set_it = array($set_it);
		}
		
		$set_it_keys = array_keys($set_it);
		
		if (isset($set_it_keys[1]))
		{
			if ($value >= $set_it_keys[0] && $value <= $set_it_keys[1])
			{
				return true;
			}
		}
		else if (isset($set_it_keys[0]) && $value == $set_it_keys[0])
		{
			return true;
		}
	}
	
	return false;
}

function synved_option_item_validate_value($id, $name, $value, &$new_value = null, array $item = null)
{
	if ($item == null)
	{
		return null;
	}
	
	$validate = synved_option_item_validate($item);
	$is_valid = true;
	$error = null;
	$error_list = array();
	
	if ($validate != null)
	{
		$new_value = $value;
		
		try
		{
			$validate->Invoke(array($new_value, $name, $id, $item));
		}
		catch (Exception $ex)
		{
			$is_valid = false;
			
			$error = $ex->getMessage();
		}
	}
	
	if (!$is_valid)
	{
		if ($error == null)
		{
			$error = __('Selected value is invalid', 'synved-option');
		}
		
		$error_list[] = array('code' => null, 'type' => null, 'message' => $error);
	}
	
	return $error_list;
}

function synved_option_item_sanitize_value_basic(array $item, $value, $default = null)
{
	$type = synved_option_item_type($item);
	$set = isset($item['set']) ? $item['set'] : null;
	
	if ($default === null)
	{
		$default = synved_option_item_default($item);
	}
	
	switch ($type)
	{
		case 'boolean':
		{
			if ($value === null)
			{
				$value = $default;
			}
			
			$value = $value ? true : 0;
			
			break;
		}
		case 'integer':
		{
			if ($value === null || $value === '')
			{
				$value = $default;
			}
			
			$value = intval($value);
			
			break;
		}
		case 'decimal':
		{
			if ($value === null || $value === '')
			{
				$value = $default;
			}
			
			$value = floatval($value);
			
			break;
		}
		case 'text':
		case 'style':
		case 'script':
		case 'image':
		case 'video':
		{
			$old_value = $value;
			$value = strval($value);
			
			if ($old_value === null || ($value == null && $set != null))
			{
				$value = $default;
			}
			
			break;
		}
		case 'color':
		{
			$value = strval($value);
			
			if ($value == null)
			{
				$value = $default;
			}
			
			break;
		}
	}
	
	return $value;
}

function synved_option_item_sanitize_value($id, $name, $value, array $item = null)
{
	if ($item == null)
	{
		$item = synved_option_item($id, $name);
	}
	
	if ($item == null)
	{
		return null;
	}
	
	$type = synved_option_item_type($item);
	$default = synved_option_item_default($item);
	$set = synved_option_item_set($item);
	$sanitize = synved_option_item_sanitize($item);
	$sanitize_raw = synved_option_item_sanitize_raw($item);
	
	if ($sanitize_raw != null)
	{
		return $sanitize_raw->Invoke(array($value, $name, $id, $item));
	}
	
	$value = synved_option_item_sanitize_value_basic($item, $value, $default);
	$is_valid = true;
	
	if ($set != null)
	{
		if (is_array($value))
		{
			$is_valid = false;
			$new_value = array();
		
			foreach ($value as $single_key => $single_value)
			{
				if (synved_option_item_set_check_value($item, $set, $single_value))
				{
					$new_value[$single_key] = $single_value;
				}
			}
			
			if ($new_value != null)
			{
				$is_valid = true;
				$value = $new_value;
			}
		}
		else
		{
			if (!synved_option_item_set_check_value($item, $set, $value))
			{
				$value = $default;
			}
		}
	}
	
	if ($is_valid)
	{
		if ($sanitize != null)
		{
			$value = $sanitize->Invoke(array($value, $name, $id, $item));
		}
		
		return $value;
	}
	
	return null;
}

function synved_option_item_addon_is_installed(array $item)
{
	if ($item != null)
	{
		$type = synved_option_item_type($item);
		
		if ($type == 'addon')
		{
			$target = synved_option_item_property($item, 'target');
			$folder = synved_option_item_property($item, 'folder');
			
			$path = $target;
			
			if ($path != null)
			{
				$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
				
				if (substr($path, -1) != DIRECTORY_SEPARATOR)
				{
					$path .= DIRECTORY_SEPARATOR;
				}
				
				$path .= $folder;
				
				if (is_dir($path))
				{
					return true;
				}
			}
			
			$module = synved_option_item_property($item, 'module');
			
			if ($module != null)
			{
				$addon_list = synved_plugout_module_addon_list($module);
				
				if (isset($addon_list[$folder]))
				{
					return true;
				}
			}
		}
	}
	
	return false;
}

?>
