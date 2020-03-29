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

		self::$config = (array) $config;

		self::$instance->setTheme(self::$config['theme']);

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

			if (empty($css))
			{
				continue;
			}

			// set unique key-index to prevent duplicate css being included
			self::$themeVars['css_themes'][sha1($css)] = $css;
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

			if (empty($js))
			{
				continue;
			}

			// set unique key-index to prevent duplicate js being included
			self::$themeVars['js_themes'][sha1($js)] = $js;
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
			self::$themeVars['inline_js'][sha1($js)] = $js;
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

			if (empty( $css ))
			{
				continue;
			}

			self::$themeVars['external_css'][sha1($css)] = $css;
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

			if (empty($js))
			{
				continue;
			}

			self::$themeVars['external_js'][sha1($js)] = $js;
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

			if (empty($plugin))
			{
				continue;
			}

			if (!array_key_exists($plugin, self::$config['plugins']))
			{
				throw ThemesException::forPluginNotRegistered($plugin);
			}

			foreach(self::$config['plugins'][$plugin] as $type => $plugin_files)
			{
				foreach($plugin_files as $plugin_file)
				{
					$plugin_path = str_replace(base_url(), FCPATH, self::$themeVars['plugin_url']);

					if (!file_exists($plugin_path . $plugin_file))
					{
						throw ThemesException::forPluginNotFound($plugin_file);
					}

					self::$themeVars['loaded_plugins'][$type][] = self::$themeVars['plugin_url'] . $plugin_file;
				}
			}
		}

		return $this;
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
			self::$config['header'] = $header_name;
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
			self::$config['template'] = $template_name;
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
			self::$config['footer'] = $footer_name;
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
			self::$config['theme'] = $theme_name;
		}

		self::$instance->setVar([
			'theme_url'  => base_url(self::$config['theme_path'] . '/' . self::$config['theme']) . '/',
			'image_url'  => base_url(self::$config['theme_path'] . '/' . self::$config['theme'] . '/' . self::$config['image_path']) . '/',
			'plugin_url' => base_url(self::$config['theme_path'] . '/' . self::$config['theme'] . '/' . self::$config['plugin_path']) . '/'
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
			$config = config('Themes');
			self::init($config);
		}

		$objTheme = self::$instance;
		$objTheme->setvar($data);

		if (!$objTheme->templateExist(self::$config['template']))
		{
			throw ThemesException::forMissingTemplateView(self::$config['template']);
		}

		$objTheme->setContent($viewPath, $objTheme::getData());

		// use custom view using theme path
		$view_config = Config('View');

		$view = new \CodeIgniter\View\View($view_config, FCPATH . self::$config['theme_path'] . '/' . self::$config['theme'] . '/');

		$view->setData($objTheme::getData());

		if (self::$config['use_full_template'])
		{
			echo $view->render(self::$config['template']);
		}
		else
		{
			if ($objTheme->templateExist(self::$config['header']))
			{
				echo $view->render(self::$config['header']);
			}

			echo $view->render(self::$config['template']);

			if ($objTheme->templateExist(self::$config['footer']))
			{
				echo $view->render(self::$config['footer']);
			}
		}
	}

	/**
	 * render CSS themes
	 */
	public static function renderCSS()
	{
		// proceed css themes, if exist
		if (array_key_exists('css_themes', self::$themeVars))
		{
			helper('themes');

			foreach(self::$themeVars['css_themes'] as $css)
			{
				$css_file = FCPATH . self::$config['theme_path'] . '/' . self::$config['theme'] . '/' . self::$config['css_path'] . '/' . validate_ext($css, '.css');

				if (file_exists($css_file))
				{
					$css_file   = str_replace(FCPATH, '', $css_file);
					$latest_css = base_url($css_file . '?v=' . filemtime($css_file));

					echo link_tag($latest_css);
				}
			}
		}

		// proceed external css, if exist
		if (array_key_exists('external_css', self::$themeVars))
		{
			foreach(self::$themeVars['external_css'] as $css)
			{
				echo link_tag($css);
			}
		}

		// proceed plugin css, if exist
		if (array_key_exists('loaded_plugins', self::$themeVars))
		{
			if (array_key_exists('css', self::$themeVars['loaded_plugins']))
			{
				foreach(self::$themeVars['loaded_plugins']['css'] as $css)
				{
					echo link_tag($css);
				}
			}
		}
	}

	/**
	 * render JS themes
	 */
	public static function renderJS()
	{
		// proceed main js theme, if exist
		if (array_key_exists('js_themes', self::$themeVars))
		{
			helper('themes');

			foreach(self::$themeVars['js_themes'] as $js)
			{
				$js_file = FCPATH . self::$config['theme_path'] . '/' . self::$config['theme'] . '/' . self::$config['js_path'] . '/' . validate_ext($js, '.js');

				if (file_exists($js_file))
				{
					$js_file   = str_replace(FCPATH, '', $js_file);
					$latest_js = base_url($js_file . '?v=' . filemtime($js_file));

					echo script_tag($latest_js);
				}
			}
		}

		// proceed external js, if exist
		if (array_key_exists('external_js', self::$themeVars))
		{
			foreach(self::$themeVars['external_js'] as $js)
			{
				echo script_tag($js);
			}
		}

		// proceed plugin js, if exist
		if (array_key_exists('loaded_plugins', self::$themeVars))
		{
			if (array_key_exists('js', self::$themeVars['loaded_plugins']))
			{
				foreach(self::$themeVars['loaded_plugins']['js'] as $js)
				{
					echo script_tag($js);
				}
			}
		}

		// proceed inline js, if exist
		if (array_key_exists('inline_js', self::$themeVars))
		{
			$inline_js = '<script type="text/javascript">' . PHP_EOL; 
			
			foreach(self::$themeVars['inline_js'] as $js)
			{
				$inline_js .= $js . PHP_EOL;
			}

			$inline_js .= '</script>' . PHP_EOL;

			echo $inline_js;
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
		if (empty($template))
		{
			return false;
		}

		helper('themes');

		return file_exists(FCPATH . self::$config['theme_path'] . '/' . self::$config['theme'] . '/' . validate_ext($template));
	}

	/**
	 * Set Main Content
	 * 
	 * @param string $viewPath
	 * @param array  $data
	 */
	protected function setContent($viewPath = null, $data = [])
	{
		if (is_string($viewPath))
		{
			$content = $viewPath; 
		}
		else
		{
			$content = "";
		}

		if (!empty($viewPath))
		{
			$fileExt = pathinfo($viewPath, PATHINFO_EXTENSION);

			$locator = \Config\Services::locator();
			$view    = $locator->locateFile($viewPath, 'Views', empty($fileExt) ? 'php' : $fileExt);

			if (!empty($view))
			{
				$content = view($viewPath, $data);
			}
		}

		$this->setVar('content', $content);
		$this->setPageTitle($data);
	}

	/**
	 * Set Page Title - used in <title> tags
	 * 
	 * @param string $page_title
	 */
	public function setPageTitle($page_title = null)
	{
		if (is_string($page_title))
		{
			$this->setVar('page_title', $page_title);
		}
		elseif (is_array($page_title) && array_key_exists('page_title', $page_title))
		{
			$this->setVar('page_title', $page_title['page_title']);
		}
		elseif (!array_key_exists('page_title', self::$themeVars)) 
		{
			// page_title is not defined, so detect current controller/method as page title
			$router = service('router');

			$namespace_controller  = $router->controllerName();
			
			$controllers = explode('\\', $namespace_controller);

			$controller  = $controllers[count($controllers)-1];

			$method = $router->methodName();

			$this->setVar('page_title', $controller . ' | ' . ucfirst($method));
		}

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
				$this->setThemeVars($_key, $_value);
			}
		}
		else
		{
			$this->setThemeVars($key, $value);
		}

		return $this;
	}

	/**
	 * Set Themes variable value
	 * 
	 * @param string $key
	 * @param mixed  $value
	 */
	protected function setThemeVars($key, $value)
	{
		self::$themeVars[$key] = $value;
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
}
