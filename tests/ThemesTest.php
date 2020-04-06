<?php 

declare(strict_types=1);

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Arifrh\Themes\Config as Config;
use Arifrh\Themes\Themes;


final class ThemesTest extends TestCase
{
    protected $themes = null;

    public function setUp(): void
    {
        helper('url');
        $this->themes = Themes::init();
    }

    public function tearDown(): void
    {
        $this->themes = null;
    }

    public function testInitReturnInstance()
    {
        $config = new Config\Themes;

        $this->assertContainsOnlyInstancesOf(
            Themes::class,
            [Themes::init($config)],
            'Themes is not an instance of Arifrh\Themes\Themes'
        );

        // init without passing config

        $this->assertContainsOnlyInstancesOf(
            Themes::class,
            [Themes::init()],
            'Themes is not an instance of Arifrh\Themes\Themes'
        );
    }

    public function testAddSingleCss()
    {
        $css = "style.css";

        $this->themes->addCSS($css);

        $themeVars = $this->themes::getData();

        $this->assertCount(1, $themeVars[$this->themes::CSS_THEME]);
    
        // add css using array

        $css = [$css];

        $this->themes->addCSS($css);

        $themeVars = $this->themes::getData();

        $this->assertCount(1, $themeVars[$this->themes::CSS_THEME]);
    }

    public function testAddMultipleCss()
    {
        $css = "style.css, style-2.css";

        $this->themes->addCSS($css);

        $themeVars = $this->themes::getData();

        $this->assertCount(2, $themeVars[$this->themes::CSS_THEME]);
   
        // add multiple css using array

        $css = explode(",", $css);

        $this->themes->addCSS($css);

        $themeVars = $this->themes::getData();

        $this->assertCount(2, $themeVars[$this->themes::CSS_THEME]);
    }

    public function testAddJs()
    {
        $js = "script.js";
        $this->themes->addJS($js);

        $themeVars = $this->themes::getData();

        $this->assertContains($js, $themeVars[$this->themes::JS_THEME]);
    }

    public function testAddInlineJs()
    {
        $inlineJs = "alert('OK');";
        $this->themes->addInlineJS($inlineJs);

        $themeVars = $this->themes::getData();

        $this->assertContains($inlineJs, $themeVars[$this->themes::INLINE_JS]);
    }

    public function testAddExternalCss()
    {
        $css = "http://example.com/css/other-style.css";

        $this->themes->addExternalCSS($css);

        $themeVars = $this->themes::getData();

        $this->assertCount(1, $themeVars[$this->themes::EXTERNAL_CSS]);
    }

    public function testAddExternalJs()
    {
        $js = "http://example.com/js/other-script.js";

        $this->themes->addExternalJS($js);

        $themeVars = $this->themes::getData();

        $this->assertCount(1, $themeVars[$this->themes::EXTERNAL_JS]);
    }

    public function testSetHeader()
    {
        $custom_header = 'custom-header';

        $this->themes->setHeader($custom_header);

        $themeConfig = $this->themes::getConfig();

        $this->assertEquals($custom_header, $themeConfig['header']);
    }

    public function testSetTemplate()
    {
        $custom_template = 'custom-template';

        $this->themes->setTemplate($custom_template);

        $themeConfig = $this->themes::getConfig();

        $this->assertEquals($custom_template, $themeConfig['template']);
    }

    public function testSetFooter()
    {
        $custom_footer = 'custom-footer';

        $this->themes->setFooter($custom_footer);

        $themeConfig = $this->themes::getConfig();

        $this->assertEquals($custom_footer, $themeConfig['footer']);
    }

    public function testUseFullTemplate()
    {
        $this->themes->useFullTemplate();

        $themeConfig = $this->themes::getConfig();

        $this->assertTrue($themeConfig['use_full_template']);
    }

    public function testSetVar()
    {
        $package_name = "ci4-themes";

        $this->themes->setVar('package_name', $package_name);

        $themeVars = $this->themes::getData();

        $this->assertEquals($package_name, $themeVars['package_name']);
    }

    public function testSetPageTitle()
    {
        $page_title = "Test Page Title";

        $this->themes->setPageTitle($page_title);

        $themeVars = $this->themes::getData();

        $this->assertEquals($page_title, $themeVars[$this->themes::PAGE_TITLE]);

        // test using array
        $this->themes->setPageTitle(['page_title' => $page_title]);

        $themeVars = $this->themes::getData();

        $this->assertEquals($page_title, $themeVars[$this->themes::PAGE_TITLE]);
    }

    public function testUsingCustomConfig()
    {
        // before using custom config, it use default config
        $themeConfig = $this->themes::getConfig();

        $this->assertEquals('starter', $themeConfig['theme']);

        $config = new Arifrh\ThemesTest\Config\Themes();
        
        $this->themes = Themes::init($config);

        $themeConfig = $this->themes::getConfig();

        $this->assertEquals('custom', $themeConfig['theme']);
    }

    public function testLoadPlugins()
    {
        $config = new Arifrh\ThemesTest\Config\Themes();
        
        $this->themes = Themes::init($config);

        $plugin = 'some-plugin';

        $this->themes->loadPlugins($plugin);

        $themeConfig = $this->themes::getConfig();
        $themeVars   = $this->themes::getData();

        $expectedCount = count($themeConfig['plugins'][$plugin]);
        $this->assertEquals($expectedCount, count($themeVars['loaded_plugins']));
    }

    public function testLoadNonExistPlugins()
    {
        $this->expectException(Arifrh\Themes\Exceptions\ThemesException::class);

        $plugin = 'non-exist-plugin';

        $this->themes->loadPlugins($plugin);
    }

    public function testLoadPluginsWithMissingFiles()
    {
        $this->expectException(Arifrh\Themes\Exceptions\ThemesException::class);

        $config = new Arifrh\ThemesTest\Config\Themes();
        
        $this->themes = Themes::init($config);

        $plugin = 'other-plugin';

        $this->themes->loadPlugins($plugin);
    }
    
    public function testRenderCss()
    {
        $config = new Arifrh\ThemesTest\Config\Themes();
        
        $this->themes = Themes::init($config);

        $plugin = 'some-plugin';

        $this->themes
            ->addCSS('style.css')
            ->addExternalCSS('http://example.org/css/other-style.css')
            ->loadPlugins($plugin);

        ob_start();
        $this->themes::renderCSS();
        $renderCSS = ob_get_contents();
        @ob_end_clean();

        $this->assertStringContainsString('/style.css', $renderCSS);
        $this->assertStringContainsString('http://example.org/css/other-style.css', $renderCSS);

       $pluginCss = $this->themes::getConfig()['plugins'][$plugin]['css'];

       foreach($pluginCss as $css)
       {
           $this->assertStringContainsString($css, $renderCSS);
       }
    }

    public function testRenderJs()
    {
        $config = new Arifrh\ThemesTest\Config\Themes();
        
        $this->themes = Themes::init($config);

        $inlineJs = "alert('OK');";
        $plugin   = 'some-plugin';

        $this->themes
            ->addJS('script.js')
            ->addInlineJs($inlineJs)
            ->addExternalJS('http://example.org/css/other-script.js')
            ->loadPlugins($plugin);

        ob_start();
        $this->themes::renderJS();
        $renderJS = ob_get_contents();
        @ob_end_clean();

        $this->assertStringContainsString('/script.js', $renderJS);
        $this->assertStringContainsString($inlineJs, $renderJS);
        $this->assertStringContainsString('http://example.org/css/other-script.js', $renderJS);

       $pluginJs = $this->themes::getConfig()['plugins'][$plugin]['js'];

       foreach($pluginJs as $js)
       {
           $this->assertStringContainsString($js, $renderJS);
       }
    }

    public function testRenderMissingTemplate()
    {
        $this->expectException(Arifrh\Themes\Exceptions\ThemesException::class);

        $this->themes->setTemplate('non-exist-template');
		$this->themes::render('This will never be rendered');
    }

    public function testRenderString()
    {
        $expected = "Hello World!";

        ob_start();
		$this->themes::render($expected);
		$renderString = ob_get_contents();
        @ob_end_clean();
        
        $this->assertStringContainsString(
            $expected,
            $renderString
        );
    }

    public function testRenderUsingFullTemplate()
    {
        $expected = "Login Page";

        ob_start();
        $this->themes->useFullTemplate();
		$this->themes::render($expected);
		$renderString = ob_get_contents();
        @ob_end_clean();
        
        $this->assertStringContainsString(
            $expected,
            $renderString
        );
    }

    public function testRenderView()
    {
        helper('themes');
        
        $data = [
            'plugin_url' => plugin_url()
        ];

        ob_start();
		$this->themes::render('Arifrh\ThemesTest\Views\TestView', $data);
		$renderString = ob_get_contents();
        @ob_end_clean();
        
        

        $this->assertStringContainsString(
            $data['plugin_url'],
            $renderString
        );
    }
}