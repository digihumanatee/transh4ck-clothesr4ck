<?php

function synved_option_section_default_name($id, $page)
{
	return $page . '_section_general';
}

function synved_option_section_default($id, $page)
{
	$section = synved_option_section_default_name($id, $page);
	
	return array('name' => $section, 'type' => 'options-section', 'label' => __('General Settings', 'synved-option'), 'tip' => __('General Settings for', 'synved-option') . ' ' . synved_option_label_from_id($id));
}

function synved_option_settings_section_cb($name, $item)
{
	$tip = synved_option_item_tip($item);
	
	if ($tip != null)
	{
		echo '<p>' . $tip . '</p>';
	}
}

?>
