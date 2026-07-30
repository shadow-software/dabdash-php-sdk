# ShadowSoftware\DabDash\ReadApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**analyticsQuery()**](ReadApi.md#analyticsQuery) | **POST** /api/v1/tools/analytics_query | Run read-only analytics queries against the production database. Available reports: revenue_by_tenant, orders_by_status, top_products, revenue_over_time, customer_stats. Revenue dating uses RevenueAttribution (pass tenant_slug for delivered-mode tenants). |
| [**catalogFlatteningAudit()**](ReadApi.md#catalogFlatteningAudit) | **POST** /api/v1/tools/catalog_flattening_audit | Read-only. Finds products whose sizes were split into separate products instead of tiers.  This happens when a catalog is imported from a store that put the size in the product NAME (\&quot;Blue Dream - 3.5G\&quot;, \&quot;Blue Dream - 7G\&quot;) instead of a size option column. The importer has no size column to read, so each size becomes its own product with a single \&quot;Default\&quot; option, and the store ends up with a long flat menu that cannot use weight pricing or mix &amp; match deals.  Returns each group of products that belong together (\&quot;family\&quot;), the sizes and prices found, and whether the group can be safely merged. A group is NOT mergeable when two of its products claim the same size — that must be resolved by hand first.  Nothing is changed. Use catalog_collapse to merge a group. |
| [**couponList()**](ReadApi.md#couponList) | **POST** /api/v1/tools/coupon_list | List a tenant&#39;s discount coupons with code, type, value, usage limits, redemption count, active state, and schedule window. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_list for those).  type:   percentage     → value is a percentage 0-100 off the order subtotal.   fixed          → value is dollars off the order subtotal.   free_delivery  → waives the delivery fee only; value is unused for this type.  Always call this before making coupon-related decisions to see current codes, usage caps, and whether a coupon has already been exhausted (used_count vs max_uses). |
| [**customerAddresses()**](ReadApi.md#customerAddresses) | **POST** /api/v1/tools/customer_addresses | Return a customer&#39;s saved addresses, coordinates, saved zones, and zone mismatch diagnostics. |
| [**customerList()**](ReadApi.md#customerList) | **POST** /api/v1/tools/customer_list | Page through all customers for a tenant, optionally filtered to those updated since a given time. Built for bulk sync — use customer_lookup instead for a single targeted search. |
| [**customerLookup()**](ReadApi.md#customerLookup) | **POST** /api/v1/tools/customer_lookup | Find customers by id, email, phone, or name and return their recent addresses, orders, and support context. |
| [**freebieList()**](ReadApi.md#freebieList) | **POST** /api/v1/tools/freebie_list | List a tenant&#39;s freebie rules (\&quot;spend $X, get a free item\&quot;) with id, name, spend threshold, the product/variation given away, quantity, stackable flag, active state, and schedule window. Freebies are evaluated on every cart change by FreebieService: a rule fires once its spend_threshold is met, adding &#x60;quantity&#x60; of the configured product/variation to the cart.  IMPORTANT: this reads the &#x60;freebies&#x60; table — the source of truth the storefront cart uses. It is NOT the legacy freebie summary promotion_audit surfaces alongside coupons/bundles; that view is stale display-only data built for a different purpose. Trust this tool for what actually applies at checkout.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order (each            rule still only fires once, at its configured quantity, regardless of how far above            its own threshold the cart is).   false  → this rule cannot combine with other freebie rules; if multiple non-stackable rules            qualify, FreebieService applies its own precedence to pick one.   This flag does NOT multiply a single rule&#39;s quantity by how many multiples of the threshold   the cart reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3. |
| [**googleAnalytics()**](ReadApi.md#googleAnalytics) | **POST** /api/v1/tools/google_analytics | Query Google Analytics (GA4) data for the platform (dabdash.com) or a specific tenant with a connected GA integration. Returns traffic overview, top pages, traffic sources, top events, and daily trend. |
| [**inventoryAuditLookup()**](ReadApi.md#inventoryAuditLookup) | **POST** /api/v1/tools/inventory_audit_lookup | Look up the historical inventory state of a list of products from the inventory_audit_logs table.  For each product, returns every variation_id ever logged in the audit trail (including variations that have since been deleted), the variation&#39;s last known stock_quantity strictly BEFORE the given cutoff timestamp, and that variation&#39;s most recent action+notes for context.  Use this tool to recover pre-incident stock values when variations have been overwritten or deleted by a destructive operation (e.g. an erroneous pricing structure assignment that wiped unit/simple variations and replaced them with weight tiers).  Output is grouped per product. Each product also includes its CURRENT variations and their stock for comparison so you can see the delta. |
| [**inventoryStatus()**](ReadApi.md#inventoryStatus) | **POST** /api/v1/tools/inventory_status | Get inventory status across all tenants or a specific tenant. Shows low stock alerts and out-of-stock products. |
| [**mailboxInspect()**](ReadApi.md#mailboxInspect) | **POST** /api/v1/tools/mailbox_inspect | Inspect a tenant inbound mailbox: sync watermark, last error, recent ingestion counts (inbound/outbound), and a healthy/bootstrap/stalled/quiet verdict. Pass a tenant_slug, or pass platform&#x3D;true for a platform-owned mailbox (tenant_id IS NULL). |
| [**metrcDiagnostics()**](ReadApi.md#metrcDiagnostics) | **POST** /api/v1/tools/metrc_diagnostics | Returns a JSON summary of Metrc compliance status for a tenant: integration mode, sync states, audit log counts by HTTP status, and pending/failed report counts. Pass a tenant_slug to inspect a specific tenant. |
| [**orderDashboard()**](ReadApi.md#orderDashboard) | **POST** /api/v1/tools/order_dashboard | Query orders across all tenants. Filter by status, order number, customer clues, date range, amount, or tenant. Returns order list with pricing context. |
| [**productInspect()**](ReadApi.md#productInspect) | **POST** /api/v1/tools/product_inspect | Inspect a specific product including every variation&#39;s price, compare_at_price, mix_match_tags, stock, and the tenant&#39;s mix &amp; match rule settings. Use this to audit pricing, sale state, and bundle configuration for support tickets. |
| [**promotionAudit()**](ReadApi.md#promotionAudit) | **POST** /api/v1/tools/promotion_audit | Inspect coupons, freebies, mix and match rules, loyalty settings, and storewide sale state for overcharge or missed-discount support tickets. |
| [**pushNotificationDiagnostics()**](ReadApi.md#pushNotificationDiagnostics) | **POST** /api/v1/tools/push_notification_diagnostics | Diagnose push notification (FCM) delivery for a vendor. Surfaces token health, notification settings, recent send history with push/email flags, and a plain-language diagnosis of why pushes are or are not being delivered. |
| [**searchConsole()**](ReadApi.md#searchConsole) | **POST** /api/v1/tools/search_console | Query Google Search Console data for the platform (dabdash.com) or a specific tenant with a connected GSC integration. Returns search overview, top queries, top pages, and daily trend. |
| [**storeInfo()**](ReadApi.md#storeInfo) | **POST** /api/v1/tools/store_info | Identify the connected store — name, slug, timezone, currency, country, and subscription status. Use to validate an API token during setup. |
| [**zoneDiagnostics()**](ReadApi.md#zoneDiagnostics) | **POST** /api/v1/tools/zone_diagnostics | Inspect zone polygons against customer or order coordinates to explain why an address is inside or outside delivery coverage. |


## `analyticsQuery()`

```php
analyticsQuery($analytics_query_request): \ShadowSoftware\DabDash\Model\AnalyticsQuery200Response
```

Run read-only analytics queries against the production database. Available reports: revenue_by_tenant, orders_by_status, top_products, revenue_over_time, customer_stats. Revenue dating uses RevenueAttribution (pass tenant_slug for delivered-mode tenants).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$analytics_query_request = new \ShadowSoftware\DabDash\Model\AnalyticsQueryRequest(); // \ShadowSoftware\DabDash\Model\AnalyticsQueryRequest

try {
    $result = $apiInstance->analyticsQuery($analytics_query_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->analyticsQuery: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **analytics_query_request** | [**\ShadowSoftware\DabDash\Model\AnalyticsQueryRequest**](../Model/AnalyticsQueryRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\AnalyticsQuery200Response**](../Model/AnalyticsQuery200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `catalogFlatteningAudit()`

```php
catalogFlatteningAudit($body): \ShadowSoftware\DabDash\Model\CatalogFlatteningAudit200Response
```

Read-only. Finds products whose sizes were split into separate products instead of tiers.  This happens when a catalog is imported from a store that put the size in the product NAME (\"Blue Dream - 3.5G\", \"Blue Dream - 7G\") instead of a size option column. The importer has no size column to read, so each size becomes its own product with a single \"Default\" option, and the store ends up with a long flat menu that cannot use weight pricing or mix & match deals.  Returns each group of products that belong together (\"family\"), the sizes and prices found, and whether the group can be safely merged. A group is NOT mergeable when two of its products claim the same size — that must be resolved by hand first.  Nothing is changed. Use catalog_collapse to merge a group.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$body = array('key' => new \stdClass); // object

try {
    $result = $apiInstance->catalogFlatteningAudit($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->catalogFlatteningAudit: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **body** | **object**|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CatalogFlatteningAudit200Response**](../Model/CatalogFlatteningAudit200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `couponList()`

```php
couponList($coupon_list_request): \ShadowSoftware\DabDash\Model\CouponList200Response
```

List a tenant's discount coupons with code, type, value, usage limits, redemption count, active state, and schedule window. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_list for those).  type:   percentage     → value is a percentage 0-100 off the order subtotal.   fixed          → value is dollars off the order subtotal.   free_delivery  → waives the delivery fee only; value is unused for this type.  Always call this before making coupon-related decisions to see current codes, usage caps, and whether a coupon has already been exhausted (used_count vs max_uses).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$coupon_list_request = new \ShadowSoftware\DabDash\Model\CouponListRequest(); // \ShadowSoftware\DabDash\Model\CouponListRequest

try {
    $result = $apiInstance->couponList($coupon_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->couponList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **coupon_list_request** | [**\ShadowSoftware\DabDash\Model\CouponListRequest**](../Model/CouponListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CouponList200Response**](../Model/CouponList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `customerAddresses()`

```php
customerAddresses($customer_addresses_request): \ShadowSoftware\DabDash\Model\CustomerAddresses200Response
```

Return a customer's saved addresses, coordinates, saved zones, and zone mismatch diagnostics.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$customer_addresses_request = new \ShadowSoftware\DabDash\Model\CustomerAddressesRequest(); // \ShadowSoftware\DabDash\Model\CustomerAddressesRequest

try {
    $result = $apiInstance->customerAddresses($customer_addresses_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->customerAddresses: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_addresses_request** | [**\ShadowSoftware\DabDash\Model\CustomerAddressesRequest**](../Model/CustomerAddressesRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerAddresses200Response**](../Model/CustomerAddresses200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `customerList()`

```php
customerList($customer_list_request): \ShadowSoftware\DabDash\Model\CustomerList200Response
```

Page through all customers for a tenant, optionally filtered to those updated since a given time. Built for bulk sync — use customer_lookup instead for a single targeted search.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$customer_list_request = new \ShadowSoftware\DabDash\Model\CustomerListRequest(); // \ShadowSoftware\DabDash\Model\CustomerListRequest

try {
    $result = $apiInstance->customerList($customer_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->customerList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_list_request** | [**\ShadowSoftware\DabDash\Model\CustomerListRequest**](../Model/CustomerListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerList200Response**](../Model/CustomerList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `customerLookup()`

```php
customerLookup($customer_lookup_request): \ShadowSoftware\DabDash\Model\CustomerLookup200Response
```

Find customers by id, email, phone, or name and return their recent addresses, orders, and support context.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$customer_lookup_request = new \ShadowSoftware\DabDash\Model\CustomerLookupRequest(); // \ShadowSoftware\DabDash\Model\CustomerLookupRequest

try {
    $result = $apiInstance->customerLookup($customer_lookup_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->customerLookup: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_lookup_request** | [**\ShadowSoftware\DabDash\Model\CustomerLookupRequest**](../Model/CustomerLookupRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerLookup200Response**](../Model/CustomerLookup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `freebieList()`

```php
freebieList($freebie_list_request): \ShadowSoftware\DabDash\Model\FreebieList200Response
```

List a tenant's freebie rules (\"spend $X, get a free item\") with id, name, spend threshold, the product/variation given away, quantity, stackable flag, active state, and schedule window. Freebies are evaluated on every cart change by FreebieService: a rule fires once its spend_threshold is met, adding `quantity` of the configured product/variation to the cart.  IMPORTANT: this reads the `freebies` table — the source of truth the storefront cart uses. It is NOT the legacy freebie summary promotion_audit surfaces alongside coupons/bundles; that view is stale display-only data built for a different purpose. Trust this tool for what actually applies at checkout.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order (each            rule still only fires once, at its configured quantity, regardless of how far above            its own threshold the cart is).   false  → this rule cannot combine with other freebie rules; if multiple non-stackable rules            qualify, FreebieService applies its own precedence to pick one.   This flag does NOT multiply a single rule's quantity by how many multiples of the threshold   the cart reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$freebie_list_request = new \ShadowSoftware\DabDash\Model\FreebieListRequest(); // \ShadowSoftware\DabDash\Model\FreebieListRequest

try {
    $result = $apiInstance->freebieList($freebie_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->freebieList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **freebie_list_request** | [**\ShadowSoftware\DabDash\Model\FreebieListRequest**](../Model/FreebieListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\FreebieList200Response**](../Model/FreebieList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `googleAnalytics()`

```php
googleAnalytics($google_analytics_request): \ShadowSoftware\DabDash\Model\GoogleAnalytics200Response
```

Query Google Analytics (GA4) data for the platform (dabdash.com) or a specific tenant with a connected GA integration. Returns traffic overview, top pages, traffic sources, top events, and daily trend.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$google_analytics_request = new \ShadowSoftware\DabDash\Model\GoogleAnalyticsRequest(); // \ShadowSoftware\DabDash\Model\GoogleAnalyticsRequest

try {
    $result = $apiInstance->googleAnalytics($google_analytics_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->googleAnalytics: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **google_analytics_request** | [**\ShadowSoftware\DabDash\Model\GoogleAnalyticsRequest**](../Model/GoogleAnalyticsRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\GoogleAnalytics200Response**](../Model/GoogleAnalytics200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `inventoryAuditLookup()`

```php
inventoryAuditLookup($inventory_audit_lookup_request): \ShadowSoftware\DabDash\Model\InventoryAuditLookup200Response
```

Look up the historical inventory state of a list of products from the inventory_audit_logs table.  For each product, returns every variation_id ever logged in the audit trail (including variations that have since been deleted), the variation's last known stock_quantity strictly BEFORE the given cutoff timestamp, and that variation's most recent action+notes for context.  Use this tool to recover pre-incident stock values when variations have been overwritten or deleted by a destructive operation (e.g. an erroneous pricing structure assignment that wiped unit/simple variations and replaced them with weight tiers).  Output is grouped per product. Each product also includes its CURRENT variations and their stock for comparison so you can see the delta.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$inventory_audit_lookup_request = new \ShadowSoftware\DabDash\Model\InventoryAuditLookupRequest(); // \ShadowSoftware\DabDash\Model\InventoryAuditLookupRequest

try {
    $result = $apiInstance->inventoryAuditLookup($inventory_audit_lookup_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->inventoryAuditLookup: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **inventory_audit_lookup_request** | [**\ShadowSoftware\DabDash\Model\InventoryAuditLookupRequest**](../Model/InventoryAuditLookupRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\InventoryAuditLookup200Response**](../Model/InventoryAuditLookup200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `inventoryStatus()`

```php
inventoryStatus($inventory_status_request): \ShadowSoftware\DabDash\Model\InventoryStatus200Response
```

Get inventory status across all tenants or a specific tenant. Shows low stock alerts and out-of-stock products.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$inventory_status_request = new \ShadowSoftware\DabDash\Model\InventoryStatusRequest(); // \ShadowSoftware\DabDash\Model\InventoryStatusRequest

try {
    $result = $apiInstance->inventoryStatus($inventory_status_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->inventoryStatus: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **inventory_status_request** | [**\ShadowSoftware\DabDash\Model\InventoryStatusRequest**](../Model/InventoryStatusRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\InventoryStatus200Response**](../Model/InventoryStatus200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `mailboxInspect()`

```php
mailboxInspect($mailbox_inspect_request): \ShadowSoftware\DabDash\Model\MailboxInspect200Response
```

Inspect a tenant inbound mailbox: sync watermark, last error, recent ingestion counts (inbound/outbound), and a healthy/bootstrap/stalled/quiet verdict. Pass a tenant_slug, or pass platform=true for a platform-owned mailbox (tenant_id IS NULL).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$mailbox_inspect_request = new \ShadowSoftware\DabDash\Model\MailboxInspectRequest(); // \ShadowSoftware\DabDash\Model\MailboxInspectRequest

try {
    $result = $apiInstance->mailboxInspect($mailbox_inspect_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->mailboxInspect: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **mailbox_inspect_request** | [**\ShadowSoftware\DabDash\Model\MailboxInspectRequest**](../Model/MailboxInspectRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\MailboxInspect200Response**](../Model/MailboxInspect200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
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
    echo 'Exception when calling ReadApi->metrcDiagnostics: ', $e->getMessage(), PHP_EOL;
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

## `orderDashboard()`

```php
orderDashboard($order_dashboard_request): \ShadowSoftware\DabDash\Model\OrderDashboard200Response
```

Query orders across all tenants. Filter by status, order number, customer clues, date range, amount, or tenant. Returns order list with pricing context.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$order_dashboard_request = new \ShadowSoftware\DabDash\Model\OrderDashboardRequest(); // \ShadowSoftware\DabDash\Model\OrderDashboardRequest

try {
    $result = $apiInstance->orderDashboard($order_dashboard_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->orderDashboard: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **order_dashboard_request** | [**\ShadowSoftware\DabDash\Model\OrderDashboardRequest**](../Model/OrderDashboardRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\OrderDashboard200Response**](../Model/OrderDashboard200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `productInspect()`

```php
productInspect($product_inspect_request): \ShadowSoftware\DabDash\Model\ProductInspect200Response
```

Inspect a specific product including every variation's price, compare_at_price, mix_match_tags, stock, and the tenant's mix & match rule settings. Use this to audit pricing, sale state, and bundle configuration for support tickets.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_inspect_request = new \ShadowSoftware\DabDash\Model\ProductInspectRequest(); // \ShadowSoftware\DabDash\Model\ProductInspectRequest

try {
    $result = $apiInstance->productInspect($product_inspect_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->productInspect: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_inspect_request** | [**\ShadowSoftware\DabDash\Model\ProductInspectRequest**](../Model/ProductInspectRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductInspect200Response**](../Model/ProductInspect200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `promotionAudit()`

```php
promotionAudit($promotion_audit_request): \ShadowSoftware\DabDash\Model\PromotionAudit200Response
```

Inspect coupons, freebies, mix and match rules, loyalty settings, and storewide sale state for overcharge or missed-discount support tickets.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$promotion_audit_request = new \ShadowSoftware\DabDash\Model\PromotionAuditRequest(); // \ShadowSoftware\DabDash\Model\PromotionAuditRequest

try {
    $result = $apiInstance->promotionAudit($promotion_audit_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->promotionAudit: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **promotion_audit_request** | [**\ShadowSoftware\DabDash\Model\PromotionAuditRequest**](../Model/PromotionAuditRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PromotionAudit200Response**](../Model/PromotionAudit200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
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
    echo 'Exception when calling ReadApi->pushNotificationDiagnostics: ', $e->getMessage(), PHP_EOL;
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

## `searchConsole()`

```php
searchConsole($search_console_request): \ShadowSoftware\DabDash\Model\SearchConsole200Response
```

Query Google Search Console data for the platform (dabdash.com) or a specific tenant with a connected GSC integration. Returns search overview, top queries, top pages, and daily trend.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$search_console_request = new \ShadowSoftware\DabDash\Model\SearchConsoleRequest(); // \ShadowSoftware\DabDash\Model\SearchConsoleRequest

try {
    $result = $apiInstance->searchConsole($search_console_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->searchConsole: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **search_console_request** | [**\ShadowSoftware\DabDash\Model\SearchConsoleRequest**](../Model/SearchConsoleRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\SearchConsole200Response**](../Model/SearchConsole200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
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
    echo 'Exception when calling ReadApi->storeInfo: ', $e->getMessage(), PHP_EOL;
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


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
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
    echo 'Exception when calling ReadApi->zoneDiagnostics: ', $e->getMessage(), PHP_EOL;
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
