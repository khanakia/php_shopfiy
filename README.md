## How to use

```php
<?php
require 'vendor/autoload.php';

use Khanakia\Shopify\ReviewExtractor;

$ext = new ReviewExtractor('myapp');
$ext->do(); // extract all the pages
$ext->prettyPrint();
```

### If we weant to extract single page
```php
$ext->do(2);
```

