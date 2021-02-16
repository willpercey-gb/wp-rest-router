# Rest Router for WordPress

### Installation 
```bash
$ composer require uwebpro/wp-rest-router
```


### Usage

```php
use UWebPro\WordPress\Rest\Router;

require_once 'vendor/autoload.php';

$router = new Router('namespace');

//Example one
$router->get('/endpoint')->uses($callback);

//Example two
$router->get('/endpoint', $callback);

//With Parameters

$router->get('/endpoint/{parameter_one}/{?optional_parameter}')->uses($callback);

//Usage

$router->get('/navigation')->uses(function()
{
    // processing 
    return new \WP_REST_Response(['data here']); // response;
});

```