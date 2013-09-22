<?php
/*
Module Name: Synved Connect
Description: Connect and sync components in a WordPress installation with a remote server
Author: Synved
Version: 1.0.1
Author URI: http://synved.com/
License: GPLv2

LEGAL STATEMENTS

NO WARRANTY
All products, support, services, information and software are provided "as is" without warranty of any kind, express or implied, including, but not limited to, the implied warranties of fitness for a particular purpose, and non-infringement.

NO LIABILITY
In no event shall Synved Ltd. be liable to you or any third party for any direct or indirect, special, incidental, or consequential damages in connection with or arising from errors, omissions, delays or other cause of action that may be attributed to your use of any product, support, services, information or software provided, including, but not limited to, lost profits or lost data, even if Synved Ltd. had been advised of the possibility of such damages.
*/

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-key.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-component.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-credit.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-support.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-connect-sponsor.php');


define('SYNVED_CONNECT_LOADED', true);
define('SYNVED_CONNECT_VERSION', 100000001);
define('SYNVED_CONNECT_VERSION_STRING', '1.0.1');


$synved_connect = array();


function synved_connect_version()
{
	return SYNVED_CONNECT_VERSION;
}

function synved_connect_version_string()
{
	return SYNVED_CONNECT_VERSION_STRING;
}

function synved_connect_object()
{
	global $synved_connect;
	
	return $synved_connect;
}

function synved_connect_server_get()
{
	global $synved_connect;
	
	if (isset($synved_connect['server']))
	{
		return $synved_connect['server'];
	}
	
	return null;
}

function synved_connect_server_set($server)
{
	global $synved_connect;
	
	$synved_connect['server'] = $server;
}

function synved_connect_dashboard_setup()
{
	wp_add_dashboard_widget('synved_connect_dashboard_widget', __('News and Updates <span class="author">by Synved</span>'), 'synved_connect_dashboard_widget');

	global $wp_meta_boxes;
	
	if (isset($wp_meta_boxes['dashboard']['normal']['core']))
	{
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$widget_backup = array('dashboard_right_now' => $normal_dashboard['dashboard_right_now'], 'synved_connect_dashboard_widget' => $normal_dashboard['synved_connect_dashboard_widget']);
		unset($normal_dashboard['dashboard_right_now']);
		unset($normal_dashboard['synved_connect_dashboard_widget']);
		
		$sorted_dashboard = array_merge($widget_backup, $normal_dashboard);
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}
}

//add_filter( 'wp_feed_cache_transient_lifetime', create_function('$a', 'return 1;') );
function synved_connect_dashboard_widget()
{
	$out = null;
	
	$install_date = get_option('synved_connect_install_date', null);
	
	if ($install_date == null)
	{
		update_option('synved_connect_install_date', time());
	}
	
	if ($install_date != null && (time() - $install_date) >= (60 * 60 * 6))
	{
		$sponsor_item = synved_connect_sponsor_item_pick(array('type' => 'intern|extern'));
	
		if ($sponsor_item != null)
		{
			$out .= synved_connect_sponsor_content($sponsor_item);
	
			$out .= '<div>&nbsp;</div>';
		}
	}
	
	$out .= '<div class="rss-widget">';
	ob_start();
	wp_widget_rss_output('http://feeds.feedburner.com/SynvedNews?format=xml', array('items' => 3, 'show_author' => 0, 'show_date' => 0, 'show_summary' => 1));
	$out .= ob_get_clean();
	$out .= '</div>';
	
	echo $out;
}

function synved_connect_path_uri($path = null)
{
	$uri = plugins_url('/synved-wp-connect') . '/synved-connect';
	
	if (function_exists('synved_plugout_module_uri_get'))
	{
		$mod_uri = synved_plugout_module_uri_get('synved-connect');
		
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

function synved_connect_id_get($component = null, $part = null)
{
	$option_key = null;
	
	if ($component != null)
	{
		$option_key = 'component_' . $component;
	}
	else
	{
		$option_key = 'default';
	}
	
	$id = get_option('synved_connect_id_' . $option_key);
	
#	if (is_array($id))
#	{
#		if ($part != null)
#		{
#			if (isset($id[$part]))
#			{
#				return $id[$part];
#			}
#			
#			return null;
#		}
#		
#		return array_shift($id);
#	}
	
	return $id;
}

function synved_connect_id_set($component = null, $sponsor_id)
{
	$option_key = null;
	
	if ($component != null)
	{
		$option_key = 'component_' . $component;
	}
	else
	{
		$option_key = 'default';
	}
	
	return update_option('synved_connect_id_' . $option_key, $sponsor_id);
}


function synved_connect_enqueue_scripts()
{
	$uri = synved_connect_path_uri();
	
	wp_register_style('synved-connect-admin', $uri . '/style/admin.css', false, '1.0');
	
	wp_enqueue_style('synved-connect-admin');
}

function synved_connect_init()
{
	
}

if (is_admin())
{
	add_action('admin_init', 'synved_connect_init');
	//add_action('admin_menu', 'synved_connect_page_add_cb');
	add_action('wp_dashboard_setup', 'synved_connect_dashboard_setup');
	add_action('admin_enqueue_scripts', 'synved_connect_enqueue_scripts');
	//add_action('wp_ajax_synved_connect', 'synved_connect_ajax');
}

?>
