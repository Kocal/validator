Validator
=========

A PHP values validator that makes you able to use the great [Laravel Validator](https://laravel.com/docs/master/validation),
but outside a Laravel project.

Installation
------------

```bash
$ composer require kocal/validator
```

Usage
-----

All Laravel validation rules except [exists](https://laravel.com/docs/master/validation#rule-exists) and 
[unique](https://laravel.com/docs/master/validation#rule-unique) are supported.

```php
<?php
use Kocal\Validator\Validator;

$rules = ['field' => 'required|min:5'];
$data = ['field' => 'Validation'];

$validator = new Validator($rules);
$validator->validate($data);
$validator->passes(); // true
$validator->fails(); // false
$validator->errors()->toArray(); // returns array of error messages
```

Advanced usage
--------------

### Translations

Available validation translation languages: see [src/lang](src/lang) directory.
The default language is `fr`.

```php
<?php
use Kocal\Validator\Validator;

$validator = new Validator([], 'es');
```

### Custom validation rule

```php
<?php
use Kocal\Validator\Validator;

$validator = new Validator(['field' => 'is_foo']);

$validator->extend('is_foo', function ($attribute, $value, $parameters, $validator) {
    return $value == 'foo';
}, "Le champ :attribute n'est pas égal à 'foo'.");

$validator->validate(['field' => 'not_foo']);
```