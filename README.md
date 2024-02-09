# This is my package hyper-tables


This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require jamesdordoy/hyper-tables
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="hyper-tables-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$hyperTables = new JamesDordoy\HyperTables();
echo $hyperTables->echoPhrase('Hello');
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
