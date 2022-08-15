<?php 

declare(strict_types=1);

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Arifrh\ThemesTest\Config\Themes as ConfigTheme;
use Arifrh\Themes\Themes;


final class ThemesTest extends TestCase
{
    protected $themes = null;

    public function setUp(): void
    {
        helper('url');

        $config           = config('Settings');
        $config->handlers = ['array'];

        $settings = new \CodeIgniter\Settings\Settings($config);

        $this->themes = Themes::init();
    }

    public function tearDown(): void
    {
        $this->themes = null;
    }

    public function testInitReturnInstance()
    {
        $this->assertContainsOnlyInstancesOf(
            Themes::class,
            [Themes::init('module:frontend')],
            'Themes is not an instance of Arifrh\Themes\Themes'
        );

        // init without passing config
        $this->assertContainsOnlyInstancesOf(
            Themes::class,
            [Themes::init()],
            'Themes is not an instance of Arifrh\Themes\Themes'
        );
    }

    public function testAddCss()
    {
        $css = ["style.css"];

        $priority = 0;

        $this->themes->addCSS($css, false, $priority);

        $tmpVars = $this->themes::getVars();

        $this->assertCount(1, $tmpVars['css'][$priority]);

        $css = [
            "style2.css",
            "custom.css",
        ];

        $this->themes->addCSS($css, false, $priority);

        $tmpVars = $this->themes::getVars();

        $this->assertCount(3, $tmpVars['css'][$priority]);

        // add CSS from external resource
        $externalCSS = 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css';

        $this->themes->addCSS([$externalCSS], true, 1);

        $externalCssTag = [
            [sha1($externalCSS) => $this->themes::linkTag($externalCSS, true)],
        ];

        $tmpVars = $this->themes::getVars();

        $this->assertSame($externalCssTag, $tmpVars['css'][1]);
    }

    public function testAddCssWithDifferentPriority()
    {
        $cssPriority1 = [
            "style2.css",
            "custom.css",
        ];
        
        // set CSS with priority order 1
        $this->themes->addCSS($cssPriority1, false, 1);

        $css = "style.css";
        // set CSS with priority order 0
        $this->themes->addCSS([$css], false, 0);

        $tmpVars = $this->themes::getVars();

        $firstOrderCss = [
            [sha1($css) => $this->themes::linkTag($css)],
        ];

        $this->assertSame($firstOrderCss, $tmpVars['css'][0]);

        $nextCss = [];

        foreach ($cssPriority1 as $css)
        {
            $nextCss[] = [sha1($css) => $this->themes::linkTag($css)];
        }

        $this->assertSame($nextCss, $tmpVars['css'][1]);
    }

    public function testRenderCss()
    {
        $this->themes
            ->addCSS('style.css')
            ->addCSS('style2.css')
            ->addCSS('http://example.org/css/other-style.css', true);

        ob_start();
        $this->themes::renderCSS();
        $renderCSS = ob_get_contents();
        @ob_end_clean();

        $this->assertStringContainsString($this->themes::linkTag('style.css'), $renderCSS);
        $this->assertStringContainsString($this->themes::linkTag('style2.css'), $renderCSS);
        $this->assertStringContainsString($this->themes::linkTag('http://example.org/css/other-style.css', true), $renderCSS);
    }

    public function testAddJs()
    {
        $js = "script.js";
        $this->themes->addJS($js);

        $tmpVars = $this->themes::getVars();

        $assetJs = [
            [sha1($js) => $this->themes::scriptTag($js)],
        ];

        $this->assertSame($assetJs, $tmpVars['js'][0]);
   
        // add inline js script
        $inlineJs = "alert('OK');";

        $this->themes->addJS($inlineJs, 'inline', 1);

        $tmpVars = $this->themes::getVars();

        $assetInlineJs = [
            [sha1($inlineJs) => script_tag($inlineJs, true)],
        ];

        $this->assertSame($assetInlineJs, $tmpVars['js'][1]);

        // add js from external source
        $js = "http://example.com/js/other-script.js";

        $this->themes->addJS($js, true, 2);

        $tmpVars = $this->themes::getVars();

        $assetJs = [
            [sha1($js) => $this->themes::scriptTag($js, true)],
        ];

        $this->assertSame($assetJs, $tmpVars['js'][2]);
    }

    public function testRenderJsWithI18n()
    {
        // add inline js script with i18n
        $inlineJs = "alert('Hello {{name}}!);";

        $this->themes->addJS($inlineJs, 'inline', 9, [
            'name' => 'Jhony',
        ]);

        ob_start();
        $this->themes::renderJS();
        $renderJS = ob_get_contents();
        @ob_end_clean();

        $this->assertStringContainsString('Hello Jhony!', $renderJS);
    }
    
    public function testLoadPlugins()
    {
        $plugin = 'some-plugin'; // this plugin has one css and one js

        $this->themes->loadPlugins($plugin);

        $tmpVars = $this->themes::getVars();

        $this->assertCount(1, $tmpVars['css'][0]);
        $this->assertCount(1, $tmpVars['js'][0]);
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

        $plugin = 'other-plugin';

        $this->themes->loadPlugins($plugin);
    }
    
    public function testSetVar()
    {
        $packageName = "ci4-themes";

        $this->themes->setVar('packageName', $packageName);

        $tmpVars = $this->themes::getVars();

        $this->assertEquals($packageName, $tmpVars['packageName']);
    }

    /*
    public function testSetPageTitle()
    {
        $page_title = "Test Page Title";

        $this->themes->setPageTitle($page_title);

        $tmpVars = $this->themes::getVars();

        $this->assertEquals($page_title, $tmpVars[$this->themes::PAGE_TITLE]);

        // test using array
        $this->themes->setPageTitle(['page_title' => $page_title]);

        $tmpVars = $this->themes::getVars();

        $this->assertEquals($page_title, $tmpVars[$this->themes::PAGE_TITLE]);
    }

    public function testRenderMissingTemplate()
    {
        $this->expectException(Arifrh\Themes\Exceptions\ThemesException::class);

        $this->themes->setTemplate('non-exist-template');
		$this->themes::render('This will never be rendered');
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
    */
}