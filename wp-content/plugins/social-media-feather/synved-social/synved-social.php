<?php
/*
Module Name: Synved Social
Description: Social sharing and following tools
Author: Synved
Version: 1.4.0
Author URI: http://synved.com/
License: GPLv2

LEGAL STATEMENTS

NO WARRANTY
All products, support, services, information and software are provided "as is" without warranty of any kind, express or implied, including, but not limited to, the implied warranties of fitness for a particular purpose, and non-infringement.

NO LIABILITY
In no event shall Synved Ltd. be liable to you or any third party for any direct or indirect, special, incidental, or consequential damages in connection with or arising from errors, omissions, delays or other cause of action that may be attributed to your use of any product, support, services, information or software provided, including, but not limited to, lost profits or lost data, even if Synved Ltd. had been advised of the possibility of such damages.
*/


define('SYNVED_SOCIAL_LOADED', true);
define('SYNVED_SOCIAL_VERSION', 100040000);
define('SYNVED_SOCIAL_VERSION_STRING', '1.4.0');

define('SYNVED_SOCIAL_ADDON_PATH', str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, dirname(__FILE__) . '/addons'));

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'synved-social-setup.php');


$synved_social = array();


function synved_social_version()
{
	return SYNVED_SOCIAL_VERSION;
}

function synved_social_version_string()
{
	return SYNVED_SOCIAL_VERSION_STRING;
}

class SynvedSocialWidget extends WP_Widget 
{
	function __construct($id_base = false, $name = null, $widget_options = array(), $control_options = array() ) 
	{
		if ($name == null)
		{
			$name = 'Social Media Feather';
		}
		
		parent::__construct($id_base, $name, $widget_options, $control_options);
	}

	function widget( $args, $instance ) 
	{
    extract( $args );  /* before/after widget, before/after title (defined by themes). */
    extract($instance);

    echo $before_widget;
    
    if ($title != null)
    {
    	echo $before_title . $title . $after_title; 
    }

    echo '<div>';
    
    $params = array();
    
    if ($icon_skin != 'default')
    {
    	$params['skin'] = $icon_skin;
    }
    
    if ($icon_size != 'default')
    {
    	$params['size'] = $icon_size;
    }
    
    if ($icon_spacing !== null && $icon_spacing !== '')
    {
    	$params['spacing'] = $icon_spacing;
    }
    
    $this->render_social_markup($params);

    echo '</div>';
    echo $after_widget; 
	}
	
	function get_defaults()
	{
		return array('icon_skin' => 'default', 'icon_size' => 'default', 'icon_spacing' => '');
	}
	
	function render_social_markup($params = null)
	{
	}

	function update($new_instance, $old_instance) 
	{
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['icon_skin'] = strip_tags($new_instance['icon_skin']);
    $instance['icon_size'] = strip_tags($new_instance['icon_size']);
    $instance['icon_spacing'] = strip_tags($new_instance['icon_spacing']);
    
    return $instance;
	}

	function form($instance) 
	{
    $defaults = $this->get_defaults();
    $instance = wp_parse_args((array) $instance, $defaults);
?>
    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo(__('Title', 'synved-social')) ?>:</label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('icon_skin'); ?>"><?php echo(__('Icon Skin', 'synved-social')) ?>:</label>
        <?php 
        $params = array(
        	'tip' => '',
        	'output_name' => $this->get_field_name('icon_skin'),
        	'value' => $instance['icon_skin'],
        	'set_before' => array(array('default' => __('Use Default'))),
       	);
       	
       	$item = synved_option_item('synved_social', 'icon_skin');
       	
       	if ($item != null)
        {
        	// XXX a bit unorthodox...
        	if (is_object($item))
        	{
        		$item = clone $item;
        	}
        	
        	unset($item['render']);
        	
        	synved_option_render_item('synved_social', 'icon_skin', $item, true, $params, 'widget'); 
        }
        ?>
        <br/>
        <label for="<?php echo $this->get_field_id('icon_size'); ?>"><?php echo(__('Icon Size', 'synved-social')) ?>:</label>
        <?php 
        $params = array(
        	'tip' => '',
        	'output_name' => $this->get_field_name('icon_size'),
        	'value' => $instance['icon_size'],
        	'set_before' => array(array('default' => __('Use Default'))),
       	);
        	
        synved_option_render_item('synved_social', 'icon_size', null, true, $params, 'widget'); 
        ?>
        <br/>
        <label for="<?php echo $this->get_field_id('icon_spacing'); ?>"><?php echo(__('Icon Spacing', 'synved-social')) ?>:</label>
        <input type="text" size="3" class="" id="<?php echo $this->get_field_id('icon_spacing'); ?>" name="<?php echo $this->get_field_name('icon_spacing'); ?>" value="<?php echo $instance['icon_spacing']; ?>" />
    </p>
<?php
	}
}

class SynvedSocialShareWidget extends SynvedSocialWidget 
{
	function __construct() 
	{
		parent::__construct('synved_social_share', __('Social Media Feather: Sharing', 'synved-social'));
	}
	
	function get_defaults()
	{
		$defaults = parent::get_defaults();
		
		return array_merge($defaults, array('title' => __('Sharing', 'synved-social')));
	}
	
	function render_social_markup($params = null)
	{
		echo synved_social_share_markup(null, null, $params);
	}
}

class SynvedSocialFollowWidget extends SynvedSocialWidget 
{
	function __construct() 
	{
		parent::__construct('synved_social_follow', __('Social Media Feather: Follow Us', 'synved-social'));
	}
	
	function get_defaults()
	{
		$defaults = parent::get_defaults();
		
		return array_merge($defaults, array('title' => __('Follow Us', 'synved-social')));
	}
	
	function render_social_markup($params = null)
	{
		echo synved_social_follow_markup(null, null, $params);
	}
}


function synved_social_service_provider_list($context, $raw = false)
{
	$provider_list = array();
	
	if ($context == 'share')
	{
		$provider_list = array(
			'facebook' => array(
				'link' => 'http://www.facebook.com/sharer.php?u=%%url%%&t=%%title%%',
				'title' => __('Share on Facebook')
			),
			'twitter' => array(
				'link' => 'http://twitter.com/share?url=%%url%%&text=%%message%%',
				'title' => __('Share on Twitter'),
			),
			'google_plus' => array(
				'link' => 'https://plus.google.com/share?url=%%url%%',
				'title' => __('Share on Google+'),
			),
			'reddit' => array(
				'link' => 'http://www.reddit.com/submit?url=%%url%%&title=%%title%%',
				'title' => __('Share on Reddit'),
			),
			'pinterest' => array(
				'link' => 'http://pinterest.com/pin/create/button/?url=%%url%%&media=%%image%%&description=%%title%%',
				'title' => __('Pin it with Pinterest'),
			),
			'linkedin' => array(
				'link' => 'http://www.linkedin.com/shareArticle?mini=true&url=%%url%%&title=%%title%%',
				'title' => __('Share on Linkedin'),
			),
			'tumblr' => array(
				'link' => 'http://tumblr.com/share?s=&v=3&t=%%title%%&u=%%url%%',
				'title' => __('Share on tumblr'),
				'default-display' => false,
			),
			'mail' => array(
				'link' => 'mailto:?subject=%%title%%&body=%%message%%:%20%%url%%',
				'title' => __('Share by email'),
			),
		);
	}
	else if ($context = 'follow')
	{
		$provider_list = array(
			'facebook' => array(
				'link' => 'http://www.facebook.com/MyAvatarName',
				'title' => __('Follow us on Facebook'),
			),
			'twitter' => array(
				'link' => 'http://twitter.com/MyAvatarName',
				'title' => __('Follow us on Twitter'),
			),
			'google_plus' => array(
				'link' => 'http://plus.google.com/needlessly_long_google_plus_id',
				'title' => __('Follow us on Google+'),
			),
			'pinterest' => array(
				'link' => 'http://pinterest.com/MyUserName/',
				'title' => __('Our board on Pinterest'),
				'default-display' => false,
			),
			'linkedin' => array(
				'link' => 'http://www.linkedin.com/in/yourid',
				'title' => __('Find us on Linkedin'),
			),
			'rss' => array(
				'label' => 'RSS',
				'link' => 'http://feeds.feedburner.com/MyFeedName',
				'title' => __('Subscribe to our RSS Feed'),
			),
			'youtube' => array(
				'link' => 'http://www.youtube.com/MyYouTubeName',
				'title' => __('Find us on YouTube'),
			),
			'vimeo' => array(
				'link' => 'http://vimeo.com/MyVimeoName',
				'title' => __('Find us on vimeo'),
				'default-display' => false,
			),
			'tumblr' => array(
				'link' => 'http://myname.tumblr.com',
				'title' => __('Find us on tumblr'),
				'default-display' => false,
			),
		);
	}
	
	$return_list = $provider_list;
	
	if ($raw == false)
	{
		$return_list = array();
		
		foreach ($provider_list as $provider_name => $provider_item)
		{
			$display = synved_option_get('synved_social', $provider_name . '_display');
			$link = synved_option_get('synved_social', $provider_name . '_' . $context . '_link');
			$title = synved_option_get('synved_social', $provider_name . '_' . $context . '_title');
		
			if ($display === null || in_array($display, array($context, 'both')))
			{
				if ($link != null)
				{
					$provider_item['link'] = $link;
				}
			
				if ($title != null)
				{
					$provider_item['title'] = $title;
				}
			
				$return_list[$provider_name] = $provider_item;
			}
		}
	}
	
	return apply_filters('synved_social_service_provider_list', $return_list, $context, $raw);
}

function synved_social_icon_skin_list()
{
	$path = synved_social_path();
	$uri = synved_social_path_uri();
	
	$icons = array(
		'regular' => array(
			'label' => __('Regular'), 
			'image' => $uri . '/image/social/regular/preview.png', 
			'folder' => '/image/social/regular/', 
			'path' => $path . '/image/social/regular/', 
			'uri' => $uri . '/image/social/regular/'
		)
	);
	
	$icons_extra = null;
	
	if (function_exists('synved_social_addon_extra_icons_get'))
	{
		$icons_extra = synved_social_addon_extra_icons_get();
		$icons = array_merge($icons, $icons_extra);
	}
	
	return apply_filters('synved_social_icon_skin_list', $icons);;
}

function synved_social_icon_skin_get($name = null)
{
	$icons = synved_social_icon_skin_list();
	
	if ($name != null && !isset($icons[$name]))
	{
		foreach ($icons as $skin_name => $skin)
		{
			if (strtolower($name) == strtolower($skin['label']))
			{
				$name = $skin_name;
				
				break;
			}
		}
	}
	
	if ($name == null || !isset($icons[$name]))
	{
		$selected = synved_option_get('synved_social', 'icon_skin');
		
		$name = $selected;
	}
	
	if (isset($icons[$name]))
	{
		return $icons[$name];
	}
	
	if (isset($icons['regular']))
	{
		return $icons['regular'];
	}
	
	return null;
}

function synved_social_icon_skin_current()
{
	return synved_social_icon_skin_get();
}

function synved_social_icon_skin_get_size_list($skin)
{
	$path = synved_social_path();
	$skin_path = isset($skin['path']) ? $skin['path'] : ($path . '/image/social/regular/');
	
	$sizes = glob($skin_path . '*', GLOB_ONLYDIR);
	$sizes = array_map('basename', $sizes);
	$size_list = array();
	
	foreach ($sizes as $size_dir)
	{
		$size_parts = explode('x', $size_dir);
		$size_width = (int) $size_parts[0];
		
		if ($size_width != null)
		{
			$size_list[] = $size_width;
		}
	}
	
	sort($size_list, SORT_NUMERIC);
	
	return $size_list;
}

function synved_social_icon_skin_get_image_list_raw($skin, $name_list, $forced_size = null)
{
	$path = synved_social_path();
	$uri = synved_social_path_uri();
	$skin_default = synved_social_icon_skin_get('regular');
	
	$skin_default_path = isset($skin_default['path']) ? $skin_default['path'] : ($path . '/image/social/regular/');
	$skin_default_uri = isset($skin_default['uri']) ? $skin_default['uri'] : ($uri . '/image/social/regular/');
	$skin_sel_path = isset($skin['path']) ? $skin['path'] : ($path . '/image/social/regular/');
	$skin_sel_uri = isset($skin['uri']) ? $skin['uri'] : ($uri . '/image/social/regular/');
	
	$default_size_list = synved_social_icon_skin_get_size_list($skin_default);
	$sel_size_list = synved_social_icon_skin_get_size_list($skin);
	
	$skin_list = array(
		array(
			'path' => $skin_sel_path,
			'uri' => $skin_sel_uri,
			'list' => $sel_size_list,
		),
		array(
			'path' => $skin_default_path,
			'uri' => $skin_default_uri,
			'list' => $default_size_list,
		),
	);
	
	$image_list = array();
	
	foreach ($name_list as $name)
	{
		foreach ($skin_list as $skin)
		{
			$skin_path = $skin['path'];
			$skin_uri = $skin['uri'];
			$size_list = $skin['list'];
			$size_count = count($size_list);
			
			$image_size_list = array();
			$image_size_list_skipped = array();
		
			for ($i = 0; $i < $size_count; $i++)
			{
				$image_size = $size_list[$i];
				$image_sub = '/' . $name . '.png';
				$size_name = $image_size . 'x' . $image_size;
				$image_path = $skin_path . $size_name . $image_sub;
				$image_uri = $skin_uri . $size_name . $image_sub;
		
				if (file_exists($image_path))
				{
					$image_size_meta = array(
						'name' => $size_name,
						'sub' => $image_sub,
						'path' => $image_path,
						'uri' => $image_uri,
					);
				
					foreach ($image_size_list_skipped as $image_size_skipped)
					{
						$image_size_list[$image_size_skipped] = $image_size_meta;
					}
				
					$image_size_skipped = array();
					$image_size_list[$image_size] = $image_size_meta;
				
					if ($forced_size != null && ($image_size > $forced_size || $i == $size_count - 1) && !isset($image_size_list[$forced_size]))
					{
						$image_size_list[$forced_size] = $image_size_meta;
					}
				}
				else
				{
					$image_size_skipped[] = $image_size;
				}
			}
		
			if ($image_size_list != null)
			{
				break;
			}
		}
		
		$image_list[$name] = $image_size_list;
	}
	
	return $image_list;
}

function synved_social_icon_skin_get_image_list($skin, $name_list, $forced_size = null)
{
	$image_list = synved_social_icon_skin_get_image_list_raw($skin, $name_list, $forced_size);
	$image_list = apply_filters('synved_social_skin_image_list', $image_list, $skin, $name_list, $forced_size);
	
	return $image_list;
}

function synved_social_button_list_shortcode($atts, $content = null, $code = '', $context = null)
{
	$vars_def = array('url' => null, 'title' => null);
	$params_def = array('skin' => null, 'size' => null, 'spacing' => null, 'class' => null, 'show' => null, 'hide' => null, 'prompt' => null, 'custom1' => null, 'custom2' => null, 'custom3' => null);
	$vars = shortcode_atts($vars_def, $atts);
	$params = shortcode_atts($params_def, $atts);
	$vars = array_filter($vars);
	$params = array_filter($params);
	
	if ($context == 'share')
	{
		return synved_social_share_markup($vars, null, $params);
	}
	else if ($context == 'follow')
	{
		return synved_social_follow_markup($vars, null, $params);
	}
	
	return null;
}

function synved_social_share_shortcode($atts, $content = null, $code = '')
{
	return synved_social_button_list_shortcode($atts, $content, $code, 'share');
}

function synved_social_follow_shortcode($atts, $content = null, $code = '')
{
	return synved_social_button_list_shortcode($atts, $content, $code, 'follow');
}

function synved_social_button_list_markup_item_out($out_item)
{
	$out = null;
	$tag = isset($out_item['tag']) ? $out_item['tag'] : null;
	$content = isset($out_item['content']) ? $out_item['content'] : null;
	$list = isset($out_item['child-list']) ? $out_item['child-list'] : null;
	
	unset($out_item['tag']);
	unset($out_item['content']);
	unset($out_item['child-list']);
	
	if ($tag != null)
	{
		$out .= '<' . $tag;
		
		foreach ($out_item as $attr_name => $attr_value)
		{
			if ($attr_name != null && $attr_value !== null)
			{
				if (in_array($attr_name, array('href', 'src')))
				{
					$attr_value = esc_url($attr_value);
				}
				else
				{
					$attr_value = esc_attr($attr_value);
				}
			
				$out .= ' ' . $attr_name . '="' . $attr_value . '"';
			}
		}
		
		if ($list != null || $content != null)
		{
			$out .= '>';
		}
		else
		{
			$out .= ' />';
		}
	}
	
	if ($list != null)
	{
		foreach ($list as $child)
		{
			$out .= synved_social_button_list_markup_item_out($child);
		}
	}
	
	if ($content != null)
	{
		$out .= $content;
	}

	if ($tag != null && ($list != null || $content != null))
	{
		$out .= '</' . $tag . '>';
	}
	
	return $out;
}

function synved_social_button_list_markup($context, $vars = null, $buttons = null, $params = null)
{
	$buttons_default = synved_social_service_provider_list($context);
	
	if ($buttons == null)
	{
		$buttons = $buttons_default;
	}
	else
	{
		$keys = array_keys($buttons);
		
		foreach ($keys as $key)
		{
			if ($buttons[$key] == null && isset($buttons_default[$key]))
			{
				$buttons[$key] = $buttons_default[$key];
			}
		}
	}
	
	$id = get_the_ID();
	
	if ($id == null)
	{
		global $post;
	
		$id = $post->ID;
	}
	
	if (!isset($vars['url']))
	{
		$full_url = synved_option_get('synved_social', 'share_full_url');
		$url = home_url($_SERVER['REQUEST_URI']);
		
		if ($id != null && in_the_loop())
		{
			$post_full_url = strtolower(get_post_meta($id, 'synved_social_share_full_url', true));
			
			if ($post_full_url != null)
			{
				if ($post_full_url == 'yes')
				{
					$full_url = true;
				}
				else if ($post_full_url == 'no')
				{
					$full_url = false;
				}
			}
		}
		
		if (!$full_url)
		{
			if ($id != null && in_the_loop())
			{
				$use_shortlinks = synved_option_get('synved_social', 'use_shortlinks');
				$url = get_permalink();
		
				if ($use_shortlinks && function_exists('wp_get_shortlink')) 
				{
					$short = wp_get_shortlink($id);
			
					if ($short != null)
					{
						$url = $short;
					}
				}
			}
		}
		
		$vars['url'] = $url;
	}
	
	if (!isset($vars['image']))
	{
		$image_src = null;
		
		if ($id != null)
		{
			$src = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
			$image_src = $src[0];
		}
		
		$vars['image'] = $image_src;
	}
	
	if (!isset($vars['title']))
	{
		$vars['title'] = get_the_title();
	}
	
	if (!isset($vars['message']))
	{
		$message = synved_option_get('synved_social', 'share_message_default');
		
		if ($message == null)
		{
			$message = __('Hey check this out', 'synved-social');
		}
		
		$vars['message'] = $message;
	}
	
	if (isset($params['class']) && !is_array($params['class']))
	{
		$class = explode(' ', $params['class']);
		$params['class'] = array_map('trim', $class);
	}
	
	if (isset($params['show']) && !is_array($params['show']))
	{
		$show = explode(',', $params['show']);
		$params['show'] = array_map('trim', $show);
	}
	
	if (isset($params['hide']) && !is_array($params['hide']))
	{
		$hide = explode(',', $params['hide']);
		$params['hide'] = array_map('trim', $hide);
	}
	
	$vars = apply_filters('synved_social_markup_variable_list', $vars, $context, $params);
	$params = apply_filters('synved_social_markup_parameter_list', $params, $context, $vars);
	
	if ($vars != null)
	{
		$vars = urlencode_deep($vars);
		
		// urlencode converts space characters to + rather than %20 which messes things up
		$vars['message'] = str_replace('+', '%20', $vars['message']);
		$vars['title'] = str_replace('+', '%20', $vars['title']);
	}
	
	$path = synved_social_path();
	$uri = synved_social_path_uri();
	$skin = synved_social_icon_skin_current();
	
	if (isset($params['skin']))
	{
		$skin = synved_social_icon_skin_get($params['skin']);
	}
	
	$skin_path = isset($skin['path']) ? $skin['path'] : ($path . '/image/social/regular/');
	$skin_uri = isset($skin['uri']) ? $skin['uri'] : ($uri . '/image/social/regular/');
	
	$icon_size = synved_option_get('synved_social', 'icon_size');
	$size = 48;
	
	if ($icon_size != null)
	{
		$size = $icon_size;
	}
	
	if (isset($params['size']))
	{
		$size = $params['size'];
		
		if (is_string($size))
		{
			$size = strtolower($size);
			$size_parts = explode('x', $size);
			$size = (int) $size_parts[0];
		}
	}
	
	$icon_spacing = synved_option_get('synved_social', 'icon_spacing');
	$spacing = 5;
	
	if ($icon_spacing != null)
	{
		$spacing = $icon_spacing;
	}
	
	if (isset($params['spacing']))
	{
		$spacing = $params['spacing'];
	}
	
	$class = isset($params['class']) ? $params['class'] : null;
	$show = isset($params['show']) ? $params['show'] : null;
	$hide = isset($params['hide']) ? $params['hide'] : null;
	
	if ($show != null)
	{
		$button_list = array();
		
		foreach ($show as $button_key)
		{
			if (isset($buttons[$button_key]))
			{
				$button_list[$button_key] = $buttons[$button_key];
				
				unset($buttons[$button_key]);
			}
		}
		
		foreach ($buttons as $button_key => $button_item)
		{
			$button_list[$button_key] = $button_item;
		}
		
		$buttons = $button_list;
	}
	
	if ($hide != null)
	{
		foreach ($hide as $button_key)
		{
			if (isset($buttons[$button_key]))
			{
				unset($buttons[$button_key]);
			}
		}
	}
	
	$out_list = array();
	$out_params = array();
	$image_list = array();
	$icon_resolution = synved_option_get('synved_social', 'icon_resolution');
	$resolutions = array('normal' => $size, 'hidef' => $size * 2);
	
	if ($icon_resolution == 'single')
	{
		$resolutions = array('single' => $size * 2);
	}
	
	foreach ($resolutions as $resolution_name => $resolution_size)
	{
		$image_list[$resolution_name] = synved_social_icon_skin_get_image_list($skin, array_keys($buttons), $resolution_size);
	}
	
	$index = 0;
	$count = count($buttons);
	
	foreach ($buttons as $button_key => $button_item)
	{
		$href = $button_item['link'];
		$title = $button_item['title'];
		
		$matches = null;
	
		if (preg_match_all('/%%(\\w+)%%/', $href, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				$var_key = $match[1];
				$replace = null;
	
				if (isset($vars[$var_key]))
				{
					$replace = $vars[$var_key];
				}
				
				$href = str_replace($match[0], $replace, $href);
			}
		}
		
		$icon_sizes = $resolutions;
		
		foreach ($icon_sizes as $icon_def => $icon_size)
		{
			$image = $image_list[$icon_def][$button_key];
			$image_size = $image[$icon_size];
			$image_sub = $image_size['sub'];
			$image_path = $image_size['path'];
			$image_uri = $image_size['uri'];
		
			if (!file_exists($image_path))
			{
				$size_list = array_keys($image);
				$image_path = apply_filters('synved_social_button_image_path', $image_path, $image_uri, $icon_size, $image_sub, $skin_path, $skin_uri, $size_list);
				$image_uri = apply_filters('synved_social_button_image_uri', $image_uri, $image_path, $icon_size, $image_sub, $skin_path, $skin_uri, $size_list);
			}
		
			$style = 'margin:0;';
		
			if (true)
			{
				$style .= 'margin-bottom:' . $spacing . 'px;';
			}
		
			if ($index < $count - 1)
			{
				$style .= 'margin-right:' . $spacing . 'px;';
			}
		
			$class_extra = null;
		
			if ($class != null)
			{
				$class_extra = ' ' . implode(' ', $class);
			}
		
			$out_button = array(
				'tag' => 'a',
				'class' => 'synved-social-button synved-social-button-' . $context .  ' synved-social-size-' . $size .  ' synved-social-resolution-' . $icon_def . ' synved-social-provider-' . $button_key . $class_extra,
				'data-provider' => $button_key,
				'target' => $button_key != 'mail' ? '_blank' : null,
				'rel' => 'nofollow',
				'title' => $title,
				'href' => $href,
				'style' => 'font-size: 0px; width:' . $size . 'px;' . 'height:' . $size . 'px;' . $style,
				'child-list' => array(
					array(
						'tag' => 'img',
						'alt' => $button_key,
						'title' => $title,
						'class' => 'synved-share-image synved-social-image synved-social-image-' . $context,
						'width' => $size,
						'height' => $size,
						'style' => 'display: inline; width:' . $size . 'px;' . 'height:' . $size . 'px; margin: 0; padding: 0; border: none; box-shadow: none;',
						'src' => $image_uri,
					)
				)
			);
			
			$out_list[$icon_def][$button_key] = $out_button;
			$out_params[$icon_def][$button_key] = array('icon-resolution' => $icon_def);
		}
		
		$index++;
	}
	
	$out = null;
	
	if ($out_list != null)
	{
		foreach ($out_list as $def_key => $def_list)
		{
			$out_list[$def_key] = apply_filters('synved_social_button_list_markup', $def_list, $out_params[$def_key], $context, $vars, $params);
		}
	}
	
	if ($out_list != null)
	{
		foreach ($out_list as $def_key => $def_list)
		{
			foreach ($def_list as $button_key => $out_item)
			{
				$out .= synved_social_button_list_markup_item_out($out_item);
			}
		}
		
		if (synved_option_get('synved_social', 'show_credit'))
		{
			$out .= '<a class="synved-social-credit" target="_blank" rel="nofollow" title="' . __('WordPress Social Media Feather', 'synved-social') . '" href="http://synved.com/wordpress-social-media-feather/" style="color:#444; text-decoration:none; font-size:8px; margin-left:5px;vertical-align:10px;white-space:nowrap;"><span>' . __('by ', 'synved-social') . '</span><img style="display: inline;margin:0;padding:0;width:16px;height:16px;" width="16" height="16" alt="feather" src="' . $uri . '/image/icon.png" /></a>';
		}
	}
	
	return $out;
}

function synved_social_share_markup($vars = null, $buttons = null, $params = null)
{
	return synved_social_button_list_markup('share', $vars, $buttons, $params);
}

function synved_social_follow_markup($vars = null, $buttons = null, $params = null)
{
	return synved_social_button_list_markup('follow', $vars, $buttons, $params);
}

