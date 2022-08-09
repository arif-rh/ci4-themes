<?php namespace Arifrh\Themes\Config;

use CodeIgniter\Config\BaseConfig;

class Themes extends BaseConfig
{
    /**
	 * Default Theme name
	 */
	public $name = 'default';

    /**
	 * Public Assets directory
	 */
	public $public_assets = 
		[
			'theme_dir'  => 'themes',
			'css_dir'    => 'css',
			'js_dir'     => 'js',
			'image_dir'  => 'images',
			'plugin_dir' => 'plugins',
		];

    /**
	 * Theme view Path
	 */
	public $theme_path = APPPATH . 'Views/themes';
    
	/**
	 * Theme templates
	 */
	public $templates = 
		[
			'header'   => 'header',
			'footer'   => 'footer',
			'index'    => 'index',
			'fullpage' => false,
		];

	/**
	 * Registered Plugins
	 * Format: 
	 * [ 
	 * 	 'plugin_key_name' => [
	 * 		'js'  => [...js_array]
	 * 		'css' => [...css_array]
	 *   ]
	 * ]
	 * 
	 */
	public $plugins = 
		[
			'bootbox' => [
				'js' => [
					'bootbox/bootbox-en.min.js'
				]
			]
		];
}