<?php namespace Arifrh\Themes\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class ThemesException extends FrameworkException implements ExceptionInterface
{
	public static function forMissingTemplateView(string $template = null)
	{
		return new static(lang('Themes.missingTemplateView', [$template]));
	}

	public static function forPluginNotRegistered(string $plugin = null)
	{
		return new static(lang('Themes.pluginNotRegistered', [$plugin]));
	}

	public static function forPluginNotFound(string $plugin = null)
	{
		return new static(lang('Themes.pluginNotFound', [$plugin]));
	}
}
