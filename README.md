# GSM Arena API (gsmarena.com)
PHP Class for grab data on [gsmarena.com](https://gsmarena.com/) website and output Array or JSON using cURL and simple html dom.

### Usage:
 
```php
// Include class gsm.php
require 'gsm.php';

// Create object
$gsm = new GsmArena();
```
### Brands:

```php
$data = $gsm->getBrands();
```

### Search:
```php
$data = $gsm->search('zenfone');
```

### Detail:
```php
$data = $gsm->detail('asus_zenfone_max_zc550kl-7476'); // Slug
```

### Return Array:
```php
print_r($data);
```

### Return JSON:
```php
// Convert ARRAY to JSON
header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);
```
