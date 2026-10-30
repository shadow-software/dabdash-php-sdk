# ShadowSoftware\DabDash\StoreApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**deliverySettingsManage()**](StoreApi.md#deliverySettingsManage) | **POST** /api/v1/tools/delivery_settings_manage | Show or configure tenant delivery operations: logistics gate, driver self-serve, customer tracking visibility, proof of delivery, driver notification suppressions, and auto-dispatch. Set uses dry_run&#x3D;true by default and writes are audited. Enable delivery logistics before enabling dependent driver controls. WhatsApp is not changed by this tool. |
| [**storeInfo()**](StoreApi.md#storeInfo) | **POST** /api/v1/tools/store_info | Identify the connected store — name, slug, timezone, currency, country, and subscription status. Use to validate an API token during setup. |
| [**storeSettingsManage()**](StoreApi.md#storeSettingsManage) | **POST** /api/v1/tools/store_settings_manage | Show or update a store&#39;s core settings: display name, phone, address, coordinates, timezone, currency, country, locale, brand colours, and opening hours.  ACTIONS:   show (default): current values.   set: pass only the fields to change. dry_run defaults true — the first call        reports the diff without writing; pass dry_run&#x3D;false to save.  Brand colours: theme_color is the primary, accent_color the secondary (both #RRGGBB; a 3-digit #RGB accent is expanded). button_text_color is auto (default: white or near-black, whichever contrasts more), white, or dark. Every response includes &#x60;palette&#x60;: the effective text colour on primary/accent, its WCAG contrast ratio, and warnings for any pairing under 4.5:1 — dry-run first to preview a palette.  opening_hours is a list of {days: [\&quot;Monday\&quot;, ...], opens: \&quot;09:00\&quot;, closes: \&quot;21:00\&quot;} rows (24h HH:MM, full English day names). Passing it replaces all rows; pass an empty list to clear. Hours feed the storefront About page and search-engine markup. storefront_theme picks the storefront look: classic (default) or modern. theme_mode controls the storefront colour mode: light or dark. hero_* fields preserve the vendor&#39;s full hero message and modern-theme content. The Modern hero featured-product card is optional: hero_spotlight_mode is auto (newest sale), product (pin one), or off (hide the card). Pass hero_spotlight_product (name or slug) or hero_spotlight_product_id to pin a product; omitting the mode then sets it to product. Badge, caption, and button are hero_spotlight_label, hero_spotlight_kicker, and hero_spotlight_cta. product_feature_manage only toggles the separate Featured Products grid. The same card and the rest of the Modern homepage/footer are also on storefront_theme_manage. Vendors edit the card in Settings → Theme. hero_ticker_items replaces custom ticker rows with {label, tag, url} objects; URLs must be site paths or https:// links. Use hero_ticker_mode&#x3D;custom to show them. hero_status_mode controls the Modern hero online/last-call pill: hours (from opening_hours), auto (delivery slots when available, else hours), or off. minimum_age is the storefront age gate / About compliance age (e.g. 19 in NS). footer_notes and about_* set Modern footer notes and About intro copy. privacy_url is the authoritative published privacy-policy URL. Use the exact blog URL when the policy is a blog post. delivery_meta_description is an optional, tenant-specific description for the public /delivery page. Keep it factual and 160 characters or fewer. social_facebook / social_instagram / social_twitter / social_x / google_business_url / gbp_url are absolute https URLs published as Organization and LocalBusiness sameAs (Knowledge Panel alignment). freebie_cost_in_cogs (default true) includes giveaway freebie wholesale cost in vendor COGS / margin reports even though freebie revenue is $0. Slug, owner, subscription, and custom domain are not changed here. |


## `deliverySettingsManage()`

```php
deliverySettingsManage($delivery_settings_manage_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Show or configure tenant delivery operations: logistics gate, driver self-serve, customer tracking visibility, proof of delivery, driver notification suppressions, and auto-dispatch. Set uses dry_run=true by default and writes are audited. Enable delivery logistics before enabling dependent driver controls. WhatsApp is not changed by this tool.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\StoreApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$delivery_settings_manage_request = new \ShadowSoftware\DabDash\Model\DeliverySettingsManageRequest(); // \ShadowSoftware\DabDash\Model\DeliverySettingsManageRequest

try {
    $result = $apiInstance->deliverySettingsManage($delivery_settings_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling StoreApi->deliverySettingsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **delivery_settings_manage_request** | [**\ShadowSoftware\DabDash\Model\DeliverySettingsManageRequest**](../Model/DeliverySettingsManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactCleanup200Response**](../Model/ContactCleanup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `storeInfo()`

```php
storeInfo($body): \ShadowSoftware\DabDash\Model\StoreInfo200Response
```

Identify the connected store — name, slug, timezone, currency, country, and subscription status. Use to validate an API token during setup.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\StoreApi(
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

[**\ShadowSoftware\DabDash\Model\StoreInfo200Response**](../Model/StoreInfo200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `storeSettingsManage()`

```php
storeSettingsManage($store_settings_manage_request): \ShadowSoftware\DabDash\Model\StoreSettingsManage200Response
```

Show or update a store's core settings: display name, phone, address, coordinates, timezone, currency, country, locale, brand colours, and opening hours.  ACTIONS:   show (default): current values.   set: pass only the fields to change. dry_run defaults true — the first call        reports the diff without writing; pass dry_run=false to save.  Brand colours: theme_color is the primary, accent_color the secondary (both #RRGGBB; a 3-digit #RGB accent is expanded). button_text_color is auto (default: white or near-black, whichever contrasts more), white, or dark. Every response includes `palette`: the effective text colour on primary/accent, its WCAG contrast ratio, and warnings for any pairing under 4.5:1 — dry-run first to preview a palette.  opening_hours is a list of {days: [\"Monday\", ...], opens: \"09:00\", closes: \"21:00\"} rows (24h HH:MM, full English day names). Passing it replaces all rows; pass an empty list to clear. Hours feed the storefront About page and search-engine markup. storefront_theme picks the storefront look: classic (default) or modern. theme_mode controls the storefront colour mode: light or dark. hero_* fields preserve the vendor's full hero message and modern-theme content. The Modern hero featured-product card is optional: hero_spotlight_mode is auto (newest sale), product (pin one), or off (hide the card). Pass hero_spotlight_product (name or slug) or hero_spotlight_product_id to pin a product; omitting the mode then sets it to product. Badge, caption, and button are hero_spotlight_label, hero_spotlight_kicker, and hero_spotlight_cta. product_feature_manage only toggles the separate Featured Products grid. The same card and the rest of the Modern homepage/footer are also on storefront_theme_manage. Vendors edit the card in Settings → Theme. hero_ticker_items replaces custom ticker rows with {label, tag, url} objects; URLs must be site paths or https:// links. Use hero_ticker_mode=custom to show them. hero_status_mode controls the Modern hero online/last-call pill: hours (from opening_hours), auto (delivery slots when available, else hours), or off. minimum_age is the storefront age gate / About compliance age (e.g. 19 in NS). footer_notes and about_* set Modern footer notes and About intro copy. privacy_url is the authoritative published privacy-policy URL. Use the exact blog URL when the policy is a blog post. delivery_meta_description is an optional, tenant-specific description for the public /delivery page. Keep it factual and 160 characters or fewer. social_facebook / social_instagram / social_twitter / social_x / google_business_url / gbp_url are absolute https URLs published as Organization and LocalBusiness sameAs (Knowledge Panel alignment). freebie_cost_in_cogs (default true) includes giveaway freebie wholesale cost in vendor COGS / margin reports even though freebie revenue is $0. Slug, owner, subscription, and custom domain are not changed here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\StoreApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$store_settings_manage_request = new \ShadowSoftware\DabDash\Model\StoreSettingsManageRequest(); // \ShadowSoftware\DabDash\Model\StoreSettingsManageRequest

try {
    $result = $apiInstance->storeSettingsManage($store_settings_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling StoreApi->storeSettingsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **store_settings_manage_request** | [**\ShadowSoftware\DabDash\Model\StoreSettingsManageRequest**](../Model/StoreSettingsManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\StoreSettingsManage200Response**](../Model/StoreSettingsManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
