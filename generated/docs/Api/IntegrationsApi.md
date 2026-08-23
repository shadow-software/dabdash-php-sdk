# ShadowSoftware\DabDash\IntegrationsApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**metrcDiagnostics()**](IntegrationsApi.md#metrcDiagnostics) | **POST** /api/v1/tools/metrc_diagnostics | Returns a JSON summary of Metrc compliance status for a tenant: integration mode, sync states, audit log counts by HTTP status, and pending/failed report counts. Pass a tenant_slug to inspect a specific tenant. |
| [**pushNotificationDiagnostics()**](IntegrationsApi.md#pushNotificationDiagnostics) | **POST** /api/v1/tools/push_notification_diagnostics | Diagnose push notification (FCM) delivery for a vendor. Surfaces token health, notification settings, recent send history with push/email flags, and a plain-language diagnosis of why pushes are or are not being delivered. |
| [**tenantBrandingManage()**](IntegrationsApi.md#tenantBrandingManage) | **POST** /api/v1/tools/tenant_branding_manage | Show or update a tenant&#39;s store logo, homepage hero image, and BIMI email logo, then upsert the Cloudflare &#x60;default._bimi&#x60; TXT record when the zone is connected.  ACTIONS:   show (default): current logo_path, storefront logo URL (custom domain when          set), hero_image_path/URL, BIMI path/URL, the TXT value to publish,          and whether Cloudflare is connected.   set: pass media_id (from media_list / media_upload) to point logo_path at a          library asset. Pass hero_media_id for settings.hero_image_path, or          clear_hero&#x3D;true to remove the hero. Pass exactly one of source_base64 /          source_path / source_url for an SVG to store the BIMI logo at          tenants/{id}/bimi-logo.svg (media_upload cannot take SVG). dry_run          defaults true — the first call reports what would happen.  Logo URLs and BIMI &#x60;l&#x3D;&#x60; values always use the tenant storefront host (custom domain or {slug}.dabdash.com), never the platform APP_URL. |


## `metrcDiagnostics()`

```php
metrcDiagnostics($body): \ShadowSoftware\DabDash\Model\MetrcDiagnostics200Response
```

Returns a JSON summary of Metrc compliance status for a tenant: integration mode, sync states, audit log counts by HTTP status, and pending/failed report counts. Pass a tenant_slug to inspect a specific tenant.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\IntegrationsApi(
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

[**\ShadowSoftware\DabDash\Model\MetrcDiagnostics200Response**](../Model/MetrcDiagnostics200Response.md)

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
pushNotificationDiagnostics($push_notification_diagnostics_request): \ShadowSoftware\DabDash\Model\PushNotificationDiagnostics200Response
```

Diagnose push notification (FCM) delivery for a vendor. Surfaces token health, notification settings, recent send history with push/email flags, and a plain-language diagnosis of why pushes are or are not being delivered.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\IntegrationsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$push_notification_diagnostics_request = new \ShadowSoftware\DabDash\Model\PushNotificationDiagnosticsRequest(); // \ShadowSoftware\DabDash\Model\PushNotificationDiagnosticsRequest

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
| **push_notification_diagnostics_request** | [**\ShadowSoftware\DabDash\Model\PushNotificationDiagnosticsRequest**](../Model/PushNotificationDiagnosticsRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PushNotificationDiagnostics200Response**](../Model/PushNotificationDiagnostics200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantBrandingManage()`

```php
tenantBrandingManage($tenant_branding_manage_request): \ShadowSoftware\DabDash\Model\TenantBrandingManage200Response
```

Show or update a tenant's store logo, homepage hero image, and BIMI email logo, then upsert the Cloudflare `default._bimi` TXT record when the zone is connected.  ACTIONS:   show (default): current logo_path, storefront logo URL (custom domain when          set), hero_image_path/URL, BIMI path/URL, the TXT value to publish,          and whether Cloudflare is connected.   set: pass media_id (from media_list / media_upload) to point logo_path at a          library asset. Pass hero_media_id for settings.hero_image_path, or          clear_hero=true to remove the hero. Pass exactly one of source_base64 /          source_path / source_url for an SVG to store the BIMI logo at          tenants/{id}/bimi-logo.svg (media_upload cannot take SVG). dry_run          defaults true — the first call reports what would happen.  Logo URLs and BIMI `l=` values always use the tenant storefront host (custom domain or {slug}.dabdash.com), never the platform APP_URL.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\IntegrationsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$tenant_branding_manage_request = new \ShadowSoftware\DabDash\Model\TenantBrandingManageRequest(); // \ShadowSoftware\DabDash\Model\TenantBrandingManageRequest

try {
    $result = $apiInstance->tenantBrandingManage($tenant_branding_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->tenantBrandingManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_branding_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantBrandingManageRequest**](../Model/TenantBrandingManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantBrandingManage200Response**](../Model/TenantBrandingManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
