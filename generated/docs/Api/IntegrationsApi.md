# ShadowSoftware\Sdk\Generated\IntegrationsApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**metrcDiagnostics()**](IntegrationsApi.md#metrcDiagnostics) | **POST** /api/v1/tools/metrc_diagnostics | Returns a JSON summary of Metrc compliance status for a tenant: integration mode, sync states, audit log counts by HTTP status, and pending/failed report counts. Pass a tenant_slug to inspect a specific tenant. |
| [**pushNotificationDiagnostics()**](IntegrationsApi.md#pushNotificationDiagnostics) | **POST** /api/v1/tools/push_notification_diagnostics | Diagnose push notification (FCM) delivery for a vendor. Surfaces token health, notification settings, recent send history with push/email flags, and a plain-language diagnosis of why pushes are or are not being delivered. |


## `metrcDiagnostics()`

```php
metrcDiagnostics($body): \ShadowSoftware\Sdk\Generated\Model\MetrcDiagnostics200Response
```

Returns a JSON summary of Metrc compliance status for a tenant: integration mode, sync states, audit log counts by HTTP status, and pending/failed report counts. Pass a tenant_slug to inspect a specific tenant.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\IntegrationsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = array('key' => new \stdClass); // object

try {
    $result = $apiInstance->metrcDiagnostics($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->metrcDiagnostics: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **body** | **object**|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\MetrcDiagnostics200Response**](../Model/MetrcDiagnostics200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pushNotificationDiagnostics()`

```php
pushNotificationDiagnostics($push_notification_diagnostics_request): \ShadowSoftware\Sdk\Generated\Model\PushNotificationDiagnostics200Response
```

Diagnose push notification (FCM) delivery for a vendor. Surfaces token health, notification settings, recent send history with push/email flags, and a plain-language diagnosis of why pushes are or are not being delivered.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\IntegrationsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$push_notification_diagnostics_request = new \ShadowSoftware\Sdk\Generated\Model\PushNotificationDiagnosticsRequest(); // \ShadowSoftware\Sdk\Generated\Model\PushNotificationDiagnosticsRequest

try {
    $result = $apiInstance->pushNotificationDiagnostics($push_notification_diagnostics_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->pushNotificationDiagnostics: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **push_notification_diagnostics_request** | [**\ShadowSoftware\Sdk\Generated\Model\PushNotificationDiagnosticsRequest**](../Model/PushNotificationDiagnosticsRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\PushNotificationDiagnostics200Response**](../Model/PushNotificationDiagnostics200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
