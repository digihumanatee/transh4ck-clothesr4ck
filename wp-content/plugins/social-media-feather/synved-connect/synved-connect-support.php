<?php

function synved_connect_support_social_follow_render()
{
	$uri = synved_connect_path_uri();
	
	$out = null;
	
	$out .= '<div class="synved-connect-notice" style="position:fixed; right:30px; top:60px; width:220px; background:#f2f2f2; font-size:110%; color:#444; padding:16px 18px 16px 18px;">';
	
	$out .= '<a href="#" style="font-size:8px; position:absolute; top:0px; right:0px; margin-right: 5px;" onclick="' . esc_attr('jQuery(this).parents(\'.synved-connect-notice\').find(\'.notice-extra\').slideToggle(\'slow\'); return false;') . '">' . __('toggle', 'synved-connect') . '</a>';
	
	$out .= '<div style="padding:8px 10px; border:dotted 1px #bbb;">';
	$out .= '<a style="display:block; line-height:32px; height:32px;" target="_blank" href="https://twitter.com/synved" title="' . __('Follow Us on Twitter!', 'synved-connect') . '"><img style="vertical-align:middle;" alt="twitter" src="' . esc_url($uri . '/image/twitter.png') . '" /><span style="line-height:normal; vertical-align:middle; margin-left:8px;">' . __('Follow Us on Twitter!', 'synved-connect') . '</span></a><div style="font-size:75%; color:#888; line-height:normal; text-align:center; margin-top:5px;">' . __('We only tweet relevant updates!', 'synved-connect') . '</div>';
	$out .= '</div>';

	$out .= '<div class="notice-extra" style="margin:8px 0 0 0; padding:8px 10px; border:dotted 1px #bbb;">';
	$out .= '<a style="display:block; line-height:32px; height:32px;" target="_blank" href="http://synved.com/about/contact/?subject=Quote%20Request" title="' . __('Hire Us!', 'synved-connect') . '"><img style="vertical-align:middle;" alt="hire_us" src="' . esc_url($uri . '/image/hire.png') . '" /><span style="line-height:normal; vertical-align:middle; margin-left:8px;">' . __('Hire Us!', 'synved-connect') . '</span></a><div style="font-size:75%; color:#888; line-height:normal; text-align:center; margin-top:5px;">' . __('For any WordPress development', 'synved-connect') . '</div>';
	$out .= '</div>';

	$out .= '<div class="notice-extra notice-secondary">';
	$out .= '<h4 style="margin:10px 0 -10px 0; padding:0;">News</h4>';
	ob_start();
	wp_widget_rss_output('http://feeds.feedburner.com/SynvedNews?format=xml', array('items' => 4, 'show_author' => 0, 'show_date' => 0, 'show_summary' => 0));
	$out .= ob_get_clean();
	$out .= '</div>';
	
	$sponsor_item = synved_connect_sponsor_item_pick(array('type' => 'intern|extern'));

	if ($sponsor_item != null)
	{
		$out .= '<div class="notice-extra notice-secondary" style="margin:10px 0 0 0; border:dotted 1px #bbb;">';
		$out .= synved_connect_sponsor_content($sponsor_item);
		$out .= '</div>';
	}
	
	$out .= '</div>';
	
	return $out;
}

function synved_connect_support_social_follow_render_small()
{
	$uri = synved_connect_path_uri();
	
	$out = null;
	
	$out .= '<span>';
	$out .= '<a target="_blank" href="https://twitter.com/synved" title="' . __('Follow Us on Twitter!', 'synved-connect') . '"><img alt="twitter" style="vertical-align:middle;" src="' . esc_url($uri . '/image/small/twitter.png') . '"></a></div>';
	$out .= '</span>';
	
	return $out;
}

