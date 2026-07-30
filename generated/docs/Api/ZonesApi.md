# ShadowSoftware\Sdk\Generated\ZonesApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**zoneDiagnostics()**](ZonesApi.md#zoneDiagnostics) | **POST** /api/v1/tools/zone_diagnostics | Inspect zone polygons against customer or order coordinates to explain why an address is inside or outside delivery coverage. |


## `zoneDiagnostics()`

```php
zoneDiagnostics($zone_diagnostics_request): \ShadowSoftware\Sdk\Generated\Model\ZoneDiagnostics200Response
```

Inspect zone polygons against customer or order coordinates to explain why an address is inside or outside delivery coverage.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\ZonesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$zone_diagnostics_request = new \ShadowSoftware\Sdk\Generated\Model\ZoneDiagnosticsRequest(); // \ShadowSoftware\Sdk\Generated\Model\ZoneDiagnosticsRequest

try {
    $result = $apiInstance->zoneDiagnostics($zone_diagnostics_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ZonesApi->zoneDiagnostics: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **zone_diagnostics_request** | [**\ShadowSoftware\Sdk\Generated\Model\ZoneDiagnosticsRequest**](../Model/ZoneDiagnosticsRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\ZoneDiagnostics200Response**](../Model/ZoneDiagnostics200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
