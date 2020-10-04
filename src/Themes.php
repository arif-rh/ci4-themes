<?php 

	/**
	 * Themes Library for CodeIgniter 4
	 *
	 * This is an open source themes management for CodeIgniter 4
	 *
	 * @author    Arif Rahman Hakim
	 * @copyright Copyright (c) 2020, Arif Rahman Hakim  (http://github.com/arif-rh)
	 * @license   BSD - http://www.opensource.org/licenses/BSD-3-Clause
	 * @link      https://github.com/arif-rh/ci4-themes
	 * @version   0.0.1
	 */

namespace Arifrh\Themes;

use Arifrh\Themes\Exceptions\ThemesException;

/**
 * Class Themes
 *
 * @package Arifrh\Themes
 */
class Themes 
{
	/**
	 * Constant of key for css themes
	 */
	const CSS_THEME = 'css_themes';

	/**
	 * Constant of key for external css
	 */
	const EXTERNAL_CSS = 'external_css';

	/**
	 * Constant of key for js themes
	 */
	const JS_THEME = 'js_themes';

	/**
	 * Constant of key for external js
	 */
	const EXTERNAL_JS = 'external_js';

	/**
	 * Constant of key for inline js
	 */
	const INLINE_JS = 'inline_js';

	/**
	 * Constant of key for loaded plugin
	 */
	const LOADED_PLUGIN = 'loaded_plugins';

	/**
	 * Constant of variable that will be used as page title inside template
	 */
	const PAGE_TITLE = 'page_title';

	/**
	 * Constant of variable that will be used as content inside template
	 */
	const CONTENT = 'content';
	
	/**
	 * Themes instance 
	 *
	 * @var    object||null
	 * @access private
	 */
	private static $instance = null;

	/**
	 * Theme variables - store variables to be used in template file
	 *
	 * @var    array
	 * @access protected
	 */
	protected static $themeVars = [];

	/**
	 * Themes Configuration - Used from \Arifrh\Config\Themes but can be overiden in the run-time
	 *
	 * @var    array
	 * @access protected
	 */
	protected static $config = [];

	/**
	 * Intantiate Themes with default config 
	 *
	 * @param  \Config\Themes    $config
	 * 
	 * @return void
	 */
	public static function init($config = null)
	{
		if (self::$instance === null)
		{
			self::$instance = new self;
		}

		if (is_null($config))
		{
			$config = config('Themes');
		}

		self::$instance::$themeVars = null;

		self::$config = (array) $config;

		// define constant for config reference key var
		foreach($config as $theme_key => $theme_value)
		{
			$constant = strtoupper($theme_key);

			if (!defined($constant))
			{
				define($constant, $theme_key);
			}
		}

		self::$instance->setTheme(self::$config[THEME]);

		return self::$instance;
	}

	/**
	 * add css file(s) to be loaded inside template
	 *
	 * @param   string||array   $css_files
	 * 
	 *@return $this Arifrh\Themes\Themes
	 */	
	public function addCSS($css_files = [])
	{
		$css_files = is_array($css_files) ? $css_files : explode(',', $css_files);

		foreach ($css_files as $css)
		{
			$css = trim($css);

			if (!empty($css))
			{
				// set unique key-index to prevent duplicate css being included
				self::$themeVars[self::CSS_THEME][sha1($css)] = $css;
			}			
		}

		return $this;
	}

	/**
	 * add js file(s) to be loaded inside template
	 *
	 * @param   string||array   $js_files
	 * 
	 *@return $this Arifrh\Themes\Themes
	 */
	public function addJS($js_files)
	{
		$js_files = is_array($js_files) ? $js_files : explode(',', $js_files);

		foreach ($js_files as $js)
		{
			$js = trim($js);

			if (!empty($js))
			{
				// set unique key-index to prevent duplicate js being included
				self::$themeVars[self::JS_THEME][sha1($js)] = $js;
			}
		}

		return $this;
	}

	/**
	 * Adding inline JS to the template
	 *  
	 * @param string $js_scripts
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */ 
	public function addInlineJS($js_scripts)
	{
		$js = trim($js_scripts);

		if (!empty($js))
		{
			self::$themeVars[self::INLINE_JS][sha1($js)] = $js;
		}

		return $this;
	}

	/**
	 * Adding i18n JS to the template
	 *  
	 * @param string  $js_scripts
	 * @param mixed[] $langs
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */ 
	public function addI18nJS(string $js_scripts, array $langs = [])
	{
		helper('themes');

		$js = trim($js_scripts);

		if (!empty($js))
		{
			if (pathinfo($js, PATHINFO_EXTENSION) == 'js')
			{
				$js = FCPATH . self::$config[THEME_PATH] . '/' . self::$config[THEME] . '/' . self::$config[JS_PATH] . '/' . $js;
			}

			self::$themeVars[self::INLINE_JS][sha1($js)] = translate($js, $langs);
		}

		return $this;
	}

	/**
	 * Add CSS from external source (fully css url)
	 * 
	 * @param string||array $full_css_path
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function addExternalCSS($full_css_path = null)
	{
		$full_css_path = is_array($full_css_path) ? $full_css_path : explode(',', $full_css_path);

		foreach ($full_css_path as $css)
		{
			$css = trim($css);

			if (!empty( $css ))
			{
				self::$themeVars[self::EXTERNAL_CSS][sha1($css)] = $css;
			}
		}

		return $this;
	}

	/**
	 * Add JS from external source (fully js url)
	 * 
	 * @param string||array $full_js_path
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function addExternalJS($full_js_path = null)
	{
		$full_js_path = is_array($full_js_path) ? $full_js_path : explode(',', $full_js_path);

		foreach ($full_js_path as $js)
		{
			$js = trim($js);

			if (!empty($js))
			{
				self::$themeVars[self::EXTERNAL_JS][sha1($js)] = $js;
			}
		}

		return $this;
	}

	/**
	 * Load Registered Plugins
	 * 
	 * @param string||array $plugins
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function loadPlugins($plugins)
	{
		$plugins = is_array($plugins) ? $plugins : explode(',', $plugins);

		foreach ($plugins as $plugin)
		{
			$plugin = trim($plugin);

			if (!empty($plugin))
			{
				if (!array_key_exists($plugin, self::$config['plugins']))
				{
					throw ThemesException::forPluginNotRegistered($plugin);
				}

				$this->loadPlugin($plugin);
			}
		}

		return $this;
	}

	/**
	 * Load Each Plugin
	 * 
	 * @param string $plugin key of plugin
	 */
	protected function loadPlugin($plugin)
	{
		$plugin_url = self::$themeVars['plugin_url'];

		foreach(self::$config['plugins'][$plugin] as $type => $plugin_files)
		{
			foreach($plugin_files as $plugin_file)
			{
				$plugin_path = str_replace(base_url(), FCPATH, $plugin_url);

				if (!is_file($plugin_path . $plugin_file))
				{
					throw ThemesException::forPluginNotFound($plugin_file);
				}

				self::$themeVars[self::LOADED_PLUGIN][$type][] = $plugin_url . $plugin_file;
			}
		}
	}

	/**
	 * Wether themes used full template or not
	 * 
	 * @param boolean $use_full_template
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function useFullTemplate($use_full_template = true)
	{
		self::$config['use_full_template'] = $use_full_template;

		return $this;
	}

	/**
	 * Set Header Template in the run-time
	 * 
	 * @param string $header_name
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function setHeader($header_name = null)
	{
		if (is_string($header_name))
		{
			self::$config[HEADER] = $header_name;
		}

		return $this;
	}

	/**
	 * Set Main Template in the run-time
	 * 
	 * @param string $template_name
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function setTemplate($template_name = null)
	{
		if (is_string($template_name))
		{
			self::$config[TEMPLATE] = $template_name;
		}

		return $this;
	}

	/**
	 * Set Footer Template in the run-time
	 * 
	 * @param string $footer_name
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function setFooter($footer_name = null)
	{
		if (is_string($footer_name))
		{
			self::$config[FOOTER] = $footer_name;
		}

		return $this;
	}

	/**
	 * Set Theme in the run-time
	 * 
	 * @param string $theme_name
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function setTheme($theme_name = null)
	{
		if (is_string($theme_name))
		{
			self::$config[THEME] = $theme_name;
		}

		self::$instance->setVar([
			'theme_url'  => base_url(self::$config[THEME_PATH] . '/' . self::$config[THEME]) . '/',
			'image_url'  => base_url(self::$config[THEME_PATH] . '/' . self::$config[THEME] . '/' . self::$config[IMAGE_PATH]) . '/',
			'plugin_url' => base_url(self::$config[THEME_PATH] . '/' . self::$config[THEME] . '/' . self::$config[PLUGIN_PATH]) . '/'
		]);

		return $this;
	}

	/**
	 * Render view or plain text or html into template theme
	 * 
	 * @param string $viewPath
	 * @param array  $data
	 */
	static function render($viewPath = null, $data = [])
	{
		if (is_null(self::$instance))
		{
			self::init();
		}
		
		$objTheme = self::$instance;
		$objTheme->setvar($data);

		if (!$objTheme->templateExist(self::$config[TEMPLATE]))
		{
			throw ThemesException::forMissingTemplateView(self::$config[TEMPLATE]);
		}

		$objTheme->setContent($viewPath, $objTheme::getData());

		// use custom view using theme path
		$view_config = Config('View');

		$view = new \CodeIgniter\View\View($view_config, FCPATH . self::$config[THEME_PATH] . '/' . self::$config[THEME] . '/');

		$view->setData($objTheme::getData());

		if (self::$config['use_full_template'])
		{
			echo $view->render(self::$config[TEMPLATE]);
		}
		else
		{
			if ($objTheme->templateExist(self::$config[HEADER]))
			{
				echo $view->render(self::$config[HEADER]);
			}

			echo $view->render(self::$config[TEMPLATE]);

			if ($objTheme->templateExist(self::$config[FOOTER]))
			{
				echo $view->render(self::$config[FOOTER]);
			}
		}
	}

	/**
	 * render CSS themes
	 */
	public static function renderCSS()
	{
		helper('themes');
		// proceed css themes, if exist
		if (array_key_exists(self::CSS_THEME, self::$themeVars))
		{
			foreach(self::$themeVars[self::CSS_THEME] as $css)
			{
				$css_file = FCPATH . self::$config[THEME_PATH] . '/' . self::$config[THEME] . '/' . self::$config['css_path'] . '/' . validate_ext($css, '.css');

				if (is_file($css_file))
				{
					$latest_version = filemtime($css_file);

					$css_file   = str_replace(FCPATH, '', $css_file);
					$latest_css = base_url($css_file . '?v=' . $latest_version);

					echo link_tag($latest_css);
				}
			}
		}

		// proceed external css, if exist
		if (array_key_exists(self::EXTERNAL_CSS, self::$themeVars))
		{
			foreach(self::$themeVars[self::EXTERNAL_CSS] as $css)
			{
				echo link_tag($css);
			}
		}

		// proceed plugin css, if exist
		if (array_key_exists(self::LOADED_PLUGIN, self::$themeVars) && array_key_exists('css', self::$themeVars[self::LOADED_PLUGIN]))
		{
			foreach(self::$themeVars[self::LOADED_PLUGIN]['css'] as $css)
			{
				echo link_tag($css);
			}
		}
	}

	/**
	 * render JS themes
	 */
	public static function renderJS()
	{
		helper('themes');
	
		self::renderExtraJs();

		// proceed main js theme, if exist
		if (array_key_exists(self::JS_THEME, self::$themeVars))
		{
			foreach(self::$themeVars[self::JS_THEME] as $js)
			{
				$js_file = FCPATH . self::$config[THEME_PATH] . '/' . self::$config[THEME] . '/' . self::$config[JS_PATH] . '/' . validate_ext($js, '.js');

				if (is_file($js_file))
				{
					$latest_version = filemtime($js_file);
					
					$js_file   = str_replace(FCPATH, '', $js_file);
					$latest_js = base_url($js_file . '?v=' . $latest_version);

					echo script_tag($latest_js);
				}
			}
		}

		// proceed inline js, if exist
		if (array_key_exists(self::INLINE_JS, self::$themeVars))
		{
			$inline_js = '<script type="text/javascript">' . PHP_EOL; 
			
			foreach(self::$themeVars[self::INLINE_JS] as $js)
			{
				$inline_js .= $js . PHP_EOL;
			}

			$inline_js .= '</script>' . PHP_EOL;

			echo $inline_js;
		}
	}

	/**
	 * Render Inline JS
	 */
	protected static function renderExtraJs()
	{
		// proceed external js, if exist
		if (array_key_exists(self::EXTERNAL_JS, self::$themeVars))
		{
			foreach(self::$themeVars[self::EXTERNAL_JS] as $js)
			{
				echo script_tag($js);
			}
		}

		// proceed plugin js, if exist
		if (array_key_exists(self::LOADED_PLUGIN, self::$themeVars) && array_key_exists('js', self::$themeVars[self::LOADED_PLUGIN]))
		{
			foreach(self::$themeVars[self::LOADED_PLUGIN]['js'] as $js)
			{
				echo script_tag($js);
			}
		}
	}

	/**
 	* Check does template exist 
 	* 
 	* @param string $template
 	* 
 	* @return boolean
 	*/
	protected function templateExist($template = null)
	{
		helper('themes');

		return is_file(FCPATH . self::$config[THEME_PATH] . '/' . self::$config[THEME] . '/' . validate_ext($template));
	}

	/**
	 * Set Main Content
	 * 
	 * @param string $viewPath
	 * @param array  $data
	 */
	protected function setContent($viewPath = null, $data = [], $viewDir = 'Views')
	{
		$content = "";

		if (is_string($viewPath))
		{
			$content = $viewPath; 
		}

		if (!empty($viewPath))
		{
			$fileExt = pathinfo($viewPath, PATHINFO_EXTENSION);

			$locator = \Config\Services::locator();
			$view    = $locator->locateFile($viewPath, $viewDir, empty($fileExt) ? 'php' : $fileExt);

			if (!empty($view))
			{
				$content = view($viewPath, $data);
			}
		}

		$this->setVar(self::CONTENT, $content);
		$this->setPageTitle($data);
	}

	/**
	 * Set Page Title - used in <title> tags
	 * 
	 * @param string $page_title
	 */
	public function setPageTitle($page_title = null)
	{
		$_page_title = '';

		if (is_string($page_title))
		{
			$_page_title = $page_title;
		}
		elseif (is_array($page_title) && array_key_exists(self::PAGE_TITLE, $page_title))
		{
			$_page_title = $page_title[self::PAGE_TITLE];
		}
		elseif (!array_key_exists(self::PAGE_TITLE, self::$themeVars) && !is_cli()) 
		{
			// page_title is not defined, so detect current controller/method as page title
			$router = service('router');
		
			$controllers = explode('\\', $router->controllerName());
			$controller  = $controllers[count($controllers)-1];

			$_page_title = ($controller . ' | ' . ucfirst($router->methodName()));
		}

		$this->setVar(self::PAGE_TITLE, $_page_title);

		return $this;
	}

	/**
	 * Set Variable to be passed into template
	 * 
	 * @param string||array $key
	 * @param mixed         $value
	 * 
	 * @return $this Arifrh\Themes\Themes 
	 */
	public function setVar($key, $value = false)
	{
		if (is_array($key))
		{
			foreach ($key as $_key => $_value)
			{
				self::$themeVars[$_key] = $_value;
			}
		}
		else
		{
			self::$themeVars[$key] = $value;
		}

		return $this;
	}

	/**
	 * Get All Themes Variables
	 * 
	 * @return array
	 */
	public static function getData(): array
	{
		return self::$themeVars;
	}

	/**
	 * Get All Themes Configs
	 * 
	 * @return array
	 */
	public static function getConfig(): array
	{
		return self::$config;
	}
}
