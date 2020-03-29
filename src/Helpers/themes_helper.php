<?php

if (! function_exists('link_tag'))
{
	// generate link css tags
	function link_tag($css)
	{
		return '<link rel="stylesheet" href="' . $css . '" />' . PHP_EOL;
	}
}

if (! function_exists('script_tag'))
{
	// generate js script tags
	function script_tag($js)
	{
		return '<script src="' . $js . '"></script>' . PHP_EOL;
	}
}

if (! function_exists('validate_ext'))
{
	// validate file to have required exyension
	function validate_ext($file, $ext = '.php')
	{
		$fileExt  = pathinfo($file, PATHINFO_EXTENSION);
		return empty($fileExt) ? $file . $ext : $file; 
	}
}

if (! function_exists('theme_url'))
{
	// return full path from active theme URL
	function theme_url($path = null)
	{
		$themeVars = Arifrh\Themes\Themes::getData();

		return $themeVars['theme_url'] . (is_string($path) ? $path : '');
	}
}

if (! function_exists('image_url'))
{
	// return full path to image URL in active theme
	function image_url($path = null)
	{
		$themeVars = Arifrh\Themes\Themes::getData();

		return $themeVars['image_url'] . (is_string($path) ? $path : '');
	}
}

if (! function_exists('plugin_url'))
{
	// return full path to plugin URL in active theme
	function plugin_url($path = null)
	{
		$themeVars = Arifrh\Themes\Themes::getData();

		return $themeVars['plugin_url'] . (is_string($path) ? $path : '');
	}
}