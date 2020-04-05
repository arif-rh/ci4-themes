# CodeIgniter 4 Themes

[![Build Status](https://travis-ci.com/arif-rh/ci4-themes.svg?branch=master)](https://travis-ci.com/arif-rh/ci4-themes)   [![Coverage Status](https://coveralls.io/repos/github/arif-rh/ci4-themes/badge.svg)](https://coveralls.io/github/arif-rh/ci4-themes)  [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=arif-rh_ci4-themes&metric=alert_status)](https://sonarcloud.io/dashboard?id=arif-rh_ci4-themes)  [![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=arif-rh_ci4-themes&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=arif-rh_ci4-themes)  [![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=arif-rh_ci4-themes&metric=security_rating)](https://sonarcloud.io/dashboard?id=arif-rh_ci4-themes)  [![Bugs](https://sonarcloud.io/api/project_badges/measure?project=arif-rh_ci4-themes&metric=bugs)](https://sonarcloud.io/dashboard?id=arif-rh_ci4-themes)  [![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=arif-rh_ci4-themes&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=arif-rh_ci4-themes)

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

- [CodeIgniter 4 Themes](#codeigniter-4-themes)
  - [Installation](#installation)
- [CI4 Themes Documentation](#ci4-themes-documentation)
  - [Table of Contents](#table-of-contents)
- [Themes Config](#themes-config)
  - [Themes](#themes)
    - [init](#init)
    - [addCSS](#addcss)
    - [addJS](#addjs)
    - [addInlineJS](#addinlinejs)
    - [addExternalCSS](#addexternalcss)
    - [addExternalJS](#addexternaljs)
    - [loadPlugins](#loadplugins)
    - [useFullTemplate](#usefulltemplate)
    - [setHeader](#setheader)
    - [setTemplate](#settemplate)
    - [setFooter](#setfooter)
    - [setTheme](#settheme)
    - [render](#render)
    - [renderCSS](#rendercss)
    - [renderJS](#renderjs)
    - [setPageTitle](#setpagetitle)
    - [setVar](#setvar)
    - [getData()](#getdata)

# Themes Config

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




## Themes

Class Themes

* Full name: \Arifrh\Themes\Themes

  


### init

Instantiate Themes with theme config, if you don't pass `$config` then it will use default one.

This method is **static**.

```
init(\Config\Themes $config = null)
```

**Parameters**:

| Parameter | Type               | Description         |
| --------- | ------------------ | ------------------- |
| `$config` | **\Config\Themes** | Theme Configuration |

Example Usage:

```php
<?php 

use Arifrh\Themes\Themes;

// inside your controller
    
$config = config('Adminlte'); // your custom theme config 
Themes::init($config);
```



---



### addCSS

Add CSS file(s) to be loaded inside template.  This must be used after `Themes::init()`. All non-static method must be called after `Themes::init()`.

```php
addCSS(string|array $css_files)
```

**Parameters:**

| Parameter    | Type                  | Description                                                  |
| ------------ | --------------------- | ------------------------------------------------------------ |
| `$css_files` | **string&#124;array** | CSS filename that will be loaded in template. This CSS file must be exist inside `css_path` in `\Config\Themes` |

**Return Value:**

Arifrh\Themes\Themes



**NOTE**: All non-static method always return the chained object, so you can call next method directly.



Example Usage:

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



---



### addJS

Add js file(s) to be loaded inside template. This method has same usage with `addCSS()` method. The different just parameter(s) to be passed are JavaScript filename.

```php
addJS(string|array $js_files)
```

**Parameters:**

| Parameter   | Type                  | Description                                                  |
| ----------- | --------------------- | ------------------------------------------------------------ |
| `$js_files` | **string&#124;array** | JS filename that will be loaded in template. This JS file must be exist inside `js_path` in `\Config\Themes` |



Example Usage:

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



---



### addInlineJS

Inject inline JavaScript code to the template.

```php
addInlineJS(string $js_scripts)
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$js_scripts` | **string** | Valid JavaScript code to be added. |



---



### addExternalCSS

Add CSS from external source (fully CSS URL). This is used when we need to include CSS files outside `css_path` in theme folder.

```php
addExternalCSS(string|array $full_css_path)
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$full_css_path` | **string&#124;array** | CSS filename with full path. |



---



### addExternalJS

Add JS from external source (fully JavaScript URL). This is used when we need to include JavaScript files outside `js_path` in theme folder.

```php
addExternalJS(string|array $full_js_path)
```

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$full_js_path` | **string&#124;array** | JavaScript filename with full path. |



---



### loadPlugins

Plugins are JavaScript Libraries that will be mostly used in many place the project, but not all page always use it. For example, `DataTable`, `Bootbox`, `Select2`. 

```php
loadPlugins(string|array $plugins)
```

**Parameters:**

| Parameter  | Type                  | Description                  |
| ---------- | --------------------- | ---------------------------- |
| `$plugins` | **string&#124;array** | Registered plugins key name. |



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

Example Usage:

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



---



### useFullTemplate

```php
useFullTemplate(boolean $use_full_template = true)
```

**Parameters:**

| Parameter            | Type        | Description |
| -------------------- | ----------- | ----------- |
| `$use_full_template` | **boolean** |             |



This Themes by default will use 3 kind of templates. All of these templates are located inside themes folder.

1. **Header**
   Header is a template that usually used to display header of site, contains `<html>`,`<title>`, `<head>` and opening `<body>` html tags. This header section usually is same for all pages, and being used to load common CSS files.

   By default, header template will use `header.php` filename, you can change this from `\Config\Themes`.
   
   
   
2. **Template**
   This is main template. Content will be injected here.

   By default, template use `index.php` and this also can be changed from `\Config\Themes`.
   
   
   
3. **Footer**
   Footer is a template that usually used to display footer of site, contains closing tags for `<body>` and `<html>`. This footer section usually is same for all pages, and  being used to load common JavaScript files.

   By default, footer template will use `footer.php` filename, you can change this from `\Config\Themes`.
   
   

In a few case, you may need to display custom template, for example your default template has navbar, sidebar, content and main footer.  But in login page, you need to display page without navbar, sidebar and has different footer. In this case, you can use full template. 

1. make your login view with full html and body tags
2. before rendering template, call `useFullTemplate`

```php
// assume your login view has full html and body tags

Themes::init()->useFullTemplate();
Themes::render('login')
```



---



### setHeader

Header that being set in `\Config\Themes` will be used as default theme header. 

```php
setHeader(string $header_name)
```

**Parameters:**

| Parameter      | Type       | Description       |
| -------------- | ---------- | ----------------- |
| `$header_name` | **string** | Header file  name |



In a few case, one or two page may need to use different header, then you can set it on-the-fly.

```php
// assume that you have another-header.php in your active theme folder

Themes::init()
    ->setHeader('another-header');
```



---



### setTemplate

Template that being set in `\Config\Themes` will be used as default theme main template. 

```php
setTemplate(string $template_name)
```

**Parameters:**

| Parameter        | Type       | Description         |
| ---------------- | ---------- | ------------------- |
| `$template_name` | **string** | Template file  name |



In a few case, one or two page may need to use different template, then you can set it on-the-fly.

```php
// assume that you have another-template.php in your active theme folder

Themes::init()
    ->setHeader('another-template');
```



---

### setFooter

Footer that being set in `\Config\Themes` will be used as default theme footer. 

```php
setFooter(string $footer_name)
```

**Parameters:**

| Parameter      | Type       | Description       |
| -------------- | ---------- | ----------------- |
| `$footer_name` | **string** | Footer file  name |



In a few case, one or two page may need to use different footer, then you can set it on-the-fly.

```php
// assume that you have another-footer.php in your active theme folder

Themes::init()
    ->setFooter('another-footer');
```



---



### setTheme

Themes that being set in `\Config\Themes` will be used as default theme. 

```php
setTheme(string $theme_name)
```

**Parameters:**

| Parameter     | Type       | Description |
| ------------- | ---------- | ----------- |
| `$theme_name` | **string** | Theme name  |



In a few case, one or two page may need to use different theme, then you can set it on-the-fly.

```php
// assume that you have frontend theme

Themes::init()
    ->setTheme('frontend');
```



---



### render

This is main method that will render any content using active theme templates.

```php
render(string $viewPath = null, array $data = array())
```

**Parameters:**

| Parameter   | Type       | Description                                                  |
| ----------- | ---------- | ------------------------------------------------------------ |
| `$viewPath` | **string** | View file name, or string to be rendered directly into template |
| `$data`     | **array**  | Dynamic variable that will be passed into view               |



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



### setPageTitle

```php
setPageTitle(string $page_title)
```

**Parameters:**

| Parameter     | Type       | Description                     |
| ------------- | ---------- | ------------------------------- |
| `$page_title` | **string** | Text will be used as title page |



Set Page Title - used in `<title>` tags. If you do not set page title, then theme will use **Controller | Method** name as default title.  `$page_title` variable always be available for themes, It can be used inside `<title>` tag.

```php+HTML
<title><?= $page_title ?> | Site Name </title>
```



---

### setVar

Set Variable to be passed into template.  Actually this can be passed using `$data` parameter in `Themes::render()` method.  So this is just an alias to passing data into template.

```php
setVar($key, $value)
```

**Parameters:**

| Parameter | Type                  | Description                                                  |
| --------- | --------------------- | ------------------------------------------------------------ |
| `$key`    | **string&#124;array** | if string, this is key of dynamic variable. If using array, then array index will be used as dynamic variable inside template |
| `$value`  | **mixed**             |                                                              |



Example Usage:

```php
Themes::init()->setVar('page_title', 'Welcome Page');

// same as

Themes::init()->setVar(['page_title' => 'Welcome Page']);

// same as

Themes::render('view_name', ['page_title' => 'Welcome Page']);
```



---

### getData()

Get All Themes Variables




