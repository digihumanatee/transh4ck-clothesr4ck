<?php

class SynvedConnectKey
{
	const STATE_OK = 0x01;
	const STATE_FAIL = 0x02;
	const STATE_MISMATCH = 0x03;
	const STATE_ONLINE_FAIL = 0x04;
}

function synved_connect_key_item_list($key)
{
	// Note, this is just used to retrieve info from a textual key and is not used yet
	$key_clear = pack('H*', $key);
	$key_items = json_decode($key_clear, true);
	
	return $key_items;
}

function synved_connect_key_item($key, $item_id)
{
	$key_items = synved_connect_key_item_list($key);
	
	if (isset($key_items[$item_id]))
	{
		return $key_items[$item_id];
	}
	
	return null;
}

function synved_connect_key_component($key)
{
	return synved_connect_key_item($key, 'component');
}

function synved_connect_key_domain($key)
{
	return synved_connect_key_item($key, 'domain');
}

function synved_connect_key_owner($key)
{
	return synved_connect_key_item($key, 'owner');
}

function synved_connect_key_item_match($item, $item_list)
{
	if (is_string($item_list))
	{
		$item_key = $item_list;
		
		if ($item == $item_key)
		{
			return true;
		}
		
		$item_key = '/' . $item_key . '/';
		
		if (preg_match($item_key, $item))
		{
			return true;
		}
	}
	else if (is_array($item_list))
	{
		// XXX TODO
	}
	
	return false;
}

function synved_connect_key_component_state($key, $component, $online_check = false)
{
	$key_component = synved_connect_key_component($key);
	$key_domain = synved_connect_key_domain($key);
	
	if (synved_connect_key_item_match($component, $key_component))
	{
		if ($online_check)
		{
			// XXX perform online check
		}
		
		if (synved_connect_key_item_match($_SERVER['SERVER_NAME'], $key_domain))
		{
			return SynvedConnectKey::STATE_OK;
		}
		
		return SynvedConnectKey::STATE_MISMATCH;
	}
	
	return SynvedConnectKey::STATE_FAIL;
}

function synved_connect_key_component_validate($key, $component, $online_check = false)
{
	$component_state = synved_connect_key_component_state($key, $component, $online_check);
	
	if ($component_state == SynvedConnectKey::STATE_OK)
	{
		return true;
	}
	
	return false;
}

?>
