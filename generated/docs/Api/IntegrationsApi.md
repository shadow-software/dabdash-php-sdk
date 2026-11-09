# ShadowSoftware\DabDash\IntegrationsApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**customerBlockManage()**](IntegrationsApi.md#customerBlockManage) | **POST** /api/v1/tools/customer_block_manage | Block or unblock a customer&#39;s current email and/or phone using existing tenant Security lists. Preview by default. Apply only on explicit user instruction with dry_run&#x3D;false, confirm&#x3D;true, a reason and expected_snapshot from status/preview. Unblocking removes the selected shared identifier blocks, including pre-existing ones; affects anyone using them. Other restrictions remain. Does not delete customers, change orders or create an account-ID ban. |
| [**customerBlockStatus()**](IntegrationsApi.md#customerBlockStatus) | **POST** /api/v1/tools/customer_block_status | Read a customer&#39;s normalized email/phone block status within one tenant. Returns the snapshot required to apply a block or unblock. These are shared identifier blocks, not an account-ID ban or a guarantee checkout will otherwise succeed. |
| [**metrcDiagnostics()**](IntegrationsApi.md#metrcDiagnostics) | **POST** /api/v1/tools/metrc_diagnostics | Returns a JSON summary of Metrc compliance status for a tenant: integration mode, sync states, audit log counts by HTTP status, and pending/failed report counts. Pass a tenant_slug to inspect a specific tenant. |
| [**pushNotificationDiagnostics()**](IntegrationsApi.md#pushNotificationDiagnostics) | **POST** /api/v1/tools/push_notification_diagnostics | Diagnose push notification (FCM) delivery for a vendor. Surfaces token health, notification settings, recent send history with push/email flags, and a plain-language diagnosis of why pushes are or are not being delivered. |
| [**storefrontThemeManage()**](IntegrationsApi.md#storefrontThemeManage) | **POST** /api/v1/tools/storefront_theme_manage | Show or update Modern storefront theme data: hero copy, the optional hero featured-product card, deals ticker, homepage sections, footer, and About cards.  The hero card is optional. hero_spotlight_mode is auto (newest sale product), product (pin one), or off (hide the card). Pass hero_spotlight_product (name or slug) or hero_spotlight_product_id to pin a product; omitting the mode then sets it to product. product_feature_manage only toggles the separate Featured Products grid. Vendors edit the same card in Settings → Theme → Hero features.  ACTIONS:   show (default): current saved fields. Unset custom text is null and uses the     storefront&#39;s shipped fallback; toggle values include their effective defaults.   set: pass only fields to change. Omitted fields are preserved. Empty text clears     the custom value. about_cards replaces the full card list. hero_ticker_items     replaces the custom ticker.   dry_run defaults true; pass dry_run&#x3D;false to save.  The WhatsApp number is left untouched unless whatsapp_number is explicitly passed. |
| [**tenantBrandingManage()**](IntegrationsApi.md#tenantBrandingManage) | **POST** /api/v1/tools/tenant_branding_manage | Show or update a tenant&#39;s store logo, homepage hero image, and BIMI email logo, then upsert the Cloudflare &#x60;default._bimi&#x60; TXT record when the zone is connected.  ACTIONS:   show (default): current logo_path, storefront logo URL (custom domain when          set), hero_image_path/URL, BIMI path/URL, the TXT value to publish,          and whether Cloudflare is connected.   set: pass media_id (from media_list / media_upload) to point logo_path at a          library asset. Pass hero_media_id for settings.hero_image_path, or          clear_hero&#x3D;true to remove the hero. Pass exactly one of source_base64 /          source_path / source_url for an SVG to store the BIMI logo at          tenants/{id}/bimi-logo.svg (media_upload cannot take SVG). dry_run          defaults true — the first call reports what would happen.  Logo URLs and BIMI &#x60;l&#x3D;&#x60; values always use the tenant storefront host (custom domain or {slug}.dabdash.com), never the platform APP_URL. |
| [**tenantDomainManage()**](IntegrationsApi.md#tenantDomainManage) | **POST** /api/v1/tools/tenant_domain_manage | Show, connect, verify, or remove a store&#39;s custom domain (e.g. maritimesmoke.com).  ACTIONS:   show (default): current domain, verified flag, and the DNS TXT record to add.   set: connect a domain (bare hostname, no http://). Resets verification. dry_run        defaults true; pass dry_run&#x3D;false to save.   verify: checks the TXT record or the storefront domain-check URL and marks the        domain verified. Run after DNS has been changed.   remove: disconnect the custom domain. dry_run defaults true.  DNS records are not created here; show returns what to add at the registrar or in Cloudflare. Until verified the store keeps working on {slug}.dabdash.com. |
| [**tenantKlaviyoManage()**](IntegrationsApi.md#tenantKlaviyoManage) | **POST** /api/v1/tools/tenant_klaviyo_manage | Show or manage a tenant&#39;s BYO Klaviyo integration (profiles, list sync, Smart List projection, order events). Prefer guiding vendors to Settings → Integrations.  ACTIONS:   show (default): connection status + settings (never the API key).   connect / disconnect: dry_run default true; apply needs dry_run&#x3D;false+confirm.   update_settings: default_list_id, mapped_contact_list_id, smart_list_ids,     smart_list_sync_enabled.   sync_now / refresh_smart_lists / use_for_orders / available_lists. |
| [**tenantMailgunManage()**](IntegrationsApi.md#tenantMailgunManage) | **POST** /api/v1/tools/tenant_mailgun_manage | Show or manage a tenant&#39;s Mailgun Send Email setup (campaigns + hybrid SMTP).  Prefer guiding vendors to Settings → Domain &amp; Email → Send Email (/admin/settings/domain#send-email). That is the canonical setup home — not Integrations alone.  ACTIONS:   show (default): connection status, Ready sending names, SMTP bootstrap     flag, and the #send-email setup URL. Never returns the API key.   connect: validate + store a Mailgun private key. dry_run defaults true;     applying requires dry_run&#x3D;false and confirm&#x3D;true. Dry-run still     validates the key with Mailgun (read-only).   disconnect: remove the Mailgun integration row (SMTP left alone).     Applying requires dry_run&#x3D;false and confirm&#x3D;true.   sync: import domains from the Mailgun account into local rows.   add_domain: create/reuse one sending name under the store/CF apex.   verify_domain: ask Mailgun to re-check DNS for one sending name.   autoconfigure: create mail1..mail5 under the Cloudflare zone and push DNS.     Applying requires dry_run&#x3D;false and confirm&#x3D;true.   bootstrap_smtp: fill tenants.settings.smtp from a Ready Mailgun domain.     Applying requires dry_run&#x3D;false; force_smtp&#x3D;true also needs confirm&#x3D;true.  Mutating actions default dry_run&#x3D;true; pass dry_run&#x3D;false to apply. |
| [**tenantSmtpManage()**](IntegrationsApi.md#tenantSmtpManage) | **POST** /api/v1/tools/tenant_smtp_manage | Show or update Advanced store SMTP (manual host/user/password).  Prefer Mailgun Send Email for campaigns and easiest setup: use tenant_mailgun_manage, and send vendors to /admin/settings/domain#send-email. This tool is for Advanced / BYO SMTP only.  ACTIONS:   show (default): returns the current SMTP settings with the password omitted.   set: updates only the supplied fields. dry_run defaults true; pass dry_run&#x3D;false        to save. smtp_password is write-only and is never returned.  Keep from_address equal to the authenticated SMTP username unless the provider has explicitly authorized a different sender or alias. A successful settings update clears the tenant&#39;s stale SMTP health banner; the next send records any still-current failure. |
| [**tenantTwilioManage()**](IntegrationsApi.md#tenantTwilioManage) | **POST** /api/v1/tools/tenant_twilio_manage | Show or manage a tenant&#39;s Twilio SMS connection (required for text campaigns). Prefer Settings → Integrations.  ACTIONS: show | connect | disconnect | test. connect needs account_sid + auth_token + from_number or messaging_service_sid. Mutating actions: dry_run default true; apply needs dry_run&#x3D;false + confirm&#x3D;true. |
| [**webhookManage()**](IntegrationsApi.md#webhookManage) | **POST** /api/v1/tools/webhook_manage | List, create, update, or delete saved tenant webhooks (order events + campaign webhook mode).  ACTIONS: list | show | upsert | delete. upsert needs label + url (https). Optional health_url, events (order.created|order.shipped), is_active, custom_headers [{name,value}]. Signing secrets are generated server-side and returned once on create. Klaviyo-managed webhooks cannot be edited here — use tenant_klaviyo_manage. Assign to a campaign with campaign_upsert.saved_webhook_id. |


## `customerBlockManage()`

```php
customerBlockManage($customer_block_manage_request): \ShadowSoftware\DabDash\Model\CustomerBlockManage200Response
```

Block or unblock a customer's current email and/or phone using existing tenant Security lists. Preview by default. Apply only on explicit user instruction with dry_run=false, confirm=true, a reason and expected_snapshot from status/preview. Unblocking removes the selected shared identifier blocks, including pre-existing ones; affects anyone using them. Other restrictions remain. Does not delete customers, change orders or create an account-ID ban.

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
$customer_block_manage_request = new \ShadowSoftware\DabDash\Model\CustomerBlockManageRequest(); // \ShadowSoftware\DabDash\Model\CustomerBlockManageRequest

try {
    $result = $apiInstance->customerBlockManage($customer_block_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->customerBlockManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_block_manage_request** | [**\ShadowSoftware\DabDash\Model\CustomerBlockManageRequest**](../Model/CustomerBlockManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerBlockManage200Response**](../Model/CustomerBlockManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `customerBlockStatus()`

```php
customerBlockStatus($customer_block_status_request): \ShadowSoftware\DabDash\Model\CustomerBlockStatus200Response
```

Read a customer's normalized email/phone block status within one tenant. Returns the snapshot required to apply a block or unblock. These are shared identifier blocks, not an account-ID ban or a guarantee checkout will otherwise succeed.

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
$customer_block_status_request = new \ShadowSoftware\DabDash\Model\CustomerBlockStatusRequest(); // \ShadowSoftware\DabDash\Model\CustomerBlockStatusRequest

try {
    $result = $apiInstance->customerBlockStatus($customer_block_status_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->customerBlockStatus: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **customer_block_status_request** | [**\ShadowSoftware\DabDash\Model\CustomerBlockStatusRequest**](../Model/CustomerBlockStatusRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CustomerBlockStatus200Response**](../Model/CustomerBlockStatus200Response.md)

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

## `storefrontThemeManage()`

```php
storefrontThemeManage($storefront_theme_manage_request): \ShadowSoftware\DabDash\Model\StorefrontThemeManage200Response
```

Show or update Modern storefront theme data: hero copy, the optional hero featured-product card, deals ticker, homepage sections, footer, and About cards.  The hero card is optional. hero_spotlight_mode is auto (newest sale product), product (pin one), or off (hide the card). Pass hero_spotlight_product (name or slug) or hero_spotlight_product_id to pin a product; omitting the mode then sets it to product. product_feature_manage only toggles the separate Featured Products grid. Vendors edit the same card in Settings → Theme → Hero features.  ACTIONS:   show (default): current saved fields. Unset custom text is null and uses the     storefront's shipped fallback; toggle values include their effective defaults.   set: pass only fields to change. Omitted fields are preserved. Empty text clears     the custom value. about_cards replaces the full card list. hero_ticker_items     replaces the custom ticker.   dry_run defaults true; pass dry_run=false to save.  The WhatsApp number is left untouched unless whatsapp_number is explicitly passed.

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
$storefront_theme_manage_request = new \ShadowSoftware\DabDash\Model\StorefrontThemeManageRequest(); // \ShadowSoftware\DabDash\Model\StorefrontThemeManageRequest

try {
    $result = $apiInstance->storefrontThemeManage($storefront_theme_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->storefrontThemeManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **storefront_theme_manage_request** | [**\ShadowSoftware\DabDash\Model\StorefrontThemeManageRequest**](../Model/StorefrontThemeManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\StorefrontThemeManage200Response**](../Model/StorefrontThemeManage200Response.md)

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

## `tenantDomainManage()`

```php
tenantDomainManage($tenant_domain_manage_request): \ShadowSoftware\DabDash\Model\TenantDomainManage200Response
```

Show, connect, verify, or remove a store's custom domain (e.g. maritimesmoke.com).  ACTIONS:   show (default): current domain, verified flag, and the DNS TXT record to add.   set: connect a domain (bare hostname, no http://). Resets verification. dry_run        defaults true; pass dry_run=false to save.   verify: checks the TXT record or the storefront domain-check URL and marks the        domain verified. Run after DNS has been changed.   remove: disconnect the custom domain. dry_run defaults true.  DNS records are not created here; show returns what to add at the registrar or in Cloudflare. Until verified the store keeps working on {slug}.dabdash.com.

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
$tenant_domain_manage_request = new \ShadowSoftware\DabDash\Model\TenantDomainManageRequest(); // \ShadowSoftware\DabDash\Model\TenantDomainManageRequest

try {
    $result = $apiInstance->tenantDomainManage($tenant_domain_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->tenantDomainManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_domain_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantDomainManageRequest**](../Model/TenantDomainManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantDomainManage200Response**](../Model/TenantDomainManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantKlaviyoManage()`

```php
tenantKlaviyoManage($tenant_klaviyo_manage_request): \ShadowSoftware\DabDash\Model\TenantKlaviyoManage200Response
```

Show or manage a tenant's BYO Klaviyo integration (profiles, list sync, Smart List projection, order events). Prefer guiding vendors to Settings → Integrations.  ACTIONS:   show (default): connection status + settings (never the API key).   connect / disconnect: dry_run default true; apply needs dry_run=false+confirm.   update_settings: default_list_id, mapped_contact_list_id, smart_list_ids,     smart_list_sync_enabled.   sync_now / refresh_smart_lists / use_for_orders / available_lists.

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
$tenant_klaviyo_manage_request = new \ShadowSoftware\DabDash\Model\TenantKlaviyoManageRequest(); // \ShadowSoftware\DabDash\Model\TenantKlaviyoManageRequest

try {
    $result = $apiInstance->tenantKlaviyoManage($tenant_klaviyo_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->tenantKlaviyoManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_klaviyo_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantKlaviyoManageRequest**](../Model/TenantKlaviyoManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantKlaviyoManage200Response**](../Model/TenantKlaviyoManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantMailgunManage()`

```php
tenantMailgunManage($tenant_mailgun_manage_request): \ShadowSoftware\DabDash\Model\TenantMailgunManage200Response
```

Show or manage a tenant's Mailgun Send Email setup (campaigns + hybrid SMTP).  Prefer guiding vendors to Settings → Domain & Email → Send Email (/admin/settings/domain#send-email). That is the canonical setup home — not Integrations alone.  ACTIONS:   show (default): connection status, Ready sending names, SMTP bootstrap     flag, and the #send-email setup URL. Never returns the API key.   connect: validate + store a Mailgun private key. dry_run defaults true;     applying requires dry_run=false and confirm=true. Dry-run still     validates the key with Mailgun (read-only).   disconnect: remove the Mailgun integration row (SMTP left alone).     Applying requires dry_run=false and confirm=true.   sync: import domains from the Mailgun account into local rows.   add_domain: create/reuse one sending name under the store/CF apex.   verify_domain: ask Mailgun to re-check DNS for one sending name.   autoconfigure: create mail1..mail5 under the Cloudflare zone and push DNS.     Applying requires dry_run=false and confirm=true.   bootstrap_smtp: fill tenants.settings.smtp from a Ready Mailgun domain.     Applying requires dry_run=false; force_smtp=true also needs confirm=true.  Mutating actions default dry_run=true; pass dry_run=false to apply.

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
$tenant_mailgun_manage_request = new \ShadowSoftware\DabDash\Model\TenantMailgunManageRequest(); // \ShadowSoftware\DabDash\Model\TenantMailgunManageRequest

try {
    $result = $apiInstance->tenantMailgunManage($tenant_mailgun_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->tenantMailgunManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_mailgun_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantMailgunManageRequest**](../Model/TenantMailgunManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantMailgunManage200Response**](../Model/TenantMailgunManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantSmtpManage()`

```php
tenantSmtpManage($tenant_smtp_manage_request): \ShadowSoftware\DabDash\Model\TenantSmtpManage200Response
```

Show or update Advanced store SMTP (manual host/user/password).  Prefer Mailgun Send Email for campaigns and easiest setup: use tenant_mailgun_manage, and send vendors to /admin/settings/domain#send-email. This tool is for Advanced / BYO SMTP only.  ACTIONS:   show (default): returns the current SMTP settings with the password omitted.   set: updates only the supplied fields. dry_run defaults true; pass dry_run=false        to save. smtp_password is write-only and is never returned.  Keep from_address equal to the authenticated SMTP username unless the provider has explicitly authorized a different sender or alias. A successful settings update clears the tenant's stale SMTP health banner; the next send records any still-current failure.

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
$tenant_smtp_manage_request = new \ShadowSoftware\DabDash\Model\TenantSmtpManageRequest(); // \ShadowSoftware\DabDash\Model\TenantSmtpManageRequest

try {
    $result = $apiInstance->tenantSmtpManage($tenant_smtp_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->tenantSmtpManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_smtp_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantSmtpManageRequest**](../Model/TenantSmtpManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantSmtpManage200Response**](../Model/TenantSmtpManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantTwilioManage()`

```php
tenantTwilioManage($tenant_twilio_manage_request): \ShadowSoftware\DabDash\Model\TenantTwilioManage200Response
```

Show or manage a tenant's Twilio SMS connection (required for text campaigns). Prefer Settings → Integrations.  ACTIONS: show | connect | disconnect | test. connect needs account_sid + auth_token + from_number or messaging_service_sid. Mutating actions: dry_run default true; apply needs dry_run=false + confirm=true.

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
$tenant_twilio_manage_request = new \ShadowSoftware\DabDash\Model\TenantTwilioManageRequest(); // \ShadowSoftware\DabDash\Model\TenantTwilioManageRequest

try {
    $result = $apiInstance->tenantTwilioManage($tenant_twilio_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->tenantTwilioManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_twilio_manage_request** | [**\ShadowSoftware\DabDash\Model\TenantTwilioManageRequest**](../Model/TenantTwilioManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\TenantTwilioManage200Response**](../Model/TenantTwilioManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `webhookManage()`

```php
webhookManage($webhook_manage_request): \ShadowSoftware\DabDash\Model\WebhookManage200Response
```

List, create, update, or delete saved tenant webhooks (order events + campaign webhook mode).  ACTIONS: list | show | upsert | delete. upsert needs label + url (https). Optional health_url, events (order.created|order.shipped), is_active, custom_headers [{name,value}]. Signing secrets are generated server-side and returned once on create. Klaviyo-managed webhooks cannot be edited here — use tenant_klaviyo_manage. Assign to a campaign with campaign_upsert.saved_webhook_id.

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
$webhook_manage_request = new \ShadowSoftware\DabDash\Model\WebhookManageRequest(); // \ShadowSoftware\DabDash\Model\WebhookManageRequest

try {
    $result = $apiInstance->webhookManage($webhook_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegrationsApi->webhookManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **webhook_manage_request** | [**\ShadowSoftware\DabDash\Model\WebhookManageRequest**](../Model/WebhookManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\WebhookManage200Response**](../Model/WebhookManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
