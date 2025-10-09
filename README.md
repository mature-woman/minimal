# MINIMAL
The best code-to-utility framework

## Nearest plans (2025)
1. Routes sorting `router::sort()`

## Installation 
Execute: `composer require mirzaev/minimal`

## Usage
*index.php*
```php
// Initializing the core
$core = new core(namespace: __NAMESPACE__);

// Initializing routes
$core->router
	->write('/', new route('index', 'index'), 'GET')
	->write('/search', new route('search', 'search'), 'POST')
	->write('/product/create', new route('product', 'create'), 'PUT')
	->write('/product/$id', new route('product', 'read'), 'GET')
	->write('/product/$id', new route('product', 'read'), 'POST')
	->write('/$categories', new route('categories', 'read'), 'GET') // Collector (since 0.3.0)
;

// Handling the request
$core->start();
```
