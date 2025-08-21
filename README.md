# MINIMAL
The best code-to-utility Framework

## Nearest plans (2025)
1. **Middlewares** technology
2. Route sorting `router::sort()`
3. Processing routes from within routes (request emulation)

## Installation 
Execute: `composer require mirzaev/minimal`

## Usage
*index.php*
```php
// Initializing core
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

// Handling request
$core->start();
```

## Projects based on MINIMAL

### ebala
**Repository:** https://git.svoboda.works/mirzaev/ebala<br>
**Github mirror:** https://github.com/mature-woman/ebala<br>
*I earned more than a **million rubles** from this project*<br>
*Repositories **may** be closed at the request of the customer*<br>

### Arming 
**Repository:** https://git.svoboda.works/weby/arming<br>
**Guthub mirror:** https://github.com/WEBY-GROUP/arming<br>
*Telegram chat-robot marketplace*<br>

### not.chat
**Repository:** https://git.svoboda.works/mirzaev/notchat<br>
**Github mirror:** https://github.com/mature-woman/notchat<br>
*P2P chat project with blockchains and smart stuff*<br>
