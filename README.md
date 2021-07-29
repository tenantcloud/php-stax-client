# Stax PHP client

The Stax PHP library provides convenient access to the Stax API from
applications written in the PHP language.

## Requirements

PHP 7.1.0 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require tenantcloud/stax-client
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Dependencies

The bindings require the following extensions in order to work properly:

-   [`curl`](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer
-   [`json`](https://secure.php.net/manual/en/book.json.php)
-   [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

## Getting Started

Simple usage looks like:

```php
use Stax\Customer;

$customer = (new Customer('api_key'))->create([
    'email' => 'email@example.com',
]);
echo $customer;
```
