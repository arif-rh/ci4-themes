<?php namespace App\Controllers;

use Arifrh\Themes\Themes;

class Pages extends BaseController
{
	/**
	 * Simple usage - just replace view iwth Themes::render
	 */
	public function index()
	{
		Themes::render('pages/index');
	}

	/**
	 * Simple usage adding CSS
	 */
	public function css()
	{
		Themes::init()->addCSS('style.css');

		Themes::render('pages/index');
	}

	/**
	 * Simple usage adding JS
	 */
	public function js()
	{
		Themes::init()->addJS('script.js');

		Themes::render('pages/index');
	}

	/**
	 * Simple usage chained method
	 */
	public function chained()
	{
		Themes::init()
			->addCSS('style.css')
			->addJS('script.js');

		Themes::render('pages/index');
	}

	/**
	 * Simple usage inline JS
	 */
	public function inline_js()
	{
		$script = "$(function(){ alert('Inline JS loaded.'); });";

		Themes::init()->addInlineJS($script);

		Themes::render('pages/index');
	}

	/**
	 * Simple usage inline JS
	 */
	public function plugin()
	{
		$script = "$(function(){ bootbox.alert('Bootbox JS loaded.'); });";

		Themes::init()
			->loadPlugins('bootbox')
			->addInlineJS($script);

		Themes::render('pages/index');
	}

	/**
	 * Example set custom page title
	 */
	public function title()
	{
		$data = [
			'page_title' => 'Starter Theme'
		];

		Themes::render('pages/front', $data);
	}

	/**
	 * Example using full template
	 * Full template is view with complete html and body tags
	 */
	public function fulltemplate()
	{
		Themes::init()->useFullTemplate();
		Themes::render('welcome_message');
	}

	/**
	 * Example usage changing theme in the run-time
	 */
	public function theme()
	{
		Themes::init()->setTheme('AdminLTE');
		Themes::render('pages/index');
	}

	/**
	 * Example usage changing theme using config file
	 */
	public function adminlte()
	{
		$config = config('Adminlte');

		Themes::init($config);
		Themes::render('pages/index');
	}
	//--------------------------------------------------------------------

}
