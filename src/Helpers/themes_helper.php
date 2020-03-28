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