Laravel 5 Page Attributes: meta and other SEO-important attributes 
=====================

[English](https://github.com/fsmdev/laravel-page-attributes/blob/master/README.md) | [Русский](https://github.com/fsmdev/laravel-page-attributes/blob/master/README.RU.md)

Package for **Laravel 5**, that helps manage metadata and other page attributes, generate html tags, get page attributes from data base and make own page attributes and html templates for they.

### Installation

    composer require fsmdev/laravel-page-attributes
    
##### For Laravel 5.4 and earlier versions
    
If you use Laravel 5.5 or higher package discover automatically. For earlier versions add provider and alias in config/app.php file.

```php
# config/app.php

'providers' => [
  ...
  Fsmdev\LaravelPageAttributes\PageAttributesServiceProvider::class,
  ...
];

'aliases' => [
  ...
  'PageAttributes' => Fsmdev\LaravelPageAttributes\Facades\PageAttributes::class,
  ...
];

```
### Configuration

If you want to change multilanguage mode this can be done by setting the value of the `FSMDEV_MULTI_LANGUAGE` property in the .env file. By default multi language mode is disabled (false).

For changin

To change other settings, use the file config/page_attributes.php. The file can be created manually or using the command:

    php artisan vendor:publish --provider="Fsmdev\LaravelPageAttributes\PageAttributesServiceProvider" --tag=config

### Basic usage

Most of the operations described below are performed using the facade PageAttributes. 

```php
use Fsmdev\LaravelPageAttributes\Facades\PageAttributes;
```

#### Setting page attributes

To set the attributes of the page (for example, metadata), the **set** method of the PageAttributes facade is used.

    set ( string $name, string $value ) : void

```php
PageAttributes::set('title', 'Awesome Page');

PageAttributes::set('h1', $post->name);

# Own attribute
PageAttributes::set('my_attribute', 'My Value');
```

#### Default values

You can set default values for page attributes. Initially, `charset` and` viewport` attributes have default values.

```php
# config/page_attributes.php

'default' => [

  # Default value for title
  'title' => 'Awesome Page',
  
  # Override charset
  'charset' => 'windows-1251',
],
```

#### Getting page attributes

To get the page attribute values, use the **get** method of the PageAttributes facade.
    
    get ( string $name) : string

#### Getting html 

To get html attributes of the page, the method **html** of the PageAttributes facade is used.

    html ( string $name) : string

You can also use Blade directives: `@charset`, `@viewport`, `@title`, `@description`, `@keywords`, `@canonical`.

```blade
{{ PageAttributes::html('title') }}
```

or

```blade
@title
```
#### Creating your own templates

The **html** method uses the predefined html code templates to generate the result. These templates can override or add new templates for your own attributes.

```php
# config/page_attributes.php

'html_templates' => [
    
    # Overriding template for h1
    'h1' => '<h1 class="some-class"><{value}/h1>',
    
    # Creating template for own attribute
    'my_attribute' => '<p>{value}</p>',
],
```

The configuration specified above can be used as follows.

Controller:

```php
PageAttributes::set('my_attribute', 'My Value');
```
View:

```blade
{{ PageAttributes::html('my_attribute') }}
```
Page output:

```html
<p>My Value</p>
```

#### Default view usage

The package includes a view that displays the following tags: charset, viewport, title, description, keywords, canonical. To use it, add it to the `<head>` block of the page.

```blade
@include('page_attributes::meta')
```
To change the view you need to create a file `resouces/views/vendor/page_attributes/meta.blade.php`. You can do this automatically with the command:

    php artisan vendor:publish --provider="Fsmdev\LaravelPageAttributes\PageAttributesServiceProvider" --tag=view

### Page Context usage

For separation data and view, it is better to store metadata in a database. When a page is associated with an object of some kind of model, it is easily solved by adding metadata fields to the model. But what to do for pages like main or categories? The page context mechanism offers a solution.

#### Installation

    php artisan vendor:publish --provider="Fsmdev\LaravelPageAttributes\PageAttributesServiceProvider" --tag=context

    php artisan migrate

After installation, a file will appear in the app/ConstantsCollections folder with the `PageAttributesContext` class inherited from [ConstantCollection](https://github.com/fsmdev/constants-collection), the table_attributes table will also appear in the database.

#### Conllection: PageAttributesContext

In this class, you must specify a set of constants corresponding to the pages for which the mechanism for obtaining attributes by context will be used. Constant values must be of type `TYNIINT UNSIGNED`. It is also recommended to set the names of the constants in the **propertiesName** method. These names can be further used to create a CRUD of the PageAttribute class.

You can read more about working with the ConstantsCollection class [here](https://github.com/fsmdev/constants-collection).

```php
# app/ConstantsCollections/PageAttributesContext.php

const INDEX = 5;
const CONTACTS = 10;
const BLOG = 15;

protected static function propertiesName()
{
    return [
        self::INDEX => 'Home Page',
        self::CONTACTS => 'Contacts Page',
        self::BLOG => 'Blog',
    ];
}
```

#### Model: PageAttribute

The package includes a model `Fsmdev\LaravelPageAttributes\Models\PageAttribute` with table `page_attributes`. The table (model) contains the following fields:

**context** (UNSIGNED TINYINT) - contains the value of the constant corresponding to the context for which the attribute is being set.

**language** (CHAR(2) NULLABLE) - language / locale for which the attribute value is specified. If the use of `multi_language` is set to false, this attribute is not taken into account when retrieving data.

**name** (char(30)) - attribute name.

**value** (text) - attribute value.

Fields **context**, **language** and **name** form a unique index and are key to defining an attribute value.

An example of filling data:

context|language|name|value
-------|--------|----|-----------
5|NULL|h1|Awesome Site
5|NULL|title|Welcome to Awesome Site
5|NULL|my_attribute|My Value
15|NULL|h1|My Blog

##### Context usage

To set the page attribute context, use the **context** method of the PageAttributes facade.

    context ( integer $context) : void

```php
PageAttributes::context(PageAttributesContext::INDEX);
```

When the context is set, the **get**, **html** methods and blade directives will use the PageAttribute model to find the values of the required attribute:

```blade
{{ PageAttributes::html('title'); }}
```

Returns (for the case when the data is filled as in the table above):

```html
<title>Welcome to Awesome Site</title>
```

### The priority of attribute value selection

When determining the attribute value, the source priority is as follows:

1. Attributes set directly by the method **set**;
2. Attributes received by context (if set);
3. Attribute default values.
