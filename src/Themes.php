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
	 * @version   2.0-dev
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
	 * Temporary variables which will be used in rendering template
	 *
	 * @var    array
	 * @access protected
	 */
	protected static $tmpVars = [];

	/**
	 * Initialize Themes based on context 
	 *
	 * @param  mixed $context
	 * 
	 * @return void
	 */
	public static function init($context = null)
	{
		if (self::$instance === null)
		{
			self::$instance = new self;
		}

		self::$instance::$tmpVars = null;

		if (! function_exists('setting'))
		{
			helper('setting');
		}

		$themeName = setting()->get('Themes.name', $context);

		self::$instance->setTheme($themeName, $context);

		return self::$instance;
	}

	 /**
	 * Set Theme name
	 * 
	 * @param string $themeName
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function setTheme($themeName, $context = null)
	{
		if (is_string($themeName))
		{
			setting()->set('Themes.name', $themeName, $context);
		}

		$publicAssets = setting()->get('Themes.public_assets');
		$themeDir     = $publicAssets['theme_dir'];
		$cssDir       = $publicAssets['css_dir'];
		$jsDir        = $publicAssets['js_dir'];
		$imageDir     = $publicAssets['image_dir'];
		$pluginDir    = $publicAssets['plugin_dir'];
		$themeURL     = base_url($themeDir . '/' . $themeName) . '/';

		self::$instance->setVar([
			'theme_url'  => $themeURL,
			'css_url'    => $themeURL . $cssDir . '/',
			'js_url'     => $themeURL . $jsDir . '/',
			'image_url'  => $themeURL . $imageDir . '/',
			'plugin_url' => $themeURL . $pluginDir . '/',
		]);

		return $this;
	}

	/**
	 * add css file(s) to be loaded inside template
	 *
	 * @param string|array   $cssFiles
	 * @param boolean        $isExternal wether the css file is local or external
 	 * @param integer        $priority   css priority order
	 *@return $this Arifrh\Themes\Themes
	 */	
	public function addCSS($cssFiles, $isExternal = false, $priority = 0)
	{
		$cssFiles = is_array($cssFiles) ? $cssFiles : explode(',', $cssFiles);

		foreach ($cssFiles as $css)
		{
			$css = trim($css);

			$cssTag = self::linkTag($css, $isExternal);

			if (! empty($css) && ! empty($cssTag))
			{
				if (isset(self::$tmpVars['css'][$priority]))
				{
					ksort(self::$tmpVars['css']);
				}

				self::$tmpVars['css'][$priority][] = [
					sha1($css) => $cssTag,
				];
			}			
		}

		return $this;
	}

	/**
	 * Generate css link html tag
	 * 
	 * @param string  $css
	 * @param boolean $isExternal wether css is local or external
	 * 
	 * @return string
	 */
	public static function linkTag($css, $isExternal = false)
	{
		if (! function_exists('validate_ext'))
		{
			helper('themes');
		}

		if ($isExternal)
		{
			return link_tag($css);
		}

		$cssURL  = css_url(validate_ext($css, '.css'));
		$cssFile = str_replace(base_url(), FCPATH, $cssURL);

		if (is_file($cssFile))
		{
			return link_tag($cssURL . '?v=' . filemtime($cssFile));
		}

		return null;
	}

	/**
	 * render CSS themes
	 */
	public static function renderCSS()
	{
		if (isset(self::$tmpVars['css']))
		{
			// sort by priority order
			ksort(self::$tmpVars['css']);
	
			$existingCss = [];
	
			foreach (self::$tmpVars['css'] as $priorityCss)
			{
				foreach ($priorityCss as $css)
				{
					foreach ($css as $idx => $linkTagHTML)
					{
						if (! in_array($idx, $existingCss))
						{
							echo $linkTagHTML . PHP_EOL;
							array_push($existingCss, $idx);
						}
					}
				}
			}
		}
	}

	/**
	 * add Js file(s) to be loaded inside template
	 *
	 * @param string|array   $jsFiles    
	 * @param boolean        $isExternalOrInline wether the js file is local or external, or inline script
 	 * @param integer        $priority           js priority order
	 *@return $this Arifrh\Themes\Themes
	 */	
	public function addJS($jsFiles, $isExternalOrInline = false, $priority = 0)
	{
		if (is_string($jsFiles) && is_string($isExternalOrInline) && strtolower($isExternalOrInline) == 'inline')
		{
			return $this->addInlineJS($jsFiles, $priority);
		}

		$jsFiles = is_array($jsFiles) ? $jsFiles : explode(',', $jsFiles);

		foreach ($jsFiles as $js)
		{
			$js = trim($js);

			$jsTag = self::scriptTag($js, $isExternalOrInline);

			if (! empty($js) && ! empty($jsTag))
			{
				if (isset(self::$tmpVars['js'][$priority]))
				{
					ksort(self::$tmpVars['js']);
				}

				self::$tmpVars['js'][$priority][] = [
					sha1($js) => $jsTag,
				];
			}	
		}

		return $this;
	}

	/**
	 * Generate script tag html
	 * 
	 * @param string  $js
	 * @param boolean $isExternal wether js is local or external
	 * 
	 * @return string
	 */
	public static function scriptTag($js, $isExternal = false)
	{
		if (! function_exists('validate_ext'))
		{
			helper('themes');
		}

		if ($isExternal)
		{
			return script_tag($js);
		}

		$jsURL  = js_url(validate_ext($js, '.js'));
		$jsFile = str_replace(base_url(), FCPATH, $jsURL);

		if (is_file($jsFile))
		{
			return script_tag($jsURL . '?v=' . filemtime($jsFile));
		}

		return null;
	}

	/**
	 * Adding inline JS to the template
	 *  
	 * @param string  $inlineScript
 	 * @param integer $priority      js priority order
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */ 
	public function addInlineJS($inlineScript, $priority = 0)
	{
		$js = trim($inlineScript);

		if (! empty($js))
		{
			if (isset(self::$tmpVars['js'][$priority]))
			{
				ksort(self::$tmpVars['js']);
			}

			self::$tmpVars['js'][$priority][] = [
				sha1($js) => script_tag($js, true),
			];
		}

		return $this;
	}

	/**
	 * render JS themes
	 */
	public static function renderJS()
	{
		if (isset(self::$tmpVars['js']))
		{
			// sort by priority order
			ksort(self::$tmpVars['js']);
	
			$existingJs = [];
	
			foreach (self::$tmpVars['js'] as $priorityJs)
			{
				foreach ($priorityJs as $js)
				{
					foreach ($js as $idx => $scriptTagHTML)
					{
						if (! in_array($idx, $existingJs))
						{
							echo $scriptTagHTML . PHP_EOL;
							array_push($existingJs, $idx);
						}
					}
				}
			}
		}

		return false;
	
		self::renderExtraJs();
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

			self::$tmpVars[self::INLINE_JS][sha1($js)] = translate($js, $langs);
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
		$plugin_url = self::$tmpVars['plugin_url'];

		foreach(self::$config['plugins'][$plugin] as $type => $plugin_files)
		{
			foreach($plugin_files as $plugin_file)
			{
				$plugin_path = str_replace(base_url(), FCPATH, $plugin_url);

				if (!is_file($plugin_path . $plugin_file))
				{
					throw ThemesException::forPluginNotFound($plugin_file);
				}

				self::$tmpVars[self::LOADED_PLUGIN][$type][] = $plugin_url . $plugin_file;
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

		$objTheme->setContent($viewPath, $objTheme::getVars());

		// use custom view using theme path
		$view_config = Config('View');

		$view = new \CodeIgniter\View\View($view_config, FCPATH . self::$config[THEME_PATH] . '/' . self::$config[THEME] . '/');

		$view->setData($objTheme::getVars());

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
	 * Render Inline JS
	 */
	protected static function renderExtraJs()
	{
		// proceed external js, if exist
		if (array_key_exists(self::EXTERNAL_JS, self::$tmpVars))
		{
			foreach(self::$tmpVars[self::EXTERNAL_JS] as $js)
			{
				echo script_tag($js);
			}
		}

		// proceed plugin js, if exist
		if (array_key_exists(self::LOADED_PLUGIN, self::$tmpVars) && array_key_exists('js', self::$tmpVars[self::LOADED_PLUGIN]))
		{
			foreach(self::$tmpVars[self::LOADED_PLUGIN]['js'] as $js)
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
		elseif (!array_key_exists(self::PAGE_TITLE, self::$tmpVars) && !is_cli()) 
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
				self::$tmpVars[$_key] = $_value;
			}
		}
		else
		{
			self::$tmpVars[$key] = $value;
		}

		return $this;
	}

	/**
	 * Get All Themes Variables
	 * 
	 * @return array
	 */
	public static function getVars(): array
	{
		return self::$tmpVars;
	}
}
