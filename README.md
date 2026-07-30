# DabDash PHP SDK

PHP client for the [DabDash](https://dabdash.com/) Tenant API.

Namespace: **`ShadowSoftware\DabDash`** (generated from `openapi.json` by
[`shadow-software/sdk-release`](https://github.com/shadow-software/sdk-release)).
Do not edit `generated/` by hand.

## Install

```bash
composer require shadow-software/dabdash-php-sdk
```

Requires PHP 8.1+.

Until Packagist lists the package, add a VCS repository:

```json
{
  "repositories": [
    { "type": "vcs", "url": "https://github.com/shadow-software/dabdash-php-sdk" }
  ],
  "require": {
    "shadow-software/dabdash-php-sdk": "^0.2"
  }
}
```

## Usage

```php
use ShadowSoftware\DabDash\Configuration;
use ShadowSoftware\DabDash\Api\CustomersApi;

$config = Configuration::getDefaultConfiguration()
    ->setHost('https://your-tenant.dabdash.com')
    ->setAccessToken($accessToken);

$api = new CustomersApi(null, $config);
```

## License

MIT
