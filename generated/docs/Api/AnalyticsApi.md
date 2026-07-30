# ShadowSoftware\Sdk\Generated\AnalyticsApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**analyticsQuery()**](AnalyticsApi.md#analyticsQuery) | **POST** /api/v1/tools/analytics_query | Run read-only analytics queries against the production database. Available reports: revenue_by_tenant, orders_by_status, top_products, revenue_over_time, customer_stats. Revenue dating uses RevenueAttribution (pass tenant_slug for delivered-mode tenants). |
| [**googleAnalytics()**](AnalyticsApi.md#googleAnalytics) | **POST** /api/v1/tools/google_analytics | Query Google Analytics (GA4) data for the platform (dabdash.com) or a specific tenant with a connected GA integration. Returns traffic overview, top pages, traffic sources, top events, and daily trend. |
| [**searchConsole()**](AnalyticsApi.md#searchConsole) | **POST** /api/v1/tools/search_console | Query Google Search Console data for the platform (dabdash.com) or a specific tenant with a connected GSC integration. Returns search overview, top queries, top pages, and daily trend. |


## `analyticsQuery()`

```php
analyticsQuery($analytics_query_request): \ShadowSoftware\Sdk\Generated\Model\AnalyticsQuery200Response
```

Run read-only analytics queries against the production database. Available reports: revenue_by_tenant, orders_by_status, top_products, revenue_over_time, customer_stats. Revenue dating uses RevenueAttribution (pass tenant_slug for delivered-mode tenants).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\AnalyticsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$analytics_query_request = new \ShadowSoftware\Sdk\Generated\Model\AnalyticsQueryRequest(); // \ShadowSoftware\Sdk\Generated\Model\AnalyticsQueryRequest

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
| **analytics_query_request** | [**\ShadowSoftware\Sdk\Generated\Model\AnalyticsQueryRequest**](../Model/AnalyticsQueryRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\AnalyticsQuery200Response**](../Model/AnalyticsQuery200Response.md)

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
googleAnalytics($google_analytics_request): \ShadowSoftware\Sdk\Generated\Model\GoogleAnalytics200Response
```

Query Google Analytics (GA4) data for the platform (dabdash.com) or a specific tenant with a connected GA integration. Returns traffic overview, top pages, traffic sources, top events, and daily trend.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\AnalyticsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$google_analytics_request = new \ShadowSoftware\Sdk\Generated\Model\GoogleAnalyticsRequest(); // \ShadowSoftware\Sdk\Generated\Model\GoogleAnalyticsRequest

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
| **google_analytics_request** | [**\ShadowSoftware\Sdk\Generated\Model\GoogleAnalyticsRequest**](../Model/GoogleAnalyticsRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\GoogleAnalytics200Response**](../Model/GoogleAnalytics200Response.md)

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
searchConsole($search_console_request): \ShadowSoftware\Sdk\Generated\Model\SearchConsole200Response
```

Query Google Search Console data for the platform (dabdash.com) or a specific tenant with a connected GSC integration. Returns search overview, top queries, top pages, and daily trend.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\AnalyticsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$search_console_request = new \ShadowSoftware\Sdk\Generated\Model\SearchConsoleRequest(); // \ShadowSoftware\Sdk\Generated\Model\SearchConsoleRequest

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
| **search_console_request** | [**\ShadowSoftware\Sdk\Generated\Model\SearchConsoleRequest**](../Model/SearchConsoleRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\SearchConsole200Response**](../Model/SearchConsole200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
