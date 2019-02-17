Laravel 5 Page Attributes: мета и другие СЕО атрибуты 
=====================

Расширение для **Laravel 5**, которое позволит устанавливать метаданные и прочие атрибуты страниц, генерировать html (теги), получать атрибуты страницы из базы данных, создавать собственные атрибуты и html для них.

### Установка

    composer require fsmdev/laravel-page-attributes
    
##### Для Laravel 5.4 и более ранних версий
    
Если Вы используете Laravel 5.5 и выше, то провайдер и алиас инициализируются автоматически. Для более ранних версий необходимо указать провайдер и алиас в файле config/app.php

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
### Конфигурация

Если Вы хотите изменить режим многоязычности, спользуемый в механизме получения атрибутов по контексту, то это можно сделать установив значение свойства `FSMDEV_MULTI_LANGUAGE` в .env файле. По умолчанию многоязычный режим выключен (false).

Для изменения других настроек используется файл config/page_attributes.php, который можно создать вручную или при помощи команды:

    php artisan vendor:publish --provider="Fsmdev\LaravelPageAttributes\PageAttributesServiceProvider" --tag=config

### Базовое использование

Большая часть операций, описанных ниже, выполняется с использованием фасада 

```php
use Fsmdev\LaravelPageAttributes\Facades\PageAttributes;
```

#### Задание атрибутов страницы

Для установки атрибутов старницы (например, метаданных) используется метод **set** фасада PageAttributes.

    set ( string $name, string $value ) : void

```php
PageAttributes::set('title', 'Awesome Page');

PageAttributes::set('h1', $post->name);

# Можно задавать свои собственные атрибуты
PageAttributes::set('my_attribute', 'My Value');
```

#### Значения по умолчанию

Для атрибутов страниц можно задать значения по умолчанию. Изначально значения по умолчанию имеют атрибуты `charset` и `viewport`.

```php
# config/page_attributes.php

'default' => [

  # Задание значения по умолчанию для title
  'title' => 'Awesome Page',
  
  # Переопределение charset
  'charset' => 'windows-1251',
],
```

#### Получение атрибутов страницы

Для получения значений атрибутов страницы используется метод **get** фасада PageAttributes.
    
    set ( string $name) : string

#### Получение html 

Для получения html атрибутов страницы используется метод **html** фасада PageAttributes.

    html ( string $name) : string

Так же можно воспользоваться Blade-директивами: `@charset`, `@viewport`, `@title`, `@description`, `@keywords`, `@canonical`.

```blade
{{ PageAttributes::html('title') }}
```

или

```blade
@title
```
#### Задание собственных шаблонов

Метод **html** для формирования результата использует предопределенные в системе шаблоны html кода атрибутов. Эти шаблоны можно переопределить или добавить новые шаблоны для собственных атрибутов.

```php
# config/page_attributes.php

'html_templates' => [
    
    # Переопределение шаблона для h1
    'h1' => '<h1 class="some-class"><{value}/h1>',
    
    # Создание шаблона для собственного атрибута
    'my_attribute' => '<p>{value}</p>',
],
```

Конфигурация, указанная выше, может использоваться следующим образом.

Контроллер:

```php
PageAttributes::set('my_attribute', 'My Value');
```
Вид:

```blade
{{ PageAttributes::html('my_attribute') }}
```
Результат на странице:

```html
<p>My Value</p>
```

#### Использование view

В пакет входит view, который отобразит следующие теги: charset, viewport, title, description, keywords, canonical. Для использования необходмо добавить его в блок `<head>` страницы.

```blade
@include('page_attributes::meta')
```
Для изменения view его необходимо создать файл `resouces/views/vendor/page_attributes/meta.blade.php`. Сделать это автоматически можно при помощи команды:

    php artisan vendor:publish --provider="Fsmdev\LaravelPageAttributes\PageAttributesServiceProvider" --tag=view

### Использование контекста страниц

Для разделения данных и представления лучше хранить метаданные в базе данных. Когда страница связанна с объектом какой то модели, то это леко решаемо добавлением полей метаданных в модель. Но что делать для страниц типа главной или разделов? Механизм контекста страниц предлагает вариант решения.

#### Установка

    php artisan vendor:publish --provider="Fsmdev\LaravelPageAttributes\PageAttributesServiceProvider" --tag=context

    php artisan migrate

После установки в папке app/ConstantsCollections появится файл с классом `PageAttributesContext`, унаследованный от [ConstantCollection](https://github.com/fsmdev/constants-collection), а так же в базе данных появится таблица `page_attributes`

#### Коллекция PageAttributesContext

В данном классе необходимо указать набор констант, соответствующий страницам, для которых будет использоваться механизм получения атрибутов по контексту. Значения констант должны быть типа `TYNIINT UNSIGNED`. Так же рекомендуеться задать имена констант в методе **propertiesName**. Эти имена могут быть в дальнейшем использованы для создания CRUD класса PageAttribute.

Подробне о работе с классом ConstantsCollection можно почитать [тут](https://github.com/fsmdev/constants-collection).

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

#### Модель PageAttribute

В пакет входит модель `Fsmdev\LaravelPageAttributes\Models\PageAttribute`. Миграцией была создана таблица `page_attributes`. Таблица (модель) содержит следующие поля:

**context** (UNSIGNED TINYINT) - содержит значение константы, соответствующей контексту, для которого задается атрибут.

**language** (CHAR(2) NULLABLE) - язык/локаль для которого задается значение атрибута. Если использование многоязычности выставлено в false, этот атрибут не учитывается при выборке данных.

**name** (char(30)) - имя атрибута.

**value** (text) - значение атрибута.

Поля **context**, **language** и **name** образуют униальный индекс и являются ключем к определению значения атрибута.

Пример заполнения данных:

context|language|name|value
-------|--------|----|-----------
5|NULL|h1|Awesome Site
5|NULL|title|Welcome to Awesome Site
5|NULL|my_attribute|My Value
15|NULL|h1|My Blog

##### Использование контекста

Для установки контекста атрибутов страницы используется метод **context** фасада PageAttributes.

    context ( integer $context) : void

```php
PageAttributes::context(PageAttributesContext::INDEX);
```

Когда контекст установлен методы **get**, **html** и blade-директивы будут использовать модель PageAttribute для поиска значений требуемого атрибута:

```blade
{{ PageAttributes::html('title'); }}
```

Вернет (для случая когда данные заполнены как в таблице выше):

```html
<title>Welcome to Awesome Site</title>
```

### Приоритет выбора значения атрибута

При определении значения атрибута приоритет источника следующий:

1. Атрибуты, установленные непосредтсвенно методом **set**;
2. Атрибуты, полученные по контексту (если он установлен);
3. Значения атрибутов по умолчанию.
