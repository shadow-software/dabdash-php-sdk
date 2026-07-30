# ShadowSoftware\DabDash\OrdersApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**orderDashboard()**](OrdersApi.md#orderDashboard) | **POST** /api/v1/tools/order_dashboard | Query orders across all tenants. Filter by status, order number, customer clues, date range, amount, or tenant. Returns order list with pricing context. |


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


$apiInstance = new ShadowSoftware\DabDash\Api\OrdersApi(
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
    echo 'Exception when calling OrdersApi->orderDashboard: ', $e->getMessage(), PHP_EOL;
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
