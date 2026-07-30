# ShadowSoftware\Sdk\Generated\MarketingApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**campaignApplyTemplate()**](MarketingApi.md#campaignApplyTemplate) | **POST** /api/v1/tools/campaign_apply_template | Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \&quot;Choose template\&quot; action in the vendor admin.  The template is rendered with the tenant&#39;s own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign&#39;s html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \&quot;Your headline here\&quot;, etc.) — after applying, use campaign_upsert to set the real html_body with this tenant&#39;s products, prices, and offers, or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_upsert (replace html_body with real data) → campaign_send_test (preview). |
| [**campaignControl()**](MarketingApi.md#campaignControl) | **POST** /api/v1/tools/campaign_control | Pause or resume a vendor email/SMS campaign.  pause: sets status&#x3D;paused with paused_reason&#x3D;manual. Works from sending or recovering. Pending recipients stay pending; the dispatcher skips this campaign so sibling sending campaigns can use the shared throttle.  resume: from paused → sending (clears pause fields). From recovering → sending and requeues transport-failed recipients (same as auto-resume after a healthy webhook health check).  dry_run defaults TRUE. Always confirm tenant_slug and campaign_id first. |
| [**campaignRecipientsRequeue()**](MarketingApi.md#campaignRecipientsRequeue) | **POST** /api/v1/tools/campaign_recipients_requeue | Unstick a vendor email/SMS campaign that auto-paused or stalled mid-send.  Resets failed recipients (and optionally stale \&quot;sending\&quot; rows) back to pending, clears webhook_consecutive_failures / paused_reason / last_error, and sets the campaign status to sending so the dispatcher continues. Already-sent recipients are never touched.  Pass retry_sending_log_errors&#x3D;true to retry every recipient that appears in the campaign sending log with a retryable error (502/timeout/SMTP soft fail, etc.). Those jobs dispatch immediately (up to dispatch_limit) so they are not stuck behind a large pending queue. The log rows stay — they are diagnostic history.  Use when a webhook campaign froze after consecutive endpoint errors (or soft SMTP failures that were misclassified before the soft-fail fix) and pending + failed recipients remain.  dry_run defaults to TRUE — first call reports counts only. Pass dry_run&#x3D;false to apply. Always confirm tenant_slug with tenant_list and campaign_id with the vendor. |
| [**campaignSendTest()**](MarketingApi.md#campaignSendTest) | **POST** /api/v1/tools/campaign_send_test | Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email campaigns go out through the tenant&#39;s own SMTP settings — exactly the path a real send uses. Pass to_email. Requires complete SMTP settings.  Text (SMS) campaigns go out through the tenant&#39;s connected Twilio account. Pass to_phone instead of to_email. Requires Twilio to be connected.  This is a preview only: it does NOT start the campaign, does NOT touch the audience, and does NOT record any send/open/click stats. Personalization tokens render with sample values; unsubscribe links point at harmless test URLs.  Works on a campaign in any status (a draft preview is the common case). Always confirm the campaign_id with the vendor first. |
| [**campaignSetImage()**](MarketingApi.md#campaignSetImage) | **POST** /api/v1/tools/campaign_set_image | Drop an image from the tenant&#39;s media library into a DRAFT campaign — no link copying, no manual HTML. Give it a campaign_id and a media_id (from the tenant&#39;s own library) and it fills an image placeholder in the campaign body with that image, using the library image&#39;s own public URL and alt text. The URL is on the tenant&#39;s public disk, so it passes the campaign HTML sanitizer untouched and renders in email clients.  WHICH SLOT IT FILLS:   System templates render image placeholders labelled \&quot;Add image\&quot;. By default this tool fills the   FIRST remaining placeholder. Pass slot_index (1-based) to target a specific placeholder when a   template has more than one. If the body has no placeholder left, the image is appended at the end.  REQUIREMENTS:   - The campaign must be a DRAFT and belong to the tenant.   - The media asset must belong to the tenant and be public (private assets have no shareable URL).  Typical flow: campaign_upsert (create) → campaign_apply_template (design with placeholders) → campaign_set_image (fill each placeholder) → campaign_upsert (real copy) → campaign_send_test. |
| [**campaignUpsert()**](MarketingApi.md#campaignUpsert) | **POST** /api/v1/tools/campaign_upsert | Create or edit a vendor email campaign on behalf of a tenant. This is the vendor → their-customers campaign universe (NOT the platform drip-to-leads system). Completed campaigns cannot be edited.  CREATE MODE (no campaign_id):   Creates a new draft. For email campaigns, &#x60;name&#x60; and &#x60;subject&#x60; are required; &#x60;html_body&#x60; is optional   on create — omit it to start blank and apply a system template afterwards with campaign_apply_template.   For text (SMS) campaigns, set channel to \&quot;sms\&quot; and pass &#x60;sms_body&#x60; (name required). Twilio must be   connected on the tenant.  UPDATE MODE (campaign_id provided):   Edits an existing campaign. Only the fields you pass are changed; omitted fields are left as-is.   Drafts accept all fields. Sending or paused campaigns accept content fields only — for email that is   name, subject, html_body, plain_body; for text that is name and sms_body. Sending campaigns keep   running and unreached recipients get the latest version. Scheduled, cancelled, and failed campaigns   must be edited from the vendor admin.  HTML BODY (email only):   Pass the full email HTML in &#x60;html_body&#x60;. It is sanitized exactly like the vendor admin editor:   a full document (&lt;!DOCTYPE …&gt; / &lt;html&gt;) keeps its table-based structure; a fragment is run   through the stricter inline allowlist. Use the literal token {{unsubscribe_url}} where the   unsubscribe link should appear — it is replaced per-recipient at send time and a List-Unsubscribe   header is added automatically. Personalization tokens {{first_name}} and {{last_name}} are also   replaced at send time.  SMS BODY (text only):   Pass the message in &#x60;sms_body&#x60; (max 1600 chars). Tokens {{first_name}} and {{last_name}} are   replaced at send time. Text campaigns only target opted-in shop customers.  AUDIENCE / MODE (email only):   mode is \&quot;smtp\&quot; (default) or \&quot;webhook\&quot;. audience_includes_customers (default true) targets the   tenant&#39;s own customers. This tool does not send anything — use campaign_send_test to preview,   then the vendor sends from the admin.  Always confirm the tenant_slug with tenant_list first. To populate a draft from a built-in design, create it here, then call campaign_apply_template. |


## `campaignApplyTemplate()`

```php
campaignApplyTemplate($campaign_apply_template_request): \ShadowSoftware\Sdk\Generated\Model\CampaignApplyTemplate200Response
```

Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \"Choose template\" action in the vendor admin.  The template is rendered with the tenant's own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign's html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \"Your headline here\", etc.) — after applying, use campaign_upsert to set the real html_body with this tenant's products, prices, and offers, or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_upsert (replace html_body with real data) → campaign_send_test (preview).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_apply_template_request = new \ShadowSoftware\Sdk\Generated\Model\CampaignApplyTemplateRequest(); // \ShadowSoftware\Sdk\Generated\Model\CampaignApplyTemplateRequest

try {
    $result = $apiInstance->campaignApplyTemplate($campaign_apply_template_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignApplyTemplate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_apply_template_request** | [**\ShadowSoftware\Sdk\Generated\Model\CampaignApplyTemplateRequest**](../Model/CampaignApplyTemplateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\CampaignApplyTemplate200Response**](../Model/CampaignApplyTemplate200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignControl()`

```php
campaignControl($campaign_control_request): \ShadowSoftware\Sdk\Generated\Model\CampaignControl200Response
```

Pause or resume a vendor email/SMS campaign.  pause: sets status=paused with paused_reason=manual. Works from sending or recovering. Pending recipients stay pending; the dispatcher skips this campaign so sibling sending campaigns can use the shared throttle.  resume: from paused → sending (clears pause fields). From recovering → sending and requeues transport-failed recipients (same as auto-resume after a healthy webhook health check).  dry_run defaults TRUE. Always confirm tenant_slug and campaign_id first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_control_request = new \ShadowSoftware\Sdk\Generated\Model\CampaignControlRequest(); // \ShadowSoftware\Sdk\Generated\Model\CampaignControlRequest

try {
    $result = $apiInstance->campaignControl($campaign_control_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignControl: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_control_request** | [**\ShadowSoftware\Sdk\Generated\Model\CampaignControlRequest**](../Model/CampaignControlRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\CampaignControl200Response**](../Model/CampaignControl200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignRecipientsRequeue()`

```php
campaignRecipientsRequeue($campaign_recipients_requeue_request): \ShadowSoftware\Sdk\Generated\Model\CampaignRecipientsRequeue200Response
```

Unstick a vendor email/SMS campaign that auto-paused or stalled mid-send.  Resets failed recipients (and optionally stale \"sending\" rows) back to pending, clears webhook_consecutive_failures / paused_reason / last_error, and sets the campaign status to sending so the dispatcher continues. Already-sent recipients are never touched.  Pass retry_sending_log_errors=true to retry every recipient that appears in the campaign sending log with a retryable error (502/timeout/SMTP soft fail, etc.). Those jobs dispatch immediately (up to dispatch_limit) so they are not stuck behind a large pending queue. The log rows stay — they are diagnostic history.  Use when a webhook campaign froze after consecutive endpoint errors (or soft SMTP failures that were misclassified before the soft-fail fix) and pending + failed recipients remain.  dry_run defaults to TRUE — first call reports counts only. Pass dry_run=false to apply. Always confirm tenant_slug with tenant_list and campaign_id with the vendor.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_recipients_requeue_request = new \ShadowSoftware\Sdk\Generated\Model\CampaignRecipientsRequeueRequest(); // \ShadowSoftware\Sdk\Generated\Model\CampaignRecipientsRequeueRequest

try {
    $result = $apiInstance->campaignRecipientsRequeue($campaign_recipients_requeue_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignRecipientsRequeue: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_recipients_requeue_request** | [**\ShadowSoftware\Sdk\Generated\Model\CampaignRecipientsRequeueRequest**](../Model/CampaignRecipientsRequeueRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\CampaignRecipientsRequeue200Response**](../Model/CampaignRecipientsRequeue200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignSendTest()`

```php
campaignSendTest($campaign_send_test_request): \ShadowSoftware\Sdk\Generated\Model\CampaignSendTest200Response
```

Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email campaigns go out through the tenant's own SMTP settings — exactly the path a real send uses. Pass to_email. Requires complete SMTP settings.  Text (SMS) campaigns go out through the tenant's connected Twilio account. Pass to_phone instead of to_email. Requires Twilio to be connected.  This is a preview only: it does NOT start the campaign, does NOT touch the audience, and does NOT record any send/open/click stats. Personalization tokens render with sample values; unsubscribe links point at harmless test URLs.  Works on a campaign in any status (a draft preview is the common case). Always confirm the campaign_id with the vendor first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_send_test_request = new \ShadowSoftware\Sdk\Generated\Model\CampaignSendTestRequest(); // \ShadowSoftware\Sdk\Generated\Model\CampaignSendTestRequest

try {
    $result = $apiInstance->campaignSendTest($campaign_send_test_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignSendTest: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_send_test_request** | [**\ShadowSoftware\Sdk\Generated\Model\CampaignSendTestRequest**](../Model/CampaignSendTestRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\CampaignSendTest200Response**](../Model/CampaignSendTest200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignSetImage()`

```php
campaignSetImage($campaign_set_image_request): \ShadowSoftware\Sdk\Generated\Model\CampaignSetImage200Response
```

Drop an image from the tenant's media library into a DRAFT campaign — no link copying, no manual HTML. Give it a campaign_id and a media_id (from the tenant's own library) and it fills an image placeholder in the campaign body with that image, using the library image's own public URL and alt text. The URL is on the tenant's public disk, so it passes the campaign HTML sanitizer untouched and renders in email clients.  WHICH SLOT IT FILLS:   System templates render image placeholders labelled \"Add image\". By default this tool fills the   FIRST remaining placeholder. Pass slot_index (1-based) to target a specific placeholder when a   template has more than one. If the body has no placeholder left, the image is appended at the end.  REQUIREMENTS:   - The campaign must be a DRAFT and belong to the tenant.   - The media asset must belong to the tenant and be public (private assets have no shareable URL).  Typical flow: campaign_upsert (create) → campaign_apply_template (design with placeholders) → campaign_set_image (fill each placeholder) → campaign_upsert (real copy) → campaign_send_test.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_set_image_request = new \ShadowSoftware\Sdk\Generated\Model\CampaignSetImageRequest(); // \ShadowSoftware\Sdk\Generated\Model\CampaignSetImageRequest

try {
    $result = $apiInstance->campaignSetImage($campaign_set_image_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignSetImage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_set_image_request** | [**\ShadowSoftware\Sdk\Generated\Model\CampaignSetImageRequest**](../Model/CampaignSetImageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\CampaignSetImage200Response**](../Model/CampaignSetImage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignUpsert()`

```php
campaignUpsert($campaign_upsert_request): \ShadowSoftware\Sdk\Generated\Model\CampaignUpsert200Response
```

Create or edit a vendor email campaign on behalf of a tenant. This is the vendor → their-customers campaign universe (NOT the platform drip-to-leads system). Completed campaigns cannot be edited.  CREATE MODE (no campaign_id):   Creates a new draft. For email campaigns, `name` and `subject` are required; `html_body` is optional   on create — omit it to start blank and apply a system template afterwards with campaign_apply_template.   For text (SMS) campaigns, set channel to \"sms\" and pass `sms_body` (name required). Twilio must be   connected on the tenant.  UPDATE MODE (campaign_id provided):   Edits an existing campaign. Only the fields you pass are changed; omitted fields are left as-is.   Drafts accept all fields. Sending or paused campaigns accept content fields only — for email that is   name, subject, html_body, plain_body; for text that is name and sms_body. Sending campaigns keep   running and unreached recipients get the latest version. Scheduled, cancelled, and failed campaigns   must be edited from the vendor admin.  HTML BODY (email only):   Pass the full email HTML in `html_body`. It is sanitized exactly like the vendor admin editor:   a full document (<!DOCTYPE …> / <html>) keeps its table-based structure; a fragment is run   through the stricter inline allowlist. Use the literal token {{unsubscribe_url}} where the   unsubscribe link should appear — it is replaced per-recipient at send time and a List-Unsubscribe   header is added automatically. Personalization tokens {{first_name}} and {{last_name}} are also   replaced at send time.  SMS BODY (text only):   Pass the message in `sms_body` (max 1600 chars). Tokens {{first_name}} and {{last_name}} are   replaced at send time. Text campaigns only target opted-in shop customers.  AUDIENCE / MODE (email only):   mode is \"smtp\" (default) or \"webhook\". audience_includes_customers (default true) targets the   tenant's own customers. This tool does not send anything — use campaign_send_test to preview,   then the vendor sends from the admin.  Always confirm the tenant_slug with tenant_list first. To populate a draft from a built-in design, create it here, then call campaign_apply_template.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_upsert_request = new \ShadowSoftware\Sdk\Generated\Model\CampaignUpsertRequest(); // \ShadowSoftware\Sdk\Generated\Model\CampaignUpsertRequest

try {
    $result = $apiInstance->campaignUpsert($campaign_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_upsert_request** | [**\ShadowSoftware\Sdk\Generated\Model\CampaignUpsertRequest**](../Model/CampaignUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\CampaignUpsert200Response**](../Model/CampaignUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
