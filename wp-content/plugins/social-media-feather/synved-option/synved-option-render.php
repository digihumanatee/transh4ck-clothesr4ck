<?php

function synved_option_render_field_name($id, $name)
{
	$out_name = synved_option_name_default($id) . '[' . $name . ']';
	
	return $out_name;
}

function synved_option_render_field_id($id, $name)
{
	$out_id = synved_option_name_default($id) . '_' . $name;
	
	return $out_id;
}

// XXX taken from WordPres, clean up...
function synved_option_render_page($page) 
{
	global $wp_settings_sections, $wp_settings_fields;

	if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
		return;

	foreach ( (array) $wp_settings_sections[$page] as $section ) 
	{
		echo "<h3>{$section['title']}</h3>\n";
		call_user_func($section['callback'], $section);
		if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
			continue;
		echo '<table class="form-table">';
		synved_option_render_section($page, $section['id']);
		echo '</table>';
	}
}

// XXX taken from WordPres, clean up...
function synved_option_render_section($page, $section) 
{
	global $wp_settings_fields;

	if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
		return;
		
	$index = 0;

	foreach ((array) $wp_settings_fields[$page][$section] as $field) 
	{
		$callback = $field['callback'];
		$args = $field['args'];
		$id = null;
		$name = null;
		$item = null;
		$class_list = array();
		
		if ($callback == 'synved_option_call_array' && $args[0] == 'synved_option_setting_cb')
		{
			$extra_args = $args[1];
			$id = $extra_args[0];
			$name = $extra_args[1];
			$item = $extra_args[2];
			
			if ($item != null)
			{
				$type = synved_option_item_type($item);
				$style = synved_option_item_style($item);
				
				$class_list[] = 'synved-option-type-' . $type;
				
				if ($style != null)
				{
					foreach ($style as $style_name)
					{
						$class_list[] = 'synved-option-style-' . $style_name;
	
						// XXX exception
						if ($style_name == 'addon-important')
						{
							if ($type == 'addon')
							{
								if (synved_option_item_addon_is_installed($item))
								{
									$class_list[] = 'synved-option-style-' . $style_name . '-installed';
								}
							}
						}
						else if ($style_name == 'group')
						{
							if ($index > 0)
							{
								$class_list[] = 'synved-option-style-' . $style_name . '-active';
							}
						}
					}
				}
			}
		}
		
		if ($class_list != null)
		{
			$class_list = ' class="' . implode(' ', $class_list) . '"';
		}
		
		echo '<tr valign="top"' . $class_list . '>';
		
		if (!empty($field['args']['label_for']))
			echo '<th scope="row"><label for="' . $field['args']['label_for'] . '">' . $field['title'] . '</label></th>';
		else
			echo '<th scope="row">' . $field['title'] . '</th>';
		echo '<td>';
		
		if ($item != null)
		{
			synved_option_render_item($id, $name, $item, true);
		}
		else
		{
			call_user_func($callback, $args);
		}
		
		echo '</td>';
		echo '</tr>';
		
		$index++;
	}
}

function synved_option_render_item($id, $name, $item = null, $render = false, $params = null, $context = null)
{
	if ($item == null)
	{
		$item = synved_option_item($id, $name);
	}
	
	if ($item == null)
	{
		return null;
	}
	
	$value = synved_option_get($id, $name);
	$type = synved_option_item_type($item);
	$style = synved_option_item_style($item);
	$label = synved_option_item_label($item);
	$tip = synved_option_item_tip($item);
	$hint = synved_option_item_hint($item);
	$default = synved_option_item_default($item);
	$set = synved_option_item_set($item);
	$set_is_linear = false;
	
	if ($set != null)
	{
		$set_is_linear = true;
		
		foreach ($set as $set_it)
		{
			if (count($set_it) > 1)
			{
				$set_is_linear = false;
				
				break;
			}
		}
	}
	
	$out_name = synved_option_render_field_name($id, $name);
	$out_id = synved_option_render_field_id($id, $name);
	$out = null;
	
	if (isset($params['output_name']))
	{
		$out_name = $params['output_name'];
	}
	
	if (isset($params['output_id']))
	{
		$out_id = $params['output_id'];
	}
	
	if (isset($params['tip']))
	{
		$tip = $params['tip'];
	}
	
	if (isset($params['default']))
	{
		$default = $params['default'];
	}
	
	if (isset($params['value']))
	{
		$value = $params['value'];
	}
	
	$new_value = $value;
	$error_list = synved_option_item_validate_value($id, $name, $value, $new_value, $item);

	if ($new_value != $value && ($context == null || $context == 'settings'))
	{
		synved_option_set($id, $name, $new_value);
		
		$value = synved_option_get($id, $name);
	}
	
	if ($error_list != null)
	{
		foreach ($error_list as $error)
		{
			$out .= '<div id="message" class="error"><p>For "<i>' . $label . '</i>": ' . $error['message'] . '</p></div>';
		}
	}
	
	if ($set_is_linear)
	{
		$out .= '<select name="' . $out_name . '" id="' . $out_id . '">';
		
		// XXX exception...remove at some point
		if (isset($params['set_before']))
		{
			$set_before = $params['set_before'];
			
			$set = array_merge($set_before, $set);
		}
		
		foreach ($set as $set_it)
		{
			$set_it_keys = array_keys($set_it);
			$selected = $set_it_keys[0] == $value ? ' selected="selected"' : null;
			
			$out .= '<option value="' . $set_it_keys[0] . '"' . $selected . '>' . $set_it[$set_it_keys[0]] . '</option>';
		}
		
		$out .= '</select>';
	}
	else
	{
		$placeholder = null;
		
		if ($hint != null)
		{
			$placeholder = ' placeholder="' . esc_attr($hint) . '"';
		}
		
		switch ($type)
		{
			case 'boolean':
			{
				$checked = $value == true ? ' checked="checked"' : null;
				
				$out .= '<fieldset><legend class="screen-reader-text"><span>' . $label . '</span></legend><label for="' . $out_id . '"><input type="hidden" name="' . $out_name . '" value="0" /><input name="' . $out_name . '" id="' . $out_id . '" type="checkbox" value="1" class="code" ' . $checked . $placeholder . ' /> ' . $label . '</label>&nbsp;&nbsp;<span class="description" style="vertical-align:middle;">' . $tip . '</span></fieldset>';
			
				break;
			}
			case 'text':
			case 'style':
			case 'script':
			case 'image':
			case 'video':
			case 'media':
			{
				$atts = array('name' => $out_name, 'type' => 'text', 'id' => $out_id, 'value' => $value, 'class' => 'regular-text');
				$att_style = array();
				$content = null;
				$tag = 'input';
				$extended = false;
				
				if ($style != null)
				{
					if (in_array('wide', $style))
					{
						$atts['class'] = 'wide-text';
					}
					
					if (in_array('extend', $style))
					{
						$extended = true;
					}
				}
				
				if (in_array($type, array('style', 'script')))
				{
					$extended = true;
					
					$att_style['width'] = '450px';
					$att_style['height'] = '250px';
				}
				
				if ($extended)
				{
					$tag = 'textarea';
				
					if (isset($atts['value']))
					{
						$content = $atts['value'];
					
						unset($atts['value']);
					}
					
					if ($content == null)
					{
						$content = '';
					}
				
					unset($atts['type']);
				}
				
				if ($hint != null)
				{
					$atts['placeholder'] = $hint;
				}
				
				if ($att_style != null)
				{
					$att_css = null;
					
					foreach ($att_style as $style_name => $style_value)
					{
						$att_css .= $style_name . ':' . $style_value . ';';
					}
					
					$atts['style'] = $att_css;
				}
				
				$out .= '<' . $tag;
				
				foreach ($atts as $att_name => $att_value)
				{
					$out .= ' ' . $att_name . '="' . esc_attr($att_value) . '"';
				}
				
				if ($content !== null)
				{
					$out .= '>' . esc_html($content) . '</' . $tag . '>';
				}
				else
				{
					$out .= ' />';
				}
				
				if (in_array($type, array('image', 'video', 'media')))
				{
					$out .= '<input type="hidden" name="' . esc_attr(synved_option_render_field_name($id, $name . '_info_')) . '" value="' . esc_attr($type) . '" />';
					$out .= '&nbsp;&nbsp;<input type="button" class="synved-option-upload-button" value="' . esc_attr(__('Select File', 'synved-option')) . '"' . $placeholder . ' />';
				}
			
				break;
			}
			case 'color':
			{
				$out .= '<div style="position:relative; float: left;">';
				$out .= '<input name="' . $out_name . '" id="' . $out_id . '" type="text" value="' . esc_attr($value) . '" class="code medium-text color-input"' . $placeholder . ' />';
				$out .= '<div class="synved-option-color-input-picker" style="background:white;border:solid 1px #ccc;display:none;position:absolute;top:100%;left:0;z-index:10000;"></div>';
				$out .= '</div>';
			
				break;
			}
			case 'integer':
			case 'decimal':
			{
				$out .= '<input name="' . $out_name . '" id="' . $out_id . '" type="text" value="' . esc_attr($value) . '" class="code small-text"' . $placeholder . ' />';
			
				break;
			}
			case 'user':
			case 'author':
			case 'category':
			case 'page':
			{
				$args = array(
					'echo' => false, 'name' => $out_name, 'id' => $name, 'selected' => $value,
					'show_option_all' => __('Every', 'synved-option') . ' ' . ucfirst($type)
				);
				
				$drop_out = null;
				
				switch ($type)
				{
					case 'author':
					{
						$args['who'] = 'author';
					}
					case 'user':
					{
						$drop_out = wp_dropdown_users($args);
						
						break;
					}
					case 'category':
					{
						$drop_out = wp_dropdown_categories($args);
						
						break;
					}
					case 'page':
					{
						$args['show_option_no_change'] = $args['show_option_all'];
						
						$drop_out = wp_dropdown_pages($args);
						
						break;
					}
				}
				
				$out .= $drop_out;
			
				break;
			}
			case 'tag-list':
			{
				$out .= '<input name="' . $out_name . '" id="' . $out_id . '" type="text" value="' . esc_attr($value) . '" class="regular-text synved-option-tag-selector"' . $placeholder . ' />';
			
				break;
			}
			case 'addon':
			{
				if (function_exists('synved_option_render_type_addon'))
				{
					$out .= synved_option_render_type_addon($id, $name, $item, $out_name, array('out_id' => $out_id, 'label' => $label));
				}
				
				break;
			}
		}
		
		if ($hint != null)
		{
			$out .= ' <span class="snvdopt"><a class="button synved-option-reset-button" title="' . __('Set value to default hinted background value', 'synved-option') . '" style="display: inline-block; padding: 0; vertical-align: middle; cursor: pointer;"><span class="ui-icon ui-icon-arrowrefresh-1-w"> </span></a></span>';
		}
	}
	
	$item_render = synved_option_item_render($item);
	
	if ($item_render != null)
	{
		$error = null;
		$new_out = null;
		
		try
		{
			$params = array('output_name' => $out_name, 'output_id' => $out_id, 'output' => $out, 'set' => $set, 'label' => $label);
			$new_out = $item_render->Invoke(array($value, $params, $name, $id, $item));
		}
		catch (Exception $ex)
		{
			$new_out = null;
			
			$error = $ex->getMessage();
		}
		
		if ($new_out !== null)
		{
			$out = $new_out;
		}
	}
	
	if ($out != null)
	{
		if ($tip != null && $type != 'boolean')
		{
			$tip_class = ' description-' . $type;
			$out .= '&nbsp;&nbsp;<span class="description' . $tip_class . '">' . $tip . '</span>';
		}
		
		if ($render)
		{
			echo $out;
		}
		else
		{
			return $out;
		}
	}
	
	return null;
}

?>
