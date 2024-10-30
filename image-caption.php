<?php
/*
Plugin Name: Image Caption
Plugin URI: http://www.channel-ai.com/blog/plugins/image-caption/
Description: Adds caption under images that have their title or alt defined.
Version: 0.2
Author: Yaosan Yeo
Author URI: http://www.channel-ai.com/blog/
*/

/*  Copyright 2008  Yaosan Yeo  (email : eyn@channel-ai.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// configurable variables

$ic_class = "img";		// CSS class used for image div container
$ic_att	= "title";		// attribute to be used for image caption
$ic_strip_title = 1;	// should "title" attribute be stripped?
$ic_mycss = 0;			// set to 1 to disable default CSS import for this plugin

// end of configurable variables

/////////////////////////////////////////////////////////////////// parse content functions

add_filter('the_content', 'ic_parse');
add_action('wp_head', 'ic_head');

function ic_parse($content)
{
	$content = preg_replace_callback('/(<p>)?(<a[^>]*>)?[\s]?(<img[^>]*[\/]*>)[\s]?(<\/a>)?(<\/p>)?/i', "ic_add", $content);
	return $content;
}

function ic_add($matches)
{
	global $ic_att;
	
	// matches[0]: <p> + <a> + <img> + </a> + </p>
	// matches[1]: <p> (useless)
	// matches[2]: <a href>
	// matches[3]: <img>
	// matches[4]: </a> (useless)
	// matches[5]: </p> (useless)
	$code = $matches[0];
	$anchor = $matches[2];
	$img = $matches[3];
	
	// order makes no difference
	$atts = array('src','width','height','alt','title','class');
	preg_match_all('/(src|width|height|alt|title|class)="([^"]*)"/i', $img, $matches);
	
	// matches is now updated with attributes and values of img tag
	$attributes = $matches[1];
	$values = $matches[2];
	
	// param is an array used to store attribute and value pairs
	$param = array();
	
	// array element index: index of attribute matches index of value
	$i = 0;
	foreach ($attributes as $attribute) {
		foreach ($atts as $att) {
			// test for accepted attributes
			if ( stristr($attribute, $att) )
				$param[$att] = $values[$i];
		}
		$i++;
	}
	
	$param['anchor'] = $anchor;
	
	// is the attribute to be extracted for image caption defined?
	if (!empty($param[$ic_att])) {
		// is width defined?
		if (empty($param['width'])) {
			// no, width is not defined!
			// try to obtain image width from image file
			$image = ic_get_image($param['src']);
			
			if ($image) {
				$param['width'] = imagesx($image);
				$param['height'] = imagesy($image);
				$output = ic_format($param);
			// if not able to, just output original img tag without caption :((
			} else {
				$output = $code;
			}
		
		// yes, width is defined!
		} else {
			$output = ic_format($param);
		}
	// desired attribute undefined or empty, output original code
	} else {
		$output = $code;
	}
	
	return $output;
}

function ic_format($param)
{
	global $ic_class, $ic_att, $ic_strip_title;
	
	$src = 'src="' . $param['src'] . '"';
	$width = 'width="' . $param['width'] . '"';
	$height = (!empty($param['height'])) ? 'height="' . $param['height'] . '"' : '';
	$alt = (!empty($param['alt'])) ? 'alt="' . $param['alt'] . '"': 'alt=""';
	$title = ( (!empty($param['title'])) && !$ic_strip_title ) ? 'title="' . $param['title'] . '"': '';
	$caption = $param[$ic_att];
	$class = $param['class'];
	if (!empty($param['anchor'])) {
		$anchor = $param['anchor'];
		$anchor_close = '</a>';
	} else {
		$anchor = '';
		$anchor_close = '';
	}
	
	ob_start();
	print <<< IMG
<div class="{$ic_class} {$class}" style="width:{$param['width']}px;">
	{$anchor}<img {$src} {$alt} {$width} {$height} {$title}/>{$anchor_close}
	<div>{$caption}</div>
</div>
IMG;
	$output = ob_get_clean();
	
	return $output;
}

function ic_get_image($src) {
	$url = parse_url(get_settings('siteurl'));
	$site = "http://" . $url["host"];	// no trailing slash
	
	// site relative?
	if (substr($src,0,1) == '/')
		$url = $site . $src;
	else
		$url = $src;
	
	require_once(ABSPATH . 'wp-includes/class-snoopy.php');
	
	$s = new Snoopy();
	$result = $s->fetch($url);
	
	$image = @imagecreatefromstring($s->results);

	return $image;
}

function ic_head()
{
	global $ic_mycss;
	
	$path = get_settings('siteurl') . '/wp-content/plugins/image-caption/ic.css';
	if (!$ic_mycss) {
		echo '<link rel="stylesheet" type="text/css" href="' . $path . '" />';
		echo "\n";
	}
}
?>
