<?php

// @codeCoverageIgnoreStart
if (! function_exists('link_tag'))
{
// @codeCoverageIgnoreEnd
	// generate link css tags
	function link_tag($css)
	{
		return '<link rel="stylesheet" href="' . $css . '" />' . PHP_EOL;
	}
// @codeCoverageIgnoreStart
}

if (! function_exists('script_tag'))
{
// @codeCoverageIgnoreEnd
	// generate js script tags
	function script_tag($js, $inline = false)
	{
		if ($inline)
		{
			return '<script type="text/javascript">' . PHP_EOL . $js . PHP_EOL . '</script>' . PHP_EOL;
		}
	
		return '<script src="' . $js . '"></script>' . PHP_EOL;
	}
// @codeCoverageIgnoreStart
}

if (! function_exists('validate_ext'))
{
// @codeCoverageIgnoreEnd
	// validate file to have required exyension
	function validate_ext($file, $ext = '.php')
	{
		$fileExt  = pathinfo($file, PATHINFO_EXTENSION);
		return empty($fileExt) ? $file . $ext : $file; 
	}
// @codeCoverageIgnoreStart
}

if (! function_exists('theme_view'))
{
// @codeCoverageIgnoreEnd
	// return full namespace theme view
	function theme_view($view)
	{
		return Arifrh\Themes\Themes::getNamespaceView($view);
	}
// @codeCoverageIgnoreStart
}

if (! function_exists('theme_url'))
{
// @codeCoverageIgnoreEnd
	// return full path from active theme URL
	function theme_url($path = null)
	{
		$tmpVars = Arifrh\Themes\Themes::getVars();

		return $tmpVars['theme_url'] . (is_string($path) ? $path : '');
	}
// @codeCoverageIgnoreStart
}

if (! function_exists('css_url'))
{
// @codeCoverageIgnoreEnd
	// return full path from active css theme URL
	function css_url($path = null)
	{
		$tmpVars = Arifrh\Themes\Themes::getVars();

		return $tmpVars['css_url'] . (is_string($path) ? $path : '');
	}
// @codeCoverageIgnoreStart
}

if (! function_exists('js_url'))
{
// @codeCoverageIgnoreEnd
	// return full path from active js theme URL
	function js_url($path = null)
	{
		$tmpVars = Arifrh\Themes\Themes::getVars();

		return $tmpVars['js_url'] . (is_string($path) ? $path : '');
	}
// @codeCoverageIgnoreStart
}

if (! function_exists('image_url'))
{
// @codeCoverageIgnoreEnd
	// return full path to image URL in active theme
	function image_url($path = null)
	{
		$tmpVars = Arifrh\Themes\Themes::getVars();

		return $tmpVars['image_url'] . (is_string($path) ? $path : '');
	}
// @codeCoverageIgnoreStart
}

if (! function_exists('plugin_url'))
{
// @codeCoverageIgnoreEnd
	// return full path to plugin URL in active theme
	function plugin_url($path = null)
	{
		$tmpVars = Arifrh\Themes\Themes::getVars();

		return $tmpVars['plugin_url'] . (is_string($path) ? $path : '');
	}
// @codeCoverageIgnoreStart
}
// @codeCoverageIgnoreEnd

/**
 * Parse through a JS file and replace language keys with language text values
 *
 * @param string  $file
 * @param mixed[] $langs
 *
 * @return string
 */
function translate(string $file, array $langs = [])
{
	$contents = is_a_file($file) ? file_get_contents($file) : $file;

	preg_match_all("/\{\{(.*?)\}\}/", $contents, $matches, PREG_PATTERN_ORDER);

	if ($matches)
	{
		foreach ($matches[1] as $match)
		{
			$contents = str_replace("{{{$match}}}", isset($langs[trim($match)]) ? $langs[trim($match)] : ('!!Missing translate for ' . $match . '!!'), $contents);
		}
	}

	return $contents;
}

function is_a_file($file, $path = '')
{
	$yes = false;

	if (filter_var($file, FILTER_VALIDATE_URL))
	{
		$yes = @fopen($file, 'r');
	}
	else
	{
		$yes = is_file($path . $file);
	}

	return $yes;
}