#Laravel 4 LanguageSelector

A package for Laravel 4 which sets dynamic the Language for the user

[![Latest Stable Version](https://poser.pugx.org/jones/language-selector/v/stable.png)](https://packagist.org/packages/jones/language-selector)
[![Total Downloads](https://poser.pugx.org/jones/language-selector/downloads.png)](https://packagist.org/packages/jones/language-selector)

If anyone has any ideas on how to make this framework agnostic, please contact me or open a pull request.

##Installation

Add `jones/language-selector` as a requirement to `composer.json`:

```javascript
{
    ...
    "require": {
        ...
        "jones/language-selector": "dev-master"
        ...
    },
}
```

Update composer:

```
$ php composer.phar update
```

Add the provider to your `app/config/app.php`:

```php
'providers' => array(

    ...
    'Jones\LanguageSelector\LanguageSelectorServiceProvider',

),
```

(Optional) Publish package config:

```
$ php artisan config:publish jones/language-selector
```

##Configuration

 * `lang_key`: The key where the language of the user is set
 
##How is checked which language is used?

First it is checked whether you entered a language key and if so we try to get the language from the user
(You need to use Laravel's Auth class or Sentry and the language have to be the short key, eg 'en'). If we
found a valid language we use this.
Second we check the "Accept-Language" Header and try to set the language with it.
If both failed we use the locale set in the config.