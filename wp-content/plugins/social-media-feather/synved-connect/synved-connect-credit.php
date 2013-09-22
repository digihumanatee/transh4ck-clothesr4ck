<?php

function synved_connect_credit_filter_apply($filter)
{
	if (!is_array($filter))
	{
		$filter = array();
	}
	
	$filter['type'] = 'credit';
	
	return $filter;
}

function synved_connect_credit_list($filter = null)
{
	$filter = synved_connect_credit_filter_apply($filter);
	
	return synved_connect_sponsor_list($filter);
}

function synved_connect_credit_id_pick($filter = null)
{
	$filter = synved_connect_credit_filter_apply($filter);
	
	return synved_connect_sponsor_id_pick($filter);
}

function synved_connect_credit_item_by_id($credit_id, $filter = null)
{
	$filter = synved_connect_credit_filter_apply($filter);
	
	return synved_connect_sponsor_item_by_id($credit_id, $filter);
}

function synved_connect_credit_item_pick($filter = null)
{
	$filter = synved_connect_credit_filter_apply($filter);
	
	return synved_connect_sponsor_item_pick($filter);
}

function synved_connect_credit_item($component = null, $filter = null)
{
	$filter = synved_connect_credit_filter_apply($filter);
	
	return synved_connect_sponsor_item($component, $filter);
}

function synved_connect_credit_link(array $credit_item = null)
{
	return synved_connect_sponsor_link($credit_item);
}

function synved_connect_credit_content(array $credit_item = null)
{
	return synved_connect_sponsor_content($credit_item);
}

?>
