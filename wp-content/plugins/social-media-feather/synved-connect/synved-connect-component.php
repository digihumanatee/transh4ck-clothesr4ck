<?php

function synved_connect_component_key($component)
{
	return get_option('synved_connect_key_' . strtolower($component));
}

function synved_connect_component_validate($component, $online_check = false)
{
	return synved_connect_key_component_validate(synved_connect_component_key($component), $component, $online_check);
}

function synved_connect_component_support($component)
{

}

function synved_connect_component_sponsor($component)
{
	$sponsor = get_option('synved_connect_sponsor');
}

?>
