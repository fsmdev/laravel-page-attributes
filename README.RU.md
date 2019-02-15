Laravel Page Attributes: мета и другие СЕО атрибуты 
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

### Использование

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
# app/config.php

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

#### Использование view

#### Задание собственных шаблонов
