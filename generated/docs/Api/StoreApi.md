# ShadowSoftware\Sdk\Generated\StoreApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**storeInfo()**](StoreApi.md#storeInfo) | **POST** /api/v1/tools/store_info | Identify the connected store — name, slug, timezone, currency, country, and subscription status. Use to validate an API token during setup. |


## `storeInfo()`

```php
storeInfo($body): \ShadowSoftware\Sdk\Generated\Model\StoreInfo200Response
```

Identify the connected store — name, slug, timezone, currency, country, and subscription status. Use to validate an API token during setup.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\StoreApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = array('key' => new \stdClass); // object

try {
    $result = $apiInstance->storeInfo($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling StoreApi->storeInfo: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **body** | **object**|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\StoreInfo200Response**](../Model/StoreInfo200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
