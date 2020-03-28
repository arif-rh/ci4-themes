## Installation

1. install via composer, run `composer require arif-rh/ci4-themes`

2. setup your theme config (optional), if you don not setup config, then it will use default one

3. if you do not change config, theme folder structure will be like below, so copy all your files here:

 

```
public/
└── themes/
    └── starter/
        ├── css/
        │ └── your-css.css
        ├── js/
        │ └── your-js.js
        ├── img/
        └── plugins/ 
```



# CI4 Themes Documentation

## Table of Contents

* [Arifrh\Themes\Config\Themes](#themes)
* [Arifrh\Themes\Themes](#themes-1)
    * [**static** init](#init)
    * [addCSS](#addcss)
    * [addJS](#addjs)
    * [addInlineJS](#addinlinejs)
    * [addExternalCSS](#addexternalcss)
    * [addExternalJS](#addexternaljs)
    * [loadPlugins](#loadplugins)
    * [useFullTemplate](#usefulltemplate)
    * [setTheme](#settheme)
    * [**static** render](#render)
    * [**static** renderCSS](#rendercss)
    * [**static** renderJS](#renderjs)
    * [setPageTitle](#setpagetitle)
    * [setVar](#setvar)
    * [**static** getData](#getdata)

## \Config\Themes

* Full name: \Arifrh\Themes\Config\Themes

This is default config themes. You can override themes by extending this default themes, for example make file inside App\Config\ 

```php
<?php namespace Config;

class Adminlte extends \Arifrh\Themes\Config\Themes
{
	public $theme = 'Adminlte';
    
    // you can overide other properties as your need
} 
```

You can use this new theme config later.


## Arifrh\Themes\Themes

Class Themes

* Full name: \Arifrh\Themes\Themes

  


### init(\Config\Themes $config = null)

Instantiate Themes with theme config, if you don't pass `$config` then it will use default one.

```php
<?php 

use Arifrh\Themes\Themes;

// inside your controller
    
$config = config('Adminlte'); // your custom theme config 
Themes::init($config);
```

* This method is **static**.

  **Parameters**:

| Parameter | Type | Description |
|-----------|------|-------------|
| `$config` | **\Config\Themes** | Theme Configuration |



---

### addCSS(string|array $css_files)

Add CSS file(s) to be loaded inside template.  This must be used after `Themes::init()`. All non-static method must be called after `Themes::init()`.

There are 3 ways you can add CSS files:

```php
// 1. using string (for single css file)

Themes::init()
    ->addCSS('style.css');

// 2. using string, separated by comma (for multiple css files)

Themes::init()
    ->addCSS('base.css, style.css');

// 3. using array (for single or multiple css files)

Themes::init()
    ->addCSS(['base.css', 'style.css']);
```




**Parameters:**

| Parameter    | Type                  | Description                                                  |
| ------------ | --------------------- | ------------------------------------------------------------ |
| `$css_files` | **string&#124;array** | CSS filename that will be loaded in template. This CSS file must be exist inside `css_path` in `\Config\Themes` |

**Return Value:**

Arifrh\Themes\Themes

**All non-static method always has chained return, so you can call next method directly.**



---

### addJS(string|array $js_files)

Add js file(s) to be loaded inside template. This method has same usage with `addCSS()` method. The different just parameter(s) to be passed are JavaScript filename.

```php
Themes::init()
    ->addJS('script.js');

// or

Themes::init()
    ->addJS(['script.js', 'order.js']);

// or use with addCSS method

Themes::init()
    ->addCSS('checkout.css')
    ->addJS(['script.js', 'order.js']);
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$js_files` | **string&#124;array** | JS filename that will be loaded in template. This JS file must be exist inside `js_path` in `\Config\Themes` |



---

### addInlineJS(string $js_scripts)

Inject inline JavaScript code to the template.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$js_scripts` | **string** | Valid JavaScript code to be added. |



---

### addExternalCSS(string|array $full_css_path)

Add CSS from external source (fully CSS URL). This is used when we need to include CSS files outside `css_path` in theme folder.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$full_css_path` | **string&#124;array** | CSS filename with full path. |



---

### addExternalJS(string|array $full_js_path)

Add JS from external source (fully JavaScript URL). This is used when we need to include JavaScript files outside `js_path` in theme folder.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$full_js_path` | **string&#124;array** | JavaScript filename with full path. |



---

### loadPlugins(string|array $plugins)

Plugins are JavaScript Libraries that will be mostly used in many place the project, but not all page always use it. For example, `DataTable`, `Bootbox`, `Select2`. 

Plugins are registered inside `\Config\Themes`. This is an example in `\Arifrh\Themes\Config\Themes` about how to define plugins.

```php
/**
  * Registered Plugins
  * Format:
  * [
  *  'plugin_key_name' => [
  *   'js' => [...js_array]
  *   'css' => [...css_array]
  *  ]
  * ]
  */

public $plugins = [
		'bootbox' => [
			'js' => [
				'bootbox/bootbox-en.min.js'
			]
		]
	];
```

This method used to load registered plugins.

```php
Themes::init()
    ->loadPlugins('bootbox');

// or

Themes::init()
    ->loadPlugins('bootbox, datatable');

// or

Themes::init()
    ->loadPlugins(['bootbox', 'select2', 'datatable']);
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$plugins` | **string&#124;array** | Registered plugins key name. |



---

### useFullTemplate(boolean $use_full_template = true)

This Themes by default will use 3 kind of templates. All of these templates are located inside themes folder.

1. **Header**
   Header is a template that usually used to display header of site, contains <html>, <title>, <head> and opening <body> html tags. This header section usually is same for all pages, and being used to load common CSS files.

   By default, header template will use `header.php` filename, you can change this from `\Config\Themes`.
   
2. **Template**
   This is main template. Content will be injected here.

   By default, template use `index.php` and this also can be changed from `\Config\Themes`.
   
3. **Footer**
   Footer is a template that usually used to display footer of site, contains closing tags for <body> and <html>. This footer section usually is same for all pages, and  being used to load common JavaScript files.

   By default, footer template will use `footer.php` filename, you can change this from `\Config\Themes`.

In a few case, you may need to display custom template, for example your default template has navbar, sidebar, content and main footer.  But in login page, you need to display page without navbar, sidebar and has different footer. In this case, you can use full template. 

1. make your login view with full html and body tags
2. before rendering template, call `useFullTemplate`

```php
// assume your login view has full html and body tags

Themes::init()->useFullTemplate();
Themes::render('login')
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$use_full_template` | **boolean** |  |



---

### setHeader(string $header_name)

Header that being set in `\Config\Themes` will be used as default theme header. 

In a few case, one or two page may need to use different header, then you can set it on-the-fly.

```php
// assume that you have another-header.php in your active theme folder

Themes::init()
    ->setHeader('another-header');
```




**Parameters:**

| Parameter      | Type       | Description       |
| -------------- | ---------- | ----------------- |
| `$header_name` | **string** | Header file  name |



---

### setTemplate(string $template_name)

Template that being set in `\Config\Themes` will be used as default theme main template. 

In a few case, one or two page may need to use different template, then you can set it on-the-fly.

```php
// assume that you have another-template.php in your active theme folder

Themes::init()
    ->setHeader('another-template');
```




**Parameters:**

| Parameter       | Type       | Description         |
| --------------- | ---------- | ------------------- |
| `$template_name | **string** | Template file  name |



---

### setFooter(string $footer_name)

Footer that being set in `\Config\Themes` will be used as default theme footer. 

In a few case, one or two page may need to use different footer, then you can set it on-the-fly.

```php
// assume that you have another-footer.php in your active theme folder

Themes::init()
    ->setFooter('another-footer');
```




**Parameters:**

| Parameter      | Type       | Description       |
| -------------- | ---------- | ----------------- |
| `$footer_name` | **string** | Footer file  name |



---

### setTheme(string $theme_name)

Themes that being set in `\Config\Themes` will be used as default theme. 

In a few case, one or two page may need to use different theme, then you can set it on-the-fly.

```php
// assume that you have frontend theme

Themes::init()
    ->setTheme('frontend');
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$theme_name` | **string** | Theme name |



---

### render(string $viewPath = null, array $data = array())

This is main method that will render any content using active theme templates.

Basically, when you want to display view in **CodeIgniter4**, you are using `view` method. Now, just replace your `view` method with `Themes::render()` and view will be rendered based on your active theme.

```php
// for example, default welcome_view in CodeIgniter4 can be render like this
// we useFullTemplate because welcome_message contains full html and body tags

Themes::init()->useFullTemplate();  
Themes::render('welcome_message');

// you can manage your header, main template and footer in theme
// then you can render any view like this

Themes::render('dashboard')
```


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$viewPath` | **string** | View file name, or string to be rendered directly into template |
| `$data` | **array** | Dynamic variable that will be passed into view |



---

### renderCSS

This method will render all CSS themes. This usually should be called inside header template

```php
<?php Arifrh\Themes\Themes::renderCSS(); ?>
```



---

### renderJS

This method will render all JavaScript themes. This usually should be called inside footer template.

```php
<?php Arifrh\Themes\Themes::renderJS(); ?>
```



---

### setPageTitle(string $page_title)

Set Page Title - used in <title> tags. If you do not set page title, then theme will use **Controller | Method** name as default title.  `$page_title` variable always be available for themes, It can be used inside <title> tag.

```php+HTML
<title><?= $page_title ?> | Site Name </title>
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$page_title` | **string** | Text will be used as title page |



---

### setVar

Set Variable to be passed into template.  Actually this can be passed using `$data` parameter in `Themes::render()` method.  So this is just an alias to passing data into template.

```php
Themes::init()->setVar('page_title', 'Welcome Page');

// same as

Themes::init()->setVar(['page_title' => 'Welcome Page']);

// same as

Themes::render('view_name', ['page_title' => 'Welcome Page']);
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **string&#124;array** | if string, this is key of dynamic variable. If using array, then array index will be used as dynamic variable inside template |
| `$value` | **mixed** |  |



---

### getData()

Get All Themes Variables




