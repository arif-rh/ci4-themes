<?php namespace Arifrh\ThemesTest\Config;

class Themes extends \Arifrh\Themes\Config\Themes
{
	public $name = 'custom';

	public $plugins = [
		'some-plugin' => [
			'css' => [
				'some-plugin/plugin.css'
            ],
            'js' => [
				'some-plugin/plugin.js'
			]
        ],
        'other-plugin' => [
			'css' => [
				'other-plugin/missing-plugin.css'
            ],
            'js' => [
				'other-plugin/missing-plugin.js'
			]
		]
	];

}