# ShadowSoftware\DabDash\CustomersApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**contactCleanup()**](CustomersApi.md#contactCleanup) | **POST** /api/v1/tools/contact_cleanup | Removes contacts that failed email validation from an imported list.  Typical use: a CSV import brought in thousands of addresses, the validator graded them, and the list is now full of dead addresses that will bounce and hurt sending reputation.  SAFETY: - dry_run defaults to TRUE. Nothing is deleted until you pass dry_run&#x3D;false. The dry run   returns exactly how many contacts match and a sample of them. - Only statuses \&quot;invalid\&quot; and \&quot;risky\&quot; can be purged. \&quot;unknown\&quot; means the validator has not   graded that contact YET — those are never deleted, and asking for them is refused. - Scoped to one contact list when list_id is given, otherwise the whole store. - Contacts are deleted, not unsubscribed. This cannot be undone from here. |
| [**customerAddresses()**](CustomersApi.md#customerAddresses) | **POST** /api/v1/tools/customer_addresses | Return a customer&#39;s saved addresses, coordinates, saved zones, and zone mismatch diagnostics. |
| [**customerList()**](CustomersApi.md#customerList) | **POST** /api/v1/tools/customer_list | Page through all customers for a tenant, optionally filtered to those updated since a given time. Built for bulk sync — use customer_lookup instead for a single targeted search. |
| [**customerLookup()**](CustomersApi.md#customerLookup) | **POST** /api/v1/tools/customer_lookup | Find customers by id, email, phone, or name and return their recent addresses, orders, and support context. |
| [**customerUpdate()**](CustomersApi.md#customerUpdate) | **POST** /api/v1/tools/customer_update | Update a customer&#39;s contact fields (name, email, phone) and/or suppress marketing consent (email_opt_out, sms_marketing_opt_out, sms_notifications_muted — one-way, cannot un-suppress). Verification, loyalty, and other DabDash-owned fields cannot be set here. |
| [**mailboxInspect()**](CustomersApi.md#mailboxInspect) | **POST** /api/v1/tools/mailbox_inspect | Inspect a tenant inbound mailbox: sync watermark, last error, recent ingestion counts (inbound/outbound), and a healthy/bootstrap/stalled/quiet verdict. Pass a tenant_slug, or pass platform&#x3D;true for a platform-owned mailbox (tenant_id IS NULL). |


## `contactCleanup()`

```php
contactCleanup($contact_cleanup_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Removes contacts that failed email validation from an imported list.  Typical use: a CSV import brought in thousands of addresses, the validator graded them, and the list is now full of dead addresses that will bounce and hurt sending reputation.  SAFETY: - dry_run defaults to TRUE. Nothing is deleted until you pass dry_run=false. The dry run   returns exactly how many contacts match and a sample of them. - Only statuses \"invalid\" and \"risky\" can be purged. \"unknown\" means the validator has not   graded that contact YET — those are never deleted, and asking for them is refused. - Scoped to one contact list when list_id is given, otherwise the whole store. - Contacts are deleted, not unsubscribed. This cannot be undone from here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CustomersApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$contact_cleanup_request = new \ShadowSoftware\DabDash\Model\ContactCleanupRequest(); // \ShadowSoftware\DabDash\Model\ContactCleanupRequest

try {
    $result = $apiInstance->contactCleanup($contact_cleanup_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CustomersApi->contactCleanup: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **contact_cleanup_request** | [**\ShadowSoftware\DabDash\Model\ContactCleanupRequest**](../Model/ContactCleanupRequest.md)|  | [optional] |

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


$apiInstance = new ShadowSoftware\DabDash\Api\CustomersApi(
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
    echo 'Exception when calling CustomersApi->customerAddresses: ', $e->getMessage(), PHP_EOL;
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


$apiInstance = new ShadowSoftware\DabDash\Api\CustomersApi(
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
    echo 'Exception when calling CustomersApi->customerList: ', $e->getMessage(), PHP_EOL;
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


$apiInstance = new ShadowSoftware\DabDash\Api\CustomersApi(
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
    echo 'Exception when calling CustomersApi->customerLookup: ', $e->getMessage(), PHP_EOL;
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

## `customerUpdate()`

```php
customerUpdate($customer_update_request): \ShadowSoftware\DabDash\Model\CustomerUpdate200Response
```

Update a customer's contact fields (name, email, phone) and/or suppress marketing consent (email_opt_out, sms_marketing_opt_out, sms_notifications_muted — one-way, cannot un-suppress). Verification, loyalty, and other DabDash-owned fields cannot be set here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CustomersApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$customer_update_request = new \ShadowSoftware\DabDash\Model\CustomerUpdateRequest(); // \ShadowSoftware\DabDash\Model\CustomerUpdateRequest

try {
    $result = $apiInstance->customerUpdate($customer_update_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CustomersApi->customerUpdate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_update_request** | [**\ShadowSoftware\DabDash\Model\CustomerUpdateRequest**](../Model/CustomerUpdateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerUpdate200Response**](../Model/CustomerUpdate200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\CustomersApi(
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
    echo 'Exception when calling CustomersApi->mailboxInspect: ', $e->getMessage(), PHP_EOL;
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
