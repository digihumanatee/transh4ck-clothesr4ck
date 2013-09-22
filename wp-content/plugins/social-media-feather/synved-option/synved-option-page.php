<?php

function synved_option_page_default_name($id)
{
	return 'page_settings';
}

function synved_option_page_default($id)
{
	$page = synved_option_page_default_name($id);
	
	return array('name' => $page, 'type' => 'options-page', 'label' => synved_option_label_from_id($id));
}

function synved_option_page_slug($id, $name, $item = null)
{
	if ($item == null)
	{
		$item = synved_option_item($id, $name);
	}
	
	$type = synved_option_item_type($item);
	$parent = synved_option_item_parent($item);

	if ($type == 'options-page')
	{
		global $synved_option_list;
		
		if (isset($synved_option_list[$id]['pages'][$name]['wp-page-slug']))
		{
			return $synved_option_list[$id]['pages'][$name]['wp-page-slug'];
		}
	}
	
	return null;
}
	
function synved_option_page_link_url($id, $name, $item = null)
{
	if ($item == null)
	{
		$item = synved_option_item($id, $name);
	}
	
	$type = synved_option_item_type($item);
	$parent = synved_option_item_parent($item);
	$slug = synved_option_page_slug($id, $name, $item);

	if ($type == 'options-page')
	{
		if ($slug != null)
		{
			return $parent . '?page=' . $slug;
		}
	}
	
	return null;
}

function synved_option_page_cb($id, $name, $item)
{
	$group = synved_option_group_default($id);
	$label = synved_option_item_label($item);
	$title = synved_option_item_title($item);
	$tip = synved_option_item_tip($item);
	$role = synved_option_item_role($item);
	
	if (!current_user_can($role))
	{
		wp_die(__('You do not have sufficient permissions to access this page.', 'synved-option'));
	}
	
	if ($title === null)
	{
		$title = $label;
	}
	
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br/></div>
		<h2><?php echo $title; ?></h2>
		<p><?php echo $tip; ?></p>
		<form action="options.php" method="post">
		<?php settings_fields($group); ?>
		<?php 
			$page_slug = synved_option_page_slug($id, $name, $item);
			synved_option_render_page($page_slug); 
		?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}

function synved_option_page_add($id, $name, $item)
{
	global $synved_option_list;
	
	$type = synved_option_item_type($item);

	if ($type == 'options-page')
	{
		$label = synved_option_item_label($item);
		$tip = synved_option_item_tip($item);
		$parent = synved_option_item_parent($item);
		$role = synved_option_item_role($item);

		if ($label == null)
		{
			$label = $name;
		}
		
		$page_slug = $id . '_' . $name;
		
		$addfunc = 'add_' . /* */ 'subm' . 'enu_page';
		$page = $addfunc($parent, $label, $label, $role, $page_slug, create_function('', 'return synved_option_page_cb(\'' . $id . '\', \'' . $name . '\', synved_option_item_find(\'' . $id . '\', \'' . $name . '\'));'));
		
		$synved_option_list[$id]['pages'][$name]['wp-page-slug'] = $page_slug;
		$synved_option_list[$id]['pages'][$name]['wp-page'] = $page;
		
		return $page;
	}
	
	return null;
}

function synved_option_page_add_cb()
{
	global $synved_option_list;
	
	if ($synved_option_list != null)
	{
		foreach ($synved_option_list as $id => $list)
		{
			$pages = $list['pages'];
		
			foreach ($pages as $name => $item)
			{
				synved_option_page_add($id, $name, $item);
			}
		}
	}
}

?>
