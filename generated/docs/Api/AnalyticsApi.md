# ShadowSoftware\DabDash\AnalyticsApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**analyticsQuery()**](AnalyticsApi.md#analyticsQuery) | **POST** /api/v1/tools/analytics_query | Run read-only analytics queries against the production database. Available reports: revenue_by_tenant, orders_by_status, top_products, revenue_over_time, customer_stats. Revenue dating uses RevenueAttribution (pass tenant_slug for delivered-mode tenants). |
| [**googleAnalytics()**](AnalyticsApi.md#googleAnalytics) | **POST** /api/v1/tools/google_analytics | Query Google Analytics (GA4) data for the platform (dabdash.com) or a specific tenant with a connected GA integration. Returns traffic overview, top pages, traffic sources, top events, and daily trend. |
| [**productProfitability()**](AnalyticsApi.md#productProfitability) | **POST** /api/v1/tools/product_profitability | Rank a tenant&#39;s products by real net margin using order-line COGS (order_items.cost_price), not price-tier approximations. Use this before recommending sales, coupons, freebies, or subscription mounting ladders — only promote SKUs with enough margin headroom.  Revenue dating follows RevenueAttribution (placed vs delivered) for the tenant. Freebie gift lines are excluded from COGS so giveaways do not distort product margins. Results include current catalog stock_status and a promo_headroom_ok flag (margin_percent &gt;&#x3D; min_margin_percent).  Sort: margin (default), revenue, or units. Pass a wide date_from for tenants with older imported history. |
| [**searchConsole()**](AnalyticsApi.md#searchConsole) | **POST** /api/v1/tools/search_console | Query Google Search Console data for the platform (dabdash.com) or a specific tenant with a connected GSC integration. Returns search overview, top queries, top pages, and daily trend. |


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


$apiInstance = new ShadowSoftware\DabDash\Api\AnalyticsApi(
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
    echo 'Exception when calling AnalyticsApi->analyticsQuery: ', $e->getMessage(), PHP_EOL;
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


$apiInstance = new ShadowSoftware\DabDash\Api\AnalyticsApi(
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
    echo 'Exception when calling AnalyticsApi->googleAnalytics: ', $e->getMessage(), PHP_EOL;
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

## `productProfitability()`

```php
productProfitability($product_profitability_request): \ShadowSoftware\DabDash\Model\ProductProfitability200Response
```

Rank a tenant's products by real net margin using order-line COGS (order_items.cost_price), not price-tier approximations. Use this before recommending sales, coupons, freebies, or subscription mounting ladders — only promote SKUs with enough margin headroom.  Revenue dating follows RevenueAttribution (placed vs delivered) for the tenant. Freebie gift lines are excluded from COGS so giveaways do not distort product margins. Results include current catalog stock_status and a promo_headroom_ok flag (margin_percent >= min_margin_percent).  Sort: margin (default), revenue, or units. Pass a wide date_from for tenants with older imported history.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\AnalyticsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_profitability_request = new \ShadowSoftware\DabDash\Model\ProductProfitabilityRequest(); // \ShadowSoftware\DabDash\Model\ProductProfitabilityRequest

try {
    $result = $apiInstance->productProfitability($product_profitability_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AnalyticsApi->productProfitability: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_profitability_request** | [**\ShadowSoftware\DabDash\Model\ProductProfitabilityRequest**](../Model/ProductProfitabilityRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductProfitability200Response**](../Model/ProductProfitability200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\AnalyticsApi(
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
    echo 'Exception when calling AnalyticsApi->searchConsole: ', $e->getMessage(), PHP_EOL;
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
