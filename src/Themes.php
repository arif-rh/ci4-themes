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
	 * @param string|array  $jsFiles    
	 * @param boolean       $isExternalOrInline wether the js file is local or external, or inline script
 	 * @param integer       $priority           js priority order
	 * @param mixed         $i18n               language translation for i18n js
	 *
	 *@return $this Arifrh\Themes\Themes
	 */	
	public function addJS($jsFiles, $isExternalOrInline = false, $priority = 0, $i18n = null)
	{
		if (is_string($jsFiles) && is_string($isExternalOrInline) && strtolower($isExternalOrInline) == 'inline')
		{
			return $this->addInlineJS($jsFiles, $priority, $i18n);
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
	 * @param mixed   $i18n          language translation for i18n js
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */ 
	public function addInlineJS($inlineScript, $priority = 0, $i18n = null)
	{
		$js = trim($inlineScript);

		if (! empty($js))
		{
			if (isset(self::$tmpVars['js'][$priority]))
			{
				ksort(self::$tmpVars['js']);
			}

			if (is_array($i18n))
			{
				$js = translate($js, $i18n);
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
	}

	/**
	 * Load Registered Plugins
	 * 
	 * @param string||array $plugins
	 * @param integer       $priority
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function loadPlugins($plugins, $priority = 0)
	{
		$plugins = is_array($plugins) ? $plugins : explode(',', $plugins);

		foreach ($plugins as $plugin)
		{
			$plugin = trim($plugin);

			if (! empty($plugin))
			{
				$registeredPlugins = setting()->get('Themes.plugins');

				if (! array_key_exists($plugin, $registeredPlugins))
				{
					throw ThemesException::forPluginNotRegistered($plugin);
				}

				$this->loadPlugin($plugin, $priority);
			}
		}

		return $this;
	}

	/**
	 * Load Each Plugin
	 * 
	 * @param string   $plugin   key of plugin
	 * @param integer  $priority
	 */
	protected function loadPlugin($plugin, $priority = 0)
	{
		$registeredPlugins = setting()->get('Themes.plugins');

		foreach($registeredPlugins[$plugin] as $type => $pluginFiles)
		{
			foreach($pluginFiles as $theFile)
			{
				$pluginUri = plugin_url($theFile);

				if (! is_file(str_replace(base_url(), FCPATH, $pluginUri)))
				{
					throw ThemesException::forPluginNotFound($theFile);
				}

				if ($type == 'js')
				{
					$this->addJS($pluginUri, true, $priority);
				}
				else
				{
					$this->addCSS($pluginUri, true, $priority);
				}
			}
		}
	}

	/**
	 * Set Layout Template
	 * 
	 * @param string $layoutName
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function setLayout($layoutName)
	{
		setting()->set('Themes.layout', $layoutName);

		return $this->setVar('layout', $layoutName);
	}

	/**
	 * Render view or plain text or html into template theme
	 * 
	 * @param string $viewPath
	 * @param array  $data
	 */
	public static function render($viewPath = null, $data = [])
	{
		if (is_null(self::$instance))
		{
			self::init();
		}
		
		$objTheme = self::$instance;
		$objTheme->setvar($data);

		$view = new \CodeIgniter\View\View();
		$data = $objTheme::getVars();

		$layout  = setting()->get('Themes.layout');

		echo $view->render($tpl['index'], $data, true);
	}

	/**
	 * Change Namespace used for Views
	 * 
	 * @param string $namespace
	 * 
	 * @return $this Arifrh\Themes\Themes
	 */
	public function setNamespaceView($namespace)
	{
		setting()->set('Themes.namespaceView', $namespace);

		return $this;
	}

	/**
	 * Get full Namespace Views
	 * 
	 * @param string $view
	 * 
	 * @return $string
	 */
	public function getNamespaceView($view)
	{
		return implode('\'', [
			setting()->get('Themes.namespaceView'),
			setting()->get('Themes.name'),
		]) . '\\' . $view;
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
	 * Generate Auto Page Title (can be used in <title> tags)
	 * 
	 * @param string $titleVar variable name will be used to save the page title
	 * 
	 */
	public function setAutoPageTitle($titleVar = 'pageTitle')
	{
		$pageTitle = '';

		if (! array_key_exists($titleVar, self::$tmpVars) && ! is_cli()) 
		{
			// detect current controller/method as page title
			$router = service('router');
		
			$controllers = explode('\\', $router->controllerName());
			$controller  = $controllers[count($controllers)-1];

			$pageTitle = ($controller . ' | ' . ucfirst($router->methodName()));
		}

		$this->setVar($$titleVar, $pageTitle);

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
