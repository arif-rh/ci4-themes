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
	public $publicDir = 
		[
			'theme_dir'  => 'themes',
			'common_dir' => 'common',
			'css_dir'    => 'css',
			'js_dir'     => 'js',
			'image_dir'  => 'images',
		];

    /**
	 * Namespaced used to locate the view theme files
	 */
	public $namespaceView = '\App\Views\themes';
    
	/**
	 * Main layout templates
	 * will be located inside the ($namespaceView + themeName) directory
	 */
	public $layout = 'main';

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