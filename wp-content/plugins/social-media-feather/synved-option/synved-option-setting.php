<?php

function synved_option_setting_sanitize_cb($id, $values)
{
	if ($values != null && is_array($values))
	{		
		foreach ($values as $name => $value)
		{
			$values[$name] = synved_option_item_sanitize_value($id, $name, $values[$name]);
		}
		
		return $values;
	}
	
	return array();
}

function synved_option_setting_cb($id, $name, $item)
{
	return synved_option_render_item($id, $name, $item, true);
}

?>
