# ShadowSoftware\DabDash\MarketingApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**campaignApplyTemplate()**](MarketingApi.md#campaignApplyTemplate) | **POST** /api/v1/tools/campaign_apply_template | Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \&quot;Choose template\&quot; action in the vendor admin.  The template is rendered with the tenant&#39;s own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign&#39;s html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \&quot;Your headline here\&quot;, etc.) — after applying, use campaign_upsert to set the real html_body with this tenant&#39;s products, prices, and offers, or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_upsert (replace html_body with real data) → campaign_send_test (preview). |
| [**campaignAudienceInspect()**](MarketingApi.md#campaignAudienceInspect) | **POST** /api/v1/tools/campaign_audience_inspect | Split the tenant&#39;s campaign audience into warm (≥1 past order) vs cold (no order history) recipients. Use this before drafting a newsletter or SMS in the Create Promotion flow: warm audiences can get exclusive codes and commercial copy; cold audiences need personalized, spam-safe language — never hard-sell or aggressive exclusive deals.  Returns counts only — it does not create or send a campaign. |
| [**campaignControl()**](MarketingApi.md#campaignControl) | **POST** /api/v1/tools/campaign_control | Pause or resume a vendor email/SMS campaign.  pause: sets status&#x3D;paused with paused_reason&#x3D;manual. Works from sending or recovering. Pending recipients stay pending; the dispatcher skips this campaign so sibling sending campaigns can use the shared throttle.  resume: from paused → sending (clears pause fields). From recovering → sending and requeues transport-failed recipients (same as auto-resume after a healthy webhook health check).  dry_run defaults TRUE. Always confirm tenant_slug and campaign_id first. |
| [**campaignFlyerStylePack()**](MarketingApi.md#campaignFlyerStylePack) | **POST** /api/v1/tools/campaign_flyer_style_pack | Teach campaign_generate_flyer what this shop&#39;s flyers look like, using their OWN past work.  A style pack is two things, both stored on the shop itself:   - Images: one or more past flyers or mood boards, held in the shop&#39;s media library.   - Art direction: short lines describing the look to carry forward (\&quot;seasonal scenes, deal-dense     grids, deep green and gold, calm lower third\&quot;).  When a pack is saved, every flyer generation attaches it ahead of the product photos and asks the model to match the MOOD and COMPOSITION — never to copy text, logos or prices out of it.  ACTIONS:   - show  — what is saved right now: the direction lines and the images on file.   - set   — save direction lines and/or point the pack at media library images.             prompt_lines replaces the saved direction. media_ids MOVES those library assets into             the style-reference folder (they stay ordinary library assets, reusable elsewhere).   - clear — remove the direction lines, and with clear_images&#x3D;true also take the images back out             of the style-reference folder. Nothing is deleted from the library.  HOW TO GET AN IMAGE IN: upload the flyer through the shop&#39;s media library (or send it to Dabby in chat, which files it there), then pass its media_id here. The pack lives entirely in the database, so it survives every deploy and can be changed at any time without one. |
| [**campaignGenerateFlyer()**](MarketingApi.md#campaignGenerateFlyer) | **POST** /api/v1/tools/campaign_generate_flyer | Generate BESPOKE flyer artwork for a campaign from the shop&#39;s own product photos, then drop it straight into the campaign design. This is the intended way to make a marketing email look like a designed flyer instead of a text layout — reach for it before writing copy, because the rest of the email is built to sit under the artwork.  WHAT IT PRODUCES:   A layered, dimensional flyer illustration in the shop&#39;s brand colours, composed around the real   packaging from the product photos you name. It renders NO text — the email supplies every word   as real text so it stays readable, translatable, and safe with images disabled.  REQUIREMENTS:   The shop must have its own AI key connected (Settings → Dab AI). Generation is billed to that   key, not to the platform, so a shop without one gets a clear message instead of a flyer. Use   the highest-quality model available on the key; that is the default and it is the point.   A shop that has saved a style pack (campaign_flyer_style_pack) automatically gets its own past   flyers attached as references, so the artwork matches the creative it already sends.  HOW TO USE IT:   - theme        — what the flyer is for, in plain words (\&quot;September restock, cosy autumn mood\&quot;).   - product_ids  — the products to feature. Omit and it picks the shop&#39;s newest featured products                    that actually have photos.   - campaign_id  — a DRAFT campaign to place the artwork into. Omit to just add it to the media                    library and place it later with campaign_set_image.   - slot_index   — which image slot to fill (1-based). Defaults to the first, which is the hero.   - reference_media_ids — if the vendor just attached a past flyer (or any image) to THIS chat and                    asked for something \&quot;in the same style\&quot; or \&quot;like this one\&quot;, pass its media_id                    here. The conversation shows it as \&quot;[Library image saved: media_id&#x3D;N, ...]\&quot;                    right after the attachment. This is a one-off style/composition reference for                    this generation only — it steers mood, layout and palette, it is never                    reproduced verbatim, and it is not saved for next time. Never ask the vendor                    for a URL or to re-upload when their image is already attached to the chat —                    read its media_id from the conversation and pass it here instead. For a shop                    that wants the same look on every flyer going forward, save it once with                    campaign_flyer_style_pack instead of repeating reference_media_ids.  Typical flow: campaign_upsert (create) → campaign_apply_template (design) → campaign_upsert (real copy) → campaign_generate_flyer (hero artwork LAST) → campaign_send_test. Generate the artwork only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after the artwork is placed erases it.  The result always lands in the media library, so it can be reused on the storefront or in a later send without generating twice. |
| [**campaignGet()**](MarketingApi.md#campaignGet) | **POST** /api/v1/tools/campaign_get | Read a campaign back, including the message body you are about to edit.  Every other campaign tool reports html_body_length — a character count — so without this you would be rewriting a document you have never seen. Call this first whenever you are asked to change, finish, or comment on existing copy.  Email campaigns return html_body; text campaigns return sms_body. A rendered system template runs 10-32KB, so read once and edit from what you read rather than re-fetching between changes. |
| [**campaignRecipientsRequeue()**](MarketingApi.md#campaignRecipientsRequeue) | **POST** /api/v1/tools/campaign_recipients_requeue | Unstick a vendor email/SMS campaign that auto-paused or stalled mid-send.  Resets failed recipients (and optionally stale \&quot;sending\&quot; rows) back to pending, clears webhook_consecutive_failures / paused_reason / last_error, and sets the campaign status to sending so the dispatcher continues. Already-sent recipients are never touched.  Pass retry_sending_log_errors&#x3D;true to retry every recipient that appears in the campaign sending log with a retryable error (502/timeout/SMTP soft fail, etc.). Those jobs dispatch immediately (up to dispatch_limit) so they are not stuck behind a large pending queue. The log rows stay — they are diagnostic history.  Use when a webhook campaign froze after consecutive endpoint errors (or soft SMTP failures that were misclassified before the soft-fail fix) and pending + failed recipients remain.  dry_run defaults to TRUE — first call reports counts only. Pass dry_run&#x3D;false to apply. Always confirm tenant_slug with tenant_list and campaign_id with the vendor. |
| [**campaignSendTest()**](MarketingApi.md#campaignSendTest) | **POST** /api/v1/tools/campaign_send_test | Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email campaigns go out through the tenant&#39;s own SMTP settings — exactly the path a real send uses. Pass to_email. Requires complete SMTP settings.  Text (SMS) campaigns go out through the tenant&#39;s connected Twilio account. Pass to_phone instead of to_email. Requires Twilio to be connected.  This is a preview only: it does NOT start the campaign, does NOT touch the audience, and does NOT record any send/open/click stats. Personalization tokens render with sample values; unsubscribe links point at harmless test URLs.  Works on a campaign in any status (a draft preview is the common case). Always confirm the campaign_id with the vendor first. |
| [**campaignSetImage()**](MarketingApi.md#campaignSetImage) | **POST** /api/v1/tools/campaign_set_image | Drop an image from the tenant&#39;s media library into a DRAFT campaign — no link copying, no manual HTML. Give it a campaign_id and a media_id (from the tenant&#39;s own library) and it fills an image placeholder in the campaign body with that image, using the library image&#39;s own public URL and alt text. The URL is on the tenant&#39;s public disk, so it passes the campaign HTML sanitizer untouched and renders in email clients.  WHICH SLOT IT FILLS:   System templates render image placeholders labelled \&quot;Add image\&quot;. By default this tool fills the   FIRST remaining placeholder. Pass slot_index (1-based) to target a specific placeholder when a   template has more than one. If the body has no placeholder left, the image is appended at the end.  REQUIREMENTS:   - The campaign must be a DRAFT and belong to the tenant.   - The media asset must belong to the tenant and be public (private assets have no shareable URL).  Typical flow: campaign_upsert (create) → campaign_apply_template (design with placeholders) → campaign_upsert (real copy) → campaign_set_image (fill each placeholder LAST) → campaign_send_test. Fill placeholders only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after an image is placed erases it. |
| [**campaignSpamScore()**](MarketingApi.md#campaignSpamScore) | **POST** /api/v1/tools/campaign_spam_score | Score vendor campaign copy for inbox risk (email HTML or SMS).  One score only: 0–100 (0 &#x3D; spam, 100 &#x3D; primary-inbox friendly). Live scoring uses first-party rules. Pass for_send&#x3D;true to run the same deep filter check used on send/schedule and fold it into that single number (never a second score).  Vendors cannot send or schedule below the platform minimum (default 80). Aim for 80+ before handoff; 85+ is excellent.  Pass campaign_id (loads draft content) OR inline channel + content fields. |
| [**campaignUpsert()**](MarketingApi.md#campaignUpsert) | **POST** /api/v1/tools/campaign_upsert | Create or edit a vendor email campaign on behalf of a tenant. This is the vendor → their-customers campaign universe (NOT the platform drip-to-leads system). Completed campaigns cannot be edited.  CREATE MODE (no campaign_id):   Creates a new draft. For email campaigns, &#x60;name&#x60; and &#x60;subject&#x60; are required; &#x60;html_body&#x60; is optional   on create — omit it to start blank and apply a system template afterwards with campaign_apply_template.   For text (SMS) campaigns, set channel to \&quot;sms\&quot; and pass &#x60;sms_body&#x60; (name required). Twilio must be   connected on the tenant.  UPDATE MODE (campaign_id provided):   Edits an existing campaign. Only the fields you pass are changed; omitted fields are left as-is.   Drafts accept all fields. Sending or paused campaigns accept content fields only — for email that is   name, subject, html_body, plain_body, fallback_first_name; for text that is name, sms_body,   and fallback_first_name. Sending campaigns keep   running and unreached recipients get the latest version. Scheduled, cancelled, and failed campaigns   must be edited from the vendor admin.  HTML BODY (email only):   Pass the full email HTML in &#x60;html_body&#x60;. It is sanitized exactly like the vendor admin editor:   a full document (&lt;!DOCTYPE …&gt; / &lt;html&gt;) keeps its table-based structure; a fragment is run   through the stricter inline allowlist. Use the literal token {{unsubscribe_url}} where the   unsubscribe link should appear — it is replaced per-recipient at send time and a List-Unsubscribe   header is added automatically. Personalization tokens {{first_name}} and {{last_name}} are also   replaced at send time. When {{first_name}} is blank, fallback_first_name is used (default \&quot;there\&quot;   when unset). Pass fallback_first_name as \&quot;\&quot; to clear an override.  SMS BODY (text only):   Pass the message in &#x60;sms_body&#x60; (max 1600 chars). Tokens {{first_name}} and {{last_name}} are   replaced at send time (same first-name fallback as email). Text campaigns only target opted-in shop customers.  AUDIENCE / MODE (email only):   mode is \&quot;smtp\&quot; (default) or \&quot;webhook\&quot;. audience_includes_customers (default true) targets the   tenant&#39;s own customers. This tool does not send anything — use campaign_send_test to preview,   then the vendor sends from the admin.  Always confirm the tenant_slug with tenant_list first. To populate a draft from a built-in design, create it here, then call campaign_apply_template.  ORDERING WARNING: html_body is a full replacement, not a merge. If campaign_generate_flyer or campaign_set_image has already placed an image into this campaign&#39;s body, do NOT call this tool again with a new html_body afterward — it silently erases the placed image. Finalize all copy with this tool FIRST, then place images last. |


## `campaignApplyTemplate()`

```php
campaignApplyTemplate($campaign_apply_template_request): \ShadowSoftware\DabDash\Model\CampaignApplyTemplate200Response
```

Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \"Choose template\" action in the vendor admin.  The template is rendered with the tenant's own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign's html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \"Your headline here\", etc.) — after applying, use campaign_upsert to set the real html_body with this tenant's products, prices, and offers, or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_upsert (replace html_body with real data) → campaign_send_test (preview).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_apply_template_request = new \ShadowSoftware\DabDash\Model\CampaignApplyTemplateRequest(); // \ShadowSoftware\DabDash\Model\CampaignApplyTemplateRequest

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
| **campaign_apply_template_request** | [**\ShadowSoftware\DabDash\Model\CampaignApplyTemplateRequest**](../Model/CampaignApplyTemplateRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignApplyTemplate200Response**](../Model/CampaignApplyTemplate200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignAudienceInspect()`

```php
campaignAudienceInspect($campaign_audience_inspect_request): \ShadowSoftware\DabDash\Model\CampaignAudienceInspect200Response
```

Split the tenant's campaign audience into warm (≥1 past order) vs cold (no order history) recipients. Use this before drafting a newsletter or SMS in the Create Promotion flow: warm audiences can get exclusive codes and commercial copy; cold audiences need personalized, spam-safe language — never hard-sell or aggressive exclusive deals.  Returns counts only — it does not create or send a campaign.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_audience_inspect_request = new \ShadowSoftware\DabDash\Model\CampaignAudienceInspectRequest(); // \ShadowSoftware\DabDash\Model\CampaignAudienceInspectRequest

try {
    $result = $apiInstance->campaignAudienceInspect($campaign_audience_inspect_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignAudienceInspect: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_audience_inspect_request** | [**\ShadowSoftware\DabDash\Model\CampaignAudienceInspectRequest**](../Model/CampaignAudienceInspectRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignAudienceInspect200Response**](../Model/CampaignAudienceInspect200Response.md)

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
campaignControl($campaign_control_request): \ShadowSoftware\DabDash\Model\CampaignControl200Response
```

Pause or resume a vendor email/SMS campaign.  pause: sets status=paused with paused_reason=manual. Works from sending or recovering. Pending recipients stay pending; the dispatcher skips this campaign so sibling sending campaigns can use the shared throttle.  resume: from paused → sending (clears pause fields). From recovering → sending and requeues transport-failed recipients (same as auto-resume after a healthy webhook health check).  dry_run defaults TRUE. Always confirm tenant_slug and campaign_id first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_control_request = new \ShadowSoftware\DabDash\Model\CampaignControlRequest(); // \ShadowSoftware\DabDash\Model\CampaignControlRequest

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
| **campaign_control_request** | [**\ShadowSoftware\DabDash\Model\CampaignControlRequest**](../Model/CampaignControlRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignControl200Response**](../Model/CampaignControl200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignFlyerStylePack()`

```php
campaignFlyerStylePack($campaign_flyer_style_pack_request): \ShadowSoftware\DabDash\Model\CampaignFlyerStylePack200Response
```

Teach campaign_generate_flyer what this shop's flyers look like, using their OWN past work.  A style pack is two things, both stored on the shop itself:   - Images: one or more past flyers or mood boards, held in the shop's media library.   - Art direction: short lines describing the look to carry forward (\"seasonal scenes, deal-dense     grids, deep green and gold, calm lower third\").  When a pack is saved, every flyer generation attaches it ahead of the product photos and asks the model to match the MOOD and COMPOSITION — never to copy text, logos or prices out of it.  ACTIONS:   - show  — what is saved right now: the direction lines and the images on file.   - set   — save direction lines and/or point the pack at media library images.             prompt_lines replaces the saved direction. media_ids MOVES those library assets into             the style-reference folder (they stay ordinary library assets, reusable elsewhere).   - clear — remove the direction lines, and with clear_images=true also take the images back out             of the style-reference folder. Nothing is deleted from the library.  HOW TO GET AN IMAGE IN: upload the flyer through the shop's media library (or send it to Dabby in chat, which files it there), then pass its media_id here. The pack lives entirely in the database, so it survives every deploy and can be changed at any time without one.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_flyer_style_pack_request = new \ShadowSoftware\DabDash\Model\CampaignFlyerStylePackRequest(); // \ShadowSoftware\DabDash\Model\CampaignFlyerStylePackRequest

try {
    $result = $apiInstance->campaignFlyerStylePack($campaign_flyer_style_pack_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignFlyerStylePack: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_flyer_style_pack_request** | [**\ShadowSoftware\DabDash\Model\CampaignFlyerStylePackRequest**](../Model/CampaignFlyerStylePackRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignFlyerStylePack200Response**](../Model/CampaignFlyerStylePack200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignGenerateFlyer()`

```php
campaignGenerateFlyer($campaign_generate_flyer_request): \ShadowSoftware\DabDash\Model\CampaignGenerateFlyer200Response
```

Generate BESPOKE flyer artwork for a campaign from the shop's own product photos, then drop it straight into the campaign design. This is the intended way to make a marketing email look like a designed flyer instead of a text layout — reach for it before writing copy, because the rest of the email is built to sit under the artwork.  WHAT IT PRODUCES:   A layered, dimensional flyer illustration in the shop's brand colours, composed around the real   packaging from the product photos you name. It renders NO text — the email supplies every word   as real text so it stays readable, translatable, and safe with images disabled.  REQUIREMENTS:   The shop must have its own AI key connected (Settings → Dab AI). Generation is billed to that   key, not to the platform, so a shop without one gets a clear message instead of a flyer. Use   the highest-quality model available on the key; that is the default and it is the point.   A shop that has saved a style pack (campaign_flyer_style_pack) automatically gets its own past   flyers attached as references, so the artwork matches the creative it already sends.  HOW TO USE IT:   - theme        — what the flyer is for, in plain words (\"September restock, cosy autumn mood\").   - product_ids  — the products to feature. Omit and it picks the shop's newest featured products                    that actually have photos.   - campaign_id  — a DRAFT campaign to place the artwork into. Omit to just add it to the media                    library and place it later with campaign_set_image.   - slot_index   — which image slot to fill (1-based). Defaults to the first, which is the hero.   - reference_media_ids — if the vendor just attached a past flyer (or any image) to THIS chat and                    asked for something \"in the same style\" or \"like this one\", pass its media_id                    here. The conversation shows it as \"[Library image saved: media_id=N, ...]\"                    right after the attachment. This is a one-off style/composition reference for                    this generation only — it steers mood, layout and palette, it is never                    reproduced verbatim, and it is not saved for next time. Never ask the vendor                    for a URL or to re-upload when their image is already attached to the chat —                    read its media_id from the conversation and pass it here instead. For a shop                    that wants the same look on every flyer going forward, save it once with                    campaign_flyer_style_pack instead of repeating reference_media_ids.  Typical flow: campaign_upsert (create) → campaign_apply_template (design) → campaign_upsert (real copy) → campaign_generate_flyer (hero artwork LAST) → campaign_send_test. Generate the artwork only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after the artwork is placed erases it.  The result always lands in the media library, so it can be reused on the storefront or in a later send without generating twice.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_generate_flyer_request = new \ShadowSoftware\DabDash\Model\CampaignGenerateFlyerRequest(); // \ShadowSoftware\DabDash\Model\CampaignGenerateFlyerRequest

try {
    $result = $apiInstance->campaignGenerateFlyer($campaign_generate_flyer_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignGenerateFlyer: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_generate_flyer_request** | [**\ShadowSoftware\DabDash\Model\CampaignGenerateFlyerRequest**](../Model/CampaignGenerateFlyerRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignGenerateFlyer200Response**](../Model/CampaignGenerateFlyer200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignGet()`

```php
campaignGet($campaign_get_request): \ShadowSoftware\DabDash\Model\CampaignGet200Response
```

Read a campaign back, including the message body you are about to edit.  Every other campaign tool reports html_body_length — a character count — so without this you would be rewriting a document you have never seen. Call this first whenever you are asked to change, finish, or comment on existing copy.  Email campaigns return html_body; text campaigns return sms_body. A rendered system template runs 10-32KB, so read once and edit from what you read rather than re-fetching between changes.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_get_request = new \ShadowSoftware\DabDash\Model\CampaignGetRequest(); // \ShadowSoftware\DabDash\Model\CampaignGetRequest

try {
    $result = $apiInstance->campaignGet($campaign_get_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_get_request** | [**\ShadowSoftware\DabDash\Model\CampaignGetRequest**](../Model/CampaignGetRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignGet200Response**](../Model/CampaignGet200Response.md)

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
campaignRecipientsRequeue($campaign_recipients_requeue_request): \ShadowSoftware\DabDash\Model\CampaignRecipientsRequeue200Response
```

Unstick a vendor email/SMS campaign that auto-paused or stalled mid-send.  Resets failed recipients (and optionally stale \"sending\" rows) back to pending, clears webhook_consecutive_failures / paused_reason / last_error, and sets the campaign status to sending so the dispatcher continues. Already-sent recipients are never touched.  Pass retry_sending_log_errors=true to retry every recipient that appears in the campaign sending log with a retryable error (502/timeout/SMTP soft fail, etc.). Those jobs dispatch immediately (up to dispatch_limit) so they are not stuck behind a large pending queue. The log rows stay — they are diagnostic history.  Use when a webhook campaign froze after consecutive endpoint errors (or soft SMTP failures that were misclassified before the soft-fail fix) and pending + failed recipients remain.  dry_run defaults to TRUE — first call reports counts only. Pass dry_run=false to apply. Always confirm tenant_slug with tenant_list and campaign_id with the vendor.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_recipients_requeue_request = new \ShadowSoftware\DabDash\Model\CampaignRecipientsRequeueRequest(); // \ShadowSoftware\DabDash\Model\CampaignRecipientsRequeueRequest

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
| **campaign_recipients_requeue_request** | [**\ShadowSoftware\DabDash\Model\CampaignRecipientsRequeueRequest**](../Model/CampaignRecipientsRequeueRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignRecipientsRequeue200Response**](../Model/CampaignRecipientsRequeue200Response.md)

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
campaignSendTest($campaign_send_test_request): \ShadowSoftware\DabDash\Model\CampaignSendTest200Response
```

Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email campaigns go out through the tenant's own SMTP settings — exactly the path a real send uses. Pass to_email. Requires complete SMTP settings.  Text (SMS) campaigns go out through the tenant's connected Twilio account. Pass to_phone instead of to_email. Requires Twilio to be connected.  This is a preview only: it does NOT start the campaign, does NOT touch the audience, and does NOT record any send/open/click stats. Personalization tokens render with sample values; unsubscribe links point at harmless test URLs.  Works on a campaign in any status (a draft preview is the common case). Always confirm the campaign_id with the vendor first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_send_test_request = new \ShadowSoftware\DabDash\Model\CampaignSendTestRequest(); // \ShadowSoftware\DabDash\Model\CampaignSendTestRequest

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
| **campaign_send_test_request** | [**\ShadowSoftware\DabDash\Model\CampaignSendTestRequest**](../Model/CampaignSendTestRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignSendTest200Response**](../Model/CampaignSendTest200Response.md)

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
campaignSetImage($campaign_set_image_request): \ShadowSoftware\DabDash\Model\CampaignSetImage200Response
```

Drop an image from the tenant's media library into a DRAFT campaign — no link copying, no manual HTML. Give it a campaign_id and a media_id (from the tenant's own library) and it fills an image placeholder in the campaign body with that image, using the library image's own public URL and alt text. The URL is on the tenant's public disk, so it passes the campaign HTML sanitizer untouched and renders in email clients.  WHICH SLOT IT FILLS:   System templates render image placeholders labelled \"Add image\". By default this tool fills the   FIRST remaining placeholder. Pass slot_index (1-based) to target a specific placeholder when a   template has more than one. If the body has no placeholder left, the image is appended at the end.  REQUIREMENTS:   - The campaign must be a DRAFT and belong to the tenant.   - The media asset must belong to the tenant and be public (private assets have no shareable URL).  Typical flow: campaign_upsert (create) → campaign_apply_template (design with placeholders) → campaign_upsert (real copy) → campaign_set_image (fill each placeholder LAST) → campaign_send_test. Fill placeholders only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after an image is placed erases it.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_set_image_request = new \ShadowSoftware\DabDash\Model\CampaignSetImageRequest(); // \ShadowSoftware\DabDash\Model\CampaignSetImageRequest

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
| **campaign_set_image_request** | [**\ShadowSoftware\DabDash\Model\CampaignSetImageRequest**](../Model/CampaignSetImageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignSetImage200Response**](../Model/CampaignSetImage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignSpamScore()`

```php
campaignSpamScore($campaign_spam_score_request): \ShadowSoftware\DabDash\Model\CampaignSpamScore200Response
```

Score vendor campaign copy for inbox risk (email HTML or SMS).  One score only: 0–100 (0 = spam, 100 = primary-inbox friendly). Live scoring uses first-party rules. Pass for_send=true to run the same deep filter check used on send/schedule and fold it into that single number (never a second score).  Vendors cannot send or schedule below the platform minimum (default 80). Aim for 80+ before handoff; 85+ is excellent.  Pass campaign_id (loads draft content) OR inline channel + content fields.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_spam_score_request = new \ShadowSoftware\DabDash\Model\CampaignSpamScoreRequest(); // \ShadowSoftware\DabDash\Model\CampaignSpamScoreRequest

try {
    $result = $apiInstance->campaignSpamScore($campaign_spam_score_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignSpamScore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_spam_score_request** | [**\ShadowSoftware\DabDash\Model\CampaignSpamScoreRequest**](../Model/CampaignSpamScoreRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignSpamScore200Response**](../Model/CampaignSpamScore200Response.md)

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
campaignUpsert($campaign_upsert_request): \ShadowSoftware\DabDash\Model\CampaignUpsert200Response
```

Create or edit a vendor email campaign on behalf of a tenant. This is the vendor → their-customers campaign universe (NOT the platform drip-to-leads system). Completed campaigns cannot be edited.  CREATE MODE (no campaign_id):   Creates a new draft. For email campaigns, `name` and `subject` are required; `html_body` is optional   on create — omit it to start blank and apply a system template afterwards with campaign_apply_template.   For text (SMS) campaigns, set channel to \"sms\" and pass `sms_body` (name required). Twilio must be   connected on the tenant.  UPDATE MODE (campaign_id provided):   Edits an existing campaign. Only the fields you pass are changed; omitted fields are left as-is.   Drafts accept all fields. Sending or paused campaigns accept content fields only — for email that is   name, subject, html_body, plain_body, fallback_first_name; for text that is name, sms_body,   and fallback_first_name. Sending campaigns keep   running and unreached recipients get the latest version. Scheduled, cancelled, and failed campaigns   must be edited from the vendor admin.  HTML BODY (email only):   Pass the full email HTML in `html_body`. It is sanitized exactly like the vendor admin editor:   a full document (<!DOCTYPE …> / <html>) keeps its table-based structure; a fragment is run   through the stricter inline allowlist. Use the literal token {{unsubscribe_url}} where the   unsubscribe link should appear — it is replaced per-recipient at send time and a List-Unsubscribe   header is added automatically. Personalization tokens {{first_name}} and {{last_name}} are also   replaced at send time. When {{first_name}} is blank, fallback_first_name is used (default \"there\"   when unset). Pass fallback_first_name as \"\" to clear an override.  SMS BODY (text only):   Pass the message in `sms_body` (max 1600 chars). Tokens {{first_name}} and {{last_name}} are   replaced at send time (same first-name fallback as email). Text campaigns only target opted-in shop customers.  AUDIENCE / MODE (email only):   mode is \"smtp\" (default) or \"webhook\". audience_includes_customers (default true) targets the   tenant's own customers. This tool does not send anything — use campaign_send_test to preview,   then the vendor sends from the admin.  Always confirm the tenant_slug with tenant_list first. To populate a draft from a built-in design, create it here, then call campaign_apply_template.  ORDERING WARNING: html_body is a full replacement, not a merge. If campaign_generate_flyer or campaign_set_image has already placed an image into this campaign's body, do NOT call this tool again with a new html_body afterward — it silently erases the placed image. Finalize all copy with this tool FIRST, then place images last.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\MarketingApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$campaign_upsert_request = new \ShadowSoftware\DabDash\Model\CampaignUpsertRequest(); // \ShadowSoftware\DabDash\Model\CampaignUpsertRequest

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
| **campaign_upsert_request** | [**\ShadowSoftware\DabDash\Model\CampaignUpsertRequest**](../Model/CampaignUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignUpsert200Response**](../Model/CampaignUpsert200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
