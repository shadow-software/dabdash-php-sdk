# ShadowSoftware\DabDash\ZonesApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**zoneClone()**](ZonesApi.md#zoneClone) | **POST** /api/v1/tools/zone_clone | Clone every polygon zone from a source tenant into the target tenant while preserving the target tenant&#39;s existing fee tiers. This is intended for importing a proven coverage map without importing another store&#39;s pricing.  The source zones are grouped into tiers by their minimum order, then mapped by rank to the target tenant&#39;s existing zones ordered by minimum order. The target tier&#39;s delivery_fee, service_fee, min_order, color, priority, and fulfillment_type are applied to every cloned source polygon. Existing target zones are replaced only when replace_existing&#x3D;true and confirm&#x3D;true. Zone colors use an evenly spaced spectrum from the target primary colour to its accent colour, one stop per fee rung. dry_run defaults true. |
| [**zoneDiagnostics()**](ZonesApi.md#zoneDiagnostics) | **POST** /api/v1/tools/zone_diagnostics | Inspect zone polygons against customer or order coordinates to explain why an address is inside or outside delivery coverage. |
| [**zoneUpsert()**](ZonesApi.md#zoneUpsert) | **POST** /api/v1/tools/zone_upsert | Create or update a delivery/pickup/shipping zone. Use zone_manage to list or delete.  CREATE (no zone_id): name is required; a zone with the same name is updated instead. UPDATE (zone_id): only the fields you pass change.  Coverage shape — pass ONE of:   polygon: list of {lat, lng} points (or [lng, lat] pairs), at least 3, at most 500.   center_lat + center_lng + radius_km: builds a 32-point circle. Good enough when            the client only publishes a list of towns; refine the polygon later. Fees and minimum order are in dollars. dry_run defaults true: the first call reports what would be saved; pass dry_run&#x3D;false to write. |


## `zoneClone()`

```php
zoneClone($zone_clone_request): \ShadowSoftware\DabDash\Model\ZoneClone200Response
```

Clone every polygon zone from a source tenant into the target tenant while preserving the target tenant's existing fee tiers. This is intended for importing a proven coverage map without importing another store's pricing.  The source zones are grouped into tiers by their minimum order, then mapped by rank to the target tenant's existing zones ordered by minimum order. The target tier's delivery_fee, service_fee, min_order, color, priority, and fulfillment_type are applied to every cloned source polygon. Existing target zones are replaced only when replace_existing=true and confirm=true. Zone colors use an evenly spaced spectrum from the target primary colour to its accent colour, one stop per fee rung. dry_run defaults true.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ZonesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$zone_clone_request = new \ShadowSoftware\DabDash\Model\ZoneCloneRequest(); // \ShadowSoftware\DabDash\Model\ZoneCloneRequest

try {
    $result = $apiInstance->zoneClone($zone_clone_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ZonesApi->zoneClone: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **zone_clone_request** | [**\ShadowSoftware\DabDash\Model\ZoneCloneRequest**](../Model/ZoneCloneRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ZoneClone200Response**](../Model/ZoneClone200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `zoneDiagnostics()`

```php
zoneDiagnostics($zone_diagnostics_request): \ShadowSoftware\DabDash\Model\ZoneDiagnostics200Response
```

Inspect zone polygons against customer or order coordinates to explain why an address is inside or outside delivery coverage.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ZonesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$zone_diagnostics_request = new \ShadowSoftware\DabDash\Model\ZoneDiagnosticsRequest(); // \ShadowSoftware\DabDash\Model\ZoneDiagnosticsRequest

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
| **zone_diagnostics_request** | [**\ShadowSoftware\DabDash\Model\ZoneDiagnosticsRequest**](../Model/ZoneDiagnosticsRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ZoneDiagnostics200Response**](../Model/ZoneDiagnostics200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `zoneUpsert()`

```php
zoneUpsert($zone_upsert_request): \ShadowSoftware\DabDash\Model\ZoneUpsert200Response
```

Create or update a delivery/pickup/shipping zone. Use zone_manage to list or delete.  CREATE (no zone_id): name is required; a zone with the same name is updated instead. UPDATE (zone_id): only the fields you pass change.  Coverage shape — pass ONE of:   polygon: list of {lat, lng} points (or [lng, lat] pairs), at least 3, at most 500.   center_lat + center_lng + radius_km: builds a 32-point circle. Good enough when            the client only publishes a list of towns; refine the polygon later. Fees and minimum order are in dollars. dry_run defaults true: the first call reports what would be saved; pass dry_run=false to write.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ZonesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$zone_upsert_request = new \ShadowSoftware\DabDash\Model\ZoneUpsertRequest(); // \ShadowSoftware\DabDash\Model\ZoneUpsertRequest

try {
    $result = $apiInstance->zoneUpsert($zone_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ZonesApi->zoneUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **zone_upsert_request** | [**\ShadowSoftware\DabDash\Model\ZoneUpsertRequest**](../Model/ZoneUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ZoneUpsert200Response**](../Model/ZoneUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
