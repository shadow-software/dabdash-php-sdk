# ShadowSoftware\DabDash\MarketingApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**abandonedCartRecoveryManage()**](MarketingApi.md#abandonedCartRecoveryManage) | **POST** /api/v1/tools/abandoned_cart_recovery_manage | Show or configure one-time abandoned-cart recovery emails for a tenant. Eligible carts are sent to an account or guest email captured during checkout; carts must be idle for the configured delay and not already recovered. Sending also requires complete tenant SMTP settings. This tool does not send immediately.  ACTIONS:   show (default): current enabled state and delay.   set: pass enabled and/or delay_hours. Omitted values are preserved.   dry_run defaults true; pass dry_run&#x3D;false to save. |
| [**blogImport()**](MarketingApi.md#blogImport) | **POST** /api/v1/tools/blog_import | Create many blog posts (and the storefront&#39;s legal pages) in one call. tenant_blog_upsert can only edit an existing post; this creates new ones.  Pass posts with title and body (HTML), and optionally slug, excerpt, category, tags, meta_title, meta_description, published (default true), published_at (ISO date) and image_url (downloaded into the media library and used as the featured image). Posts are matched by slug within the store: existing ones are skipped unless update_existing&#x3D;true, in which case only the fields you pass are changed.  PRIVACY: a privacy-policy post remains at its own /blog/{slug} URL. When it is the store&#39;s policy, set that exact published URL with store_settings_manage privacy_url. Terms and refund remain available at their storefront policy URLs.  dry_run defaults true: the first call reports what would be created, updated or skipped. Pass dry_run&#x3D;false to write. Image failures never block a post. |
| [**campaignApplyTemplate()**](MarketingApi.md#campaignApplyTemplate) | **POST** /api/v1/tools/campaign_apply_template | Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \&quot;Choose template\&quot; action in the vendor admin.  The template is rendered with the tenant&#39;s own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign&#39;s html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \&quot;Your headline here\&quot;, etc.) — after applying, call campaign_get, then campaign_upsert to swap placeholder copy/prices inside the existing html_body (do NOT replace the whole body — that deletes the template layout). Use campaign_set_image for product photos. Or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_get + campaign_upsert (edit copy in place) + campaign_set_image (photos) → campaign_send_test (preview). |
| [**campaignAudienceInspect()**](MarketingApi.md#campaignAudienceInspect) | **POST** /api/v1/tools/campaign_audience_inspect | Split the tenant&#39;s campaign audience into warm (≥1 past order) vs cold (no order history) recipients. Use this before drafting a newsletter or SMS in the Create Promotion flow: warm audiences can get exclusive codes and commercial copy; cold audiences need personalized, spam-safe language — never hard-sell or aggressive exclusive deals.  Returns counts only — it does not create or send a campaign. |
| [**campaignControl()**](MarketingApi.md#campaignControl) | **POST** /api/v1/tools/campaign_control | Pause or resume a vendor email/SMS campaign.  pause: sets status&#x3D;paused with paused_reason&#x3D;manual. Works from sending or recovering. Pending recipients stay pending; the dispatcher skips this campaign so sibling sending campaigns can use the shared throttle.  resume: from paused → sending (clears pause fields). From recovering → sending and requeues transport-failed recipients (same as auto-resume after a healthy webhook health check).  dry_run defaults TRUE. Always confirm tenant_slug and campaign_id first. |
| [**campaignFlyerStylePack()**](MarketingApi.md#campaignFlyerStylePack) | **POST** /api/v1/tools/campaign_flyer_style_pack | Teach campaign_generate_flyer what this shop&#39;s flyers look like, using their OWN past work.  A style pack is two things, both stored on the shop itself:   - Images: one or more past flyers or mood boards, held in the shop&#39;s media library.   - Art direction: short lines describing the look to carry forward (\&quot;seasonal scenes, deal-dense     grids, deep green and gold, calm lower third\&quot;).  When a pack is saved, every flyer generation attaches it ahead of the product photos and asks the model to match the MOOD and COMPOSITION — never to copy text, logos or prices out of it.  ACTIONS:   - show  — what is saved right now: the direction lines and the images on file.   - set   — save direction lines and/or point the pack at media library images.             prompt_lines replaces the saved direction. media_ids MOVES those library assets into             the style-reference folder (they stay ordinary library assets, reusable elsewhere).   - clear — remove the direction lines, and with clear_images&#x3D;true also take the images back out             of the style-reference folder. Nothing is deleted from the library.  HOW TO GET AN IMAGE IN: upload the flyer through the shop&#39;s media library (or send it to Dabby in chat, which files it there), then pass its media_id here. The pack lives entirely in the database, so it survives every deploy and can be changed at any time without one. |
| [**campaignGenerateFlyer()**](MarketingApi.md#campaignGenerateFlyer) | **POST** /api/v1/tools/campaign_generate_flyer | Generate BESPOKE flyer artwork for a campaign from the shop&#39;s own product photos, then drop it straight into the campaign design. This is the intended way to make a marketing email look like a designed flyer instead of a text layout — reach for it before writing copy, because the rest of the email is built to sit under the artwork.  WHAT IT PRODUCES:   A layered, dimensional flyer illustration in the shop&#39;s brand colours, composed around the real   packaging from the product photos you name. It renders NO text — the email supplies every word   as real text so it stays readable, translatable, and safe with images disabled.  REQUIREMENTS:   The shop must have its own AI key connected (Settings → Dab AI). Generation is billed to that   key, not to the platform, so a shop without one gets a clear message instead of a flyer. Use   the highest-quality model available on the key; that is the default and it is the point.   A shop that has saved a style pack (campaign_flyer_style_pack) automatically gets its own past   flyers attached as references, so the artwork matches the creative it already sends.  HOW TO USE IT:   - theme        — what the flyer is for, in plain words (\&quot;September restock, cosy autumn mood\&quot;).   - product_ids  — the products to feature. Omit and it picks the shop&#39;s newest featured products                    that actually have photos.   - campaign_id  — a DRAFT campaign to place the artwork into. Omit to just add it to the media                    library and place it later with campaign_set_image.   - slot_index   — which image slot to fill (1-based). Defaults to the first, which is the hero.   - reference_media_ids — if the vendor just attached a past flyer (or any image) to THIS chat and                    asked for something \&quot;in the same style\&quot; or \&quot;like this one\&quot;, pass its media_id                    here. The conversation shows it as \&quot;[Library image saved: media_id&#x3D;N, ...]\&quot;                    right after the attachment. This is a one-off style/composition reference for                    this generation only — it steers mood, layout and palette, it is never                    reproduced verbatim, and it is not saved for next time. Never ask the vendor                    for a URL or to re-upload when their image is already attached to the chat —                    read its media_id from the conversation and pass it here instead. For a shop                    that wants the same look on every flyer going forward, save it once with                    campaign_flyer_style_pack instead of repeating reference_media_ids.  Typical flow: campaign_upsert (create) → campaign_apply_template (design) → campaign_upsert (real copy) → campaign_generate_flyer (hero artwork LAST) → campaign_send_test. Generate the artwork only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after the artwork is placed erases it.  The result always lands in the media library, so it can be reused on the storefront or in a later send without generating twice. |
| [**campaignGet()**](MarketingApi.md#campaignGet) | **POST** /api/v1/tools/campaign_get | Read a campaign back, including the message body you are about to edit.  Every other campaign tool reports html_body_length — a character count — so without this you would be rewriting a document you have never seen. Call this first whenever you are asked to change, finish, or comment on existing copy.  Email campaigns return html_body; text campaigns return sms_body. A rendered system template runs 10-32KB, so read once and edit from what you read rather than re-fetching between changes. |
| [**campaignLaunch()**](MarketingApi.md#campaignLaunch) | **POST** /api/v1/tools/campaign_launch | Start, schedule, or cancel a vendor campaign after it is ready.  ACTIONS:   send_now — draft → materialize + send (or resume paused). Requires spam/inbox gate.   schedule — draft → scheduled_at (tenant timezone local datetime string).   cancel — sending/paused/recovering/scheduled/failed → cancelled.  dry_run defaults TRUE. Applying requires dry_run&#x3D;false AND confirm&#x3D;true. Always campaign_spam_score / campaign_send_test first, then confirm with the vendor. |
| [**campaignLinkAudit()**](MarketingApi.md#campaignLinkAudit) | **POST** /api/v1/tools/campaign_link_audit | Audit every href in a draft campaign&#39;s HTML against the tenant catalog.  For /products/{slug} and /categories/{slug} links it resolves the row (including draft/archived products) and reports status, stock, and live price. A link that HTTP-200s to the wrong or unpublished product is still a blocking issue — do not rely on curl alone. Optional check_http&#x3D;true also probes the live URL.  Returns links[] plus blocking_issues[] so launch prep can refuse send until product deep links are public and correct. |
| [**campaignList()**](MarketingApi.md#campaignList) | **POST** /api/v1/tools/campaign_list | List recent vendor email/SMS campaigns for a tenant so you can find campaign_id without guessing. Filter by status, channel, or a name/subject substring (q). Returns transport mode, audience flags, and list counts — use campaign_get for the full HTML/SMS body. |
| [**campaignPreviewImage()**](MarketingApi.md#campaignPreviewImage) | **POST** /api/v1/tools/campaign_preview_image | Render a draft email campaign as a PNG screenshot so you (and the vendor) can see exactly how it looks — desktop (600px) or mobile (390px).  Call this AFTER you finish editing an email (template apply, copy upsert, flyer/image placement) to visually confirm your own work before declaring the campaign ready. Prefer desktop first; also call with viewport&#x3D;mobile when the send matters on phones or when the design uses media queries.  When the connected model supports vision input, the response includes vision_review: either \&quot;LOOKS GOOD\&quot; or a short list of concrete layout problems. Fix those problems with campaign_upsert / campaign_set_image and re-check. When vision is unavailable, image_url is still returned for the vendor chat preview — say you cannot visually confirm on this model.  Pass campaign_id (loads stored html_body) OR inline html_body for unsaved markup. SMS campaigns are rejected. |
| [**campaignRecipientsRequeue()**](MarketingApi.md#campaignRecipientsRequeue) | **POST** /api/v1/tools/campaign_recipients_requeue | Unstick a vendor email/SMS campaign that auto-paused or stalled mid-send.  Resets failed recipients (and optionally stale \&quot;sending\&quot; rows) back to pending, clears webhook_consecutive_failures / paused_reason / last_error, and sets the campaign status to sending so the dispatcher continues. Already-sent recipients are never touched.  Pass retry_sending_log_errors&#x3D;true to retry every recipient that appears in the campaign sending log with a retryable error (502/timeout/SMTP soft fail, etc.). Those jobs dispatch immediately (up to dispatch_limit) so they are not stuck behind a large pending queue. The log rows stay — they are diagnostic history.  Use when a webhook campaign froze after consecutive endpoint errors (or soft SMTP failures that were misclassified before the soft-fail fix) and pending + failed recipients remain.  dry_run defaults to TRUE — first call reports counts only. Pass dry_run&#x3D;false to apply. Always confirm tenant_slug with tenant_list and campaign_id with the vendor. |
| [**campaignSendTest()**](MarketingApi.md#campaignSendTest) | **POST** /api/v1/tools/campaign_send_test | Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email: routes by campaign mode —   smtp → tenant SMTP (requires complete SMTP settings)   mailgun → Mailgun Messages API via a Ready sending name on the campaign   webhook → posts a test payload to the campaign webhook (no inbox email) Pass to_email for smtp/mailgun. Webhook mode uses to_email only as the sample recipient identity in the payload.  Text (SMS): Twilio. Pass to_phone. Requires Twilio connected.  Preview only — does NOT start the campaign, touch the audience, or record stats. |
| [**campaignSetImage()**](MarketingApi.md#campaignSetImage) | **POST** /api/v1/tools/campaign_set_image | Drop an image from the tenant&#39;s media library into a DRAFT campaign — no link copying, no manual HTML. Give it a campaign_id and a media_id (from the tenant&#39;s own library) and it fills an image placeholder in the campaign body with that image, using the library image&#39;s own public URL and alt text. The URL is on the tenant&#39;s public disk, so it passes the campaign HTML sanitizer untouched and renders in email clients.  WHICH SLOT IT FILLS:   System templates render image placeholders labelled \&quot;Add image\&quot;. By default this tool fills the   FIRST remaining placeholder. Pass slot_index (1-based) to target a specific placeholder when a   template has more than one. If the body has no placeholder left, the image is appended at the end.  REQUIREMENTS:   - The campaign must be a DRAFT and belong to the tenant.   - The media asset must belong to the tenant and be public (private assets have no shareable URL).  Typical flow: campaign_upsert (create) → campaign_apply_template (design with placeholders) → campaign_upsert (real copy) → campaign_set_image (fill each placeholder LAST) → campaign_send_test. Fill placeholders only after the copy is final — html_body on campaign_upsert is a full replacement, so calling campaign_upsert again after an image is placed erases it. |
| [**campaignSpamScore()**](MarketingApi.md#campaignSpamScore) | **POST** /api/v1/tools/campaign_spam_score | Score vendor campaign copy for inbox risk (email HTML or SMS).  One score only: 0–100 (0 &#x3D; spam, 100 &#x3D; primary-inbox friendly). Live scoring uses first-party rules. Pass for_send&#x3D;true to run the same deep filter check used on send/schedule and fold it into that single number (never a second score).  When for_send&#x3D;true on email, the response also includes spamassassin_score and deep_check_rules[] (Postmark/SpamAssassin hits with name, score, description) plus findings[] entries with actionable hints — use these to fix blocked campaigns.  Vendors cannot send or schedule below the platform minimum (default 80). Aim for 80+ before handoff; 85+ is excellent.  Pass campaign_id (loads draft content) OR inline channel + content fields. |
| [**campaignUpsert()**](MarketingApi.md#campaignUpsert) | **POST** /api/v1/tools/campaign_upsert | Create or edit a vendor email/SMS campaign (vendor → their customers; NOT platform drip-to-leads). Completed campaigns cannot be edited.  CREATE (no campaign_id): draft. Email needs name+subject (html_body optional — then campaign_apply_template). SMS needs name+sms_body and connected Twilio.  UPDATE (campaign_id): only passed fields change. Drafts accept all fields. Sending/paused accept content only (email: name/subject/html_body/plain_body/fallback_first_name; SMS: name/sms_body/fallback_first_name).  TRANSPORT (email): mode &#x3D; smtp | webhook | mailgun.   mailgun: require Ready mailgun_domain_ids (from tenant_mailgun_manage show); optional   mailgun_pace drip|blast.   webhook: prefer saved_webhook_id, or inline webhook_url (+ optional headers/health). AUDIENCE: contact_list_ids (manual and/or Smart Lists) and/or audience_includes_customers (default true). Lead lists cannot use smtp — use mailgun or webhook. SMS may attach Smart Lists only (not imported manual lists). Discover lists with contact_list_manage / smart_list_manage.  Optional: throttle_per_minute/hour, send_interval_minutes (1–30), send_window_*.  Does not start a live send — use campaign_send_test, then campaign_launch (confirm) or the vendor admin Send button.  ORDERING: html_body is a full replace. Finalize copy before campaign_generate_flyer / campaign_set_image, or later html_body upserts erase placed images. |
| [**contactListManage()**](MarketingApi.md#contactListManage) | **POST** /api/v1/tools/contact_list_manage | Discover and manage contact lists (manual + Smart) for campaigns, coupons, and Klaviyo.  ACTIONS:   list (default): all lists with id, name, kind (manual|smart), default_type, contacts_count,     usable_for hints (email/sms/coupon/klaviyo).   show: one list by list_id (includes Smart List definition when kind&#x3D;smart).   create_manual: create an empty imported/manual list (name required; default_type lead|customer).   rename: update name/description/default_type on a manual list (not Smart — use smart_list_manage).   delete: remove a list (dry_run default true; applying needs dry_run&#x3D;false + confirm&#x3D;true).  CSV import stays in the vendor admin. Use smart_list_manage for Smart List CRUD/preview. |
| [**notificationSettingsManage()**](MarketingApi.md#notificationSettingsManage) | **POST** /api/v1/tools/notification_settings_manage | Show or configure tenant-wide vendor order, low-stock, digest, restock, and storefront enquiry notification preferences. This tool deliberately excludes per-user opt-outs, SMS provider configuration, and WhatsApp. Set uses dry_run&#x3D;true by default and writes are audited. |
| [**reviewsManage()**](MarketingApi.md#reviewsManage) | **POST** /api/v1/tools/reviews_manage | Show or configure verified-purchase product reviews for a tenant, and preview or apply moderation changes to one review. Customers can review products only after a delivered order. Hidden reviews are excluded from the product rating.  ACTIONS:   show (default): current review setting and moderation counts.   set: pass enabled to turn review collection on or off. dry_run defaults true.   hide or unhide: pass review_id. dry_run defaults true; applying a moderation   change also recalculates that product&#39;s rating. |
| [**seoMaxImageManage()**](MarketingApi.md#seoMaxImageManage) | **POST** /api/v1/tools/seo_max_image_manage | Manage the SEO Max product-image rollout without Artisan commands or seed data. Use action&#x3D;status first. action&#x3D;configure toggles AVIF product delivery and the AVIF-only image sitemap. action&#x3D;backfill processes active products in bounded, resumable batches; dry_run&#x3D;true only reports work. Repeat with next_cursor as after_id until it is null. action&#x3D;submit notifies Google Search Console and IndexNow after the sitemap is live. |
| [**seoSettingsManage()**](MarketingApi.md#seoSettingsManage) | **POST** /api/v1/tools/seo_settings_manage | Show or update a tenant&#39;s standard SEO and SEO MAX settings. Standard fields control homepage metadata, title templates, sitemap, Open Graph, and Twitter metadata. SEO MAX fields control automated blog targeting and publishing, plus image delivery flags.  ACTIONS:   show (default): current values, including the stored featured-image canvas path.   set: pass only fields to change. dry_run defaults true; pass dry_run&#x3D;false to save. Enabling SEO MAX requires an active SEO MAX subscription or trial and complete schedule settings. Featured-image canvas uploads are handled by media tools and are read-only here. |
| [**smartListManage()**](MarketingApi.md#smartListManage) | **POST** /api/v1/tools/smart_list_manage | Create, edit, preview, or delete Smart Lists (dynamic customer audiences).  ACTIONS:   list / show — discover Smart Lists (also available via contact_list_manage).   upsert — create (omit list_id) or update. Requires name + definition     {version:1, match:\&quot;all\&quot;|\&quot;any\&quot;, conditions:[...]}.     Rule types: purchased_category, recent_category_buyers, lapsed_category_buyers,     not_purchased_category, purchased_product, purchase_count, spend_total,     top_spenders, dormant_customer, loyalty_lifetime_points, loyalty_tier.   preview — count matching customers for a definition (or existing list_id).   delete — dry_run default true; applying needs dry_run&#x3D;false + confirm&#x3D;true.  Attach Smart Lists to campaigns with campaign_upsert.contact_list_ids or to coupons via coupon_upsert.contact_list_id. Project to Klaviyo with tenant_klaviyo_manage. |
| [**suppressionManage()**](MarketingApi.md#suppressionManage) | **POST** /api/v1/tools/suppression_manage | Inspect or lift email suppressions / campaign unsubscribes for a tenant.  ACTIONS:   list — recent suppressed customers (email_opt_out) and unsubscribed contacts.   lift_transactional — clear customer email_opt_out for an email.   resubscribe — restore contact.subscribed for campaign lists.   lift_all — both transactional + campaign for one email.  Mutating actions: dry_run default true; apply needs dry_run&#x3D;false + confirm&#x3D;true. |
| [**tenantBlogUpsert()**](MarketingApi.md#tenantBlogUpsert) | **POST** /api/v1/tools/tenant_blog_upsert | Update an existing tenant (vendor) blog post&#39;s fields — title, excerpt, body, category, featured_image, meta_title, meta_description, og_image, or is_published. Only the fields you pass are changed. Use tenant_blog_lookup first to find the post_id or slug.  IMAGE: pass media_id (from media_list / media_upload) to set featured_image from a tenant media-library asset. Raw featured_image URLs/paths are refused — ingest with media_upload first, then place by media_id (GitHub #184). Pass featured_image empty/null to clear. A superseded image this post previously owned is cleaned up from disk automatically once the change is saved.  dry_run (default true) previews the fields that would change without writing — call once with dry_run&#x3D;true and review before dry_run&#x3D;false. |


## `abandonedCartRecoveryManage()`

```php
abandonedCartRecoveryManage($abandoned_cart_recovery_manage_request): \ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManage200Response
```

Show or configure one-time abandoned-cart recovery emails for a tenant. Eligible carts are sent to an account or guest email captured during checkout; carts must be idle for the configured delay and not already recovered. Sending also requires complete tenant SMTP settings. This tool does not send immediately.  ACTIONS:   show (default): current enabled state and delay.   set: pass enabled and/or delay_hours. Omitted values are preserved.   dry_run defaults true; pass dry_run=false to save.

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
$abandoned_cart_recovery_manage_request = new \ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManageRequest(); // \ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManageRequest

try {
    $result = $apiInstance->abandonedCartRecoveryManage($abandoned_cart_recovery_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->abandonedCartRecoveryManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **abandoned_cart_recovery_manage_request** | [**\ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManageRequest**](../Model/AbandonedCartRecoveryManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\AbandonedCartRecoveryManage200Response**](../Model/AbandonedCartRecoveryManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `blogImport()`

```php
blogImport($blog_import_request): \ShadowSoftware\DabDash\Model\BlogImport200Response
```

Create many blog posts (and the storefront's legal pages) in one call. tenant_blog_upsert can only edit an existing post; this creates new ones.  Pass posts with title and body (HTML), and optionally slug, excerpt, category, tags, meta_title, meta_description, published (default true), published_at (ISO date) and image_url (downloaded into the media library and used as the featured image). Posts are matched by slug within the store: existing ones are skipped unless update_existing=true, in which case only the fields you pass are changed.  PRIVACY: a privacy-policy post remains at its own /blog/{slug} URL. When it is the store's policy, set that exact published URL with store_settings_manage privacy_url. Terms and refund remain available at their storefront policy URLs.  dry_run defaults true: the first call reports what would be created, updated or skipped. Pass dry_run=false to write. Image failures never block a post.

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
$blog_import_request = new \ShadowSoftware\DabDash\Model\BlogImportRequest(); // \ShadowSoftware\DabDash\Model\BlogImportRequest

try {
    $result = $apiInstance->blogImport($blog_import_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->blogImport: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **blog_import_request** | [**\ShadowSoftware\DabDash\Model\BlogImportRequest**](../Model/BlogImportRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BlogImport200Response**](../Model/BlogImport200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignApplyTemplate()`

```php
campaignApplyTemplate($campaign_apply_template_request): \ShadowSoftware\DabDash\Model\CampaignApplyTemplate200Response
```

Apply a built-in system email template to a DRAFT campaign, replacing its html_body with the rendered, tenant-branded design. This mirrors the \"Choose template\" action in the vendor admin.  The template is rendered with the tenant's own branding (theme colour, logo, name, address, phone), then sanitized and written to the campaign's html_body. The {{unsubscribe_url}} token is preserved. The design ships with placeholder copy ([Product name], $00, \"Your headline here\", etc.) — after applying, call campaign_get, then campaign_upsert to swap placeholder copy/prices inside the existing html_body (do NOT replace the whole body — that deletes the template layout). Use campaign_set_image for product photos. Or hand off to the vendor to fill in.  Only DRAFT campaigns can have a template applied. To discover valid template_id values, omit template_id (or pass an unknown one) and the tool returns the list of available templates.  Typical flow: campaign_upsert (create draft) → campaign_apply_template (lay down the design) → campaign_get + campaign_upsert (edit copy in place) + campaign_set_image (photos) → campaign_send_test (preview).

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

## `campaignLaunch()`

```php
campaignLaunch($campaign_launch_request): \ShadowSoftware\DabDash\Model\CampaignLaunch200Response
```

Start, schedule, or cancel a vendor campaign after it is ready.  ACTIONS:   send_now — draft → materialize + send (or resume paused). Requires spam/inbox gate.   schedule — draft → scheduled_at (tenant timezone local datetime string).   cancel — sending/paused/recovering/scheduled/failed → cancelled.  dry_run defaults TRUE. Applying requires dry_run=false AND confirm=true. Always campaign_spam_score / campaign_send_test first, then confirm with the vendor.

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
$campaign_launch_request = new \ShadowSoftware\DabDash\Model\CampaignLaunchRequest(); // \ShadowSoftware\DabDash\Model\CampaignLaunchRequest

try {
    $result = $apiInstance->campaignLaunch($campaign_launch_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignLaunch: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_launch_request** | [**\ShadowSoftware\DabDash\Model\CampaignLaunchRequest**](../Model/CampaignLaunchRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignLaunch200Response**](../Model/CampaignLaunch200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignLinkAudit()`

```php
campaignLinkAudit($campaign_link_audit_request): \ShadowSoftware\DabDash\Model\CampaignLinkAudit200Response
```

Audit every href in a draft campaign's HTML against the tenant catalog.  For /products/{slug} and /categories/{slug} links it resolves the row (including draft/archived products) and reports status, stock, and live price. A link that HTTP-200s to the wrong or unpublished product is still a blocking issue — do not rely on curl alone. Optional check_http=true also probes the live URL.  Returns links[] plus blocking_issues[] so launch prep can refuse send until product deep links are public and correct.

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
$campaign_link_audit_request = new \ShadowSoftware\DabDash\Model\CampaignLinkAuditRequest(); // \ShadowSoftware\DabDash\Model\CampaignLinkAuditRequest

try {
    $result = $apiInstance->campaignLinkAudit($campaign_link_audit_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignLinkAudit: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_link_audit_request** | [**\ShadowSoftware\DabDash\Model\CampaignLinkAuditRequest**](../Model/CampaignLinkAuditRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignLinkAudit200Response**](../Model/CampaignLinkAudit200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignList()`

```php
campaignList($campaign_list_request): \ShadowSoftware\DabDash\Model\CampaignList200Response
```

List recent vendor email/SMS campaigns for a tenant so you can find campaign_id without guessing. Filter by status, channel, or a name/subject substring (q). Returns transport mode, audience flags, and list counts — use campaign_get for the full HTML/SMS body.

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
$campaign_list_request = new \ShadowSoftware\DabDash\Model\CampaignListRequest(); // \ShadowSoftware\DabDash\Model\CampaignListRequest

try {
    $result = $apiInstance->campaignList($campaign_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_list_request** | [**\ShadowSoftware\DabDash\Model\CampaignListRequest**](../Model/CampaignListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignList200Response**](../Model/CampaignList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `campaignPreviewImage()`

```php
campaignPreviewImage($campaign_preview_image_request): \ShadowSoftware\DabDash\Model\CampaignPreviewImage200Response
```

Render a draft email campaign as a PNG screenshot so you (and the vendor) can see exactly how it looks — desktop (600px) or mobile (390px).  Call this AFTER you finish editing an email (template apply, copy upsert, flyer/image placement) to visually confirm your own work before declaring the campaign ready. Prefer desktop first; also call with viewport=mobile when the send matters on phones or when the design uses media queries.  When the connected model supports vision input, the response includes vision_review: either \"LOOKS GOOD\" or a short list of concrete layout problems. Fix those problems with campaign_upsert / campaign_set_image and re-check. When vision is unavailable, image_url is still returned for the vendor chat preview — say you cannot visually confirm on this model.  Pass campaign_id (loads stored html_body) OR inline html_body for unsaved markup. SMS campaigns are rejected.

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
$campaign_preview_image_request = new \ShadowSoftware\DabDash\Model\CampaignPreviewImageRequest(); // \ShadowSoftware\DabDash\Model\CampaignPreviewImageRequest

try {
    $result = $apiInstance->campaignPreviewImage($campaign_preview_image_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->campaignPreviewImage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **campaign_preview_image_request** | [**\ShadowSoftware\DabDash\Model\CampaignPreviewImageRequest**](../Model/CampaignPreviewImageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CampaignPreviewImage200Response**](../Model/CampaignPreviewImage200Response.md)

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

Send a single test copy of a campaign so the design and copy can be reviewed before the real send.  Email: routes by campaign mode —   smtp → tenant SMTP (requires complete SMTP settings)   mailgun → Mailgun Messages API via a Ready sending name on the campaign   webhook → posts a test payload to the campaign webhook (no inbox email) Pass to_email for smtp/mailgun. Webhook mode uses to_email only as the sample recipient identity in the payload.  Text (SMS): Twilio. Pass to_phone. Requires Twilio connected.  Preview only — does NOT start the campaign, touch the audience, or record stats.

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

Score vendor campaign copy for inbox risk (email HTML or SMS).  One score only: 0–100 (0 = spam, 100 = primary-inbox friendly). Live scoring uses first-party rules. Pass for_send=true to run the same deep filter check used on send/schedule and fold it into that single number (never a second score).  When for_send=true on email, the response also includes spamassassin_score and deep_check_rules[] (Postmark/SpamAssassin hits with name, score, description) plus findings[] entries with actionable hints — use these to fix blocked campaigns.  Vendors cannot send or schedule below the platform minimum (default 80). Aim for 80+ before handoff; 85+ is excellent.  Pass campaign_id (loads draft content) OR inline channel + content fields.

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

Create or edit a vendor email/SMS campaign (vendor → their customers; NOT platform drip-to-leads). Completed campaigns cannot be edited.  CREATE (no campaign_id): draft. Email needs name+subject (html_body optional — then campaign_apply_template). SMS needs name+sms_body and connected Twilio.  UPDATE (campaign_id): only passed fields change. Drafts accept all fields. Sending/paused accept content only (email: name/subject/html_body/plain_body/fallback_first_name; SMS: name/sms_body/fallback_first_name).  TRANSPORT (email): mode = smtp | webhook | mailgun.   mailgun: require Ready mailgun_domain_ids (from tenant_mailgun_manage show); optional   mailgun_pace drip|blast.   webhook: prefer saved_webhook_id, or inline webhook_url (+ optional headers/health). AUDIENCE: contact_list_ids (manual and/or Smart Lists) and/or audience_includes_customers (default true). Lead lists cannot use smtp — use mailgun or webhook. SMS may attach Smart Lists only (not imported manual lists). Discover lists with contact_list_manage / smart_list_manage.  Optional: throttle_per_minute/hour, send_interval_minutes (1–30), send_window_*.  Does not start a live send — use campaign_send_test, then campaign_launch (confirm) or the vendor admin Send button.  ORDERING: html_body is a full replace. Finalize copy before campaign_generate_flyer / campaign_set_image, or later html_body upserts erase placed images.

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

## `contactListManage()`

```php
contactListManage($contact_list_manage_request): \ShadowSoftware\DabDash\Model\ContactListManage200Response
```

Discover and manage contact lists (manual + Smart) for campaigns, coupons, and Klaviyo.  ACTIONS:   list (default): all lists with id, name, kind (manual|smart), default_type, contacts_count,     usable_for hints (email/sms/coupon/klaviyo).   show: one list by list_id (includes Smart List definition when kind=smart).   create_manual: create an empty imported/manual list (name required; default_type lead|customer).   rename: update name/description/default_type on a manual list (not Smart — use smart_list_manage).   delete: remove a list (dry_run default true; applying needs dry_run=false + confirm=true).  CSV import stays in the vendor admin. Use smart_list_manage for Smart List CRUD/preview.

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
$contact_list_manage_request = new \ShadowSoftware\DabDash\Model\ContactListManageRequest(); // \ShadowSoftware\DabDash\Model\ContactListManageRequest

try {
    $result = $apiInstance->contactListManage($contact_list_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->contactListManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **contact_list_manage_request** | [**\ShadowSoftware\DabDash\Model\ContactListManageRequest**](../Model/ContactListManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ContactListManage200Response**](../Model/ContactListManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `notificationSettingsManage()`

```php
notificationSettingsManage($notification_settings_manage_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Show or configure tenant-wide vendor order, low-stock, digest, restock, and storefront enquiry notification preferences. This tool deliberately excludes per-user opt-outs, SMS provider configuration, and WhatsApp. Set uses dry_run=true by default and writes are audited.

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
$notification_settings_manage_request = new \ShadowSoftware\DabDash\Model\NotificationSettingsManageRequest(); // \ShadowSoftware\DabDash\Model\NotificationSettingsManageRequest

try {
    $result = $apiInstance->notificationSettingsManage($notification_settings_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->notificationSettingsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **notification_settings_manage_request** | [**\ShadowSoftware\DabDash\Model\NotificationSettingsManageRequest**](../Model/NotificationSettingsManageRequest.md)|  | [optional] |

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

## `reviewsManage()`

```php
reviewsManage($reviews_manage_request): \ShadowSoftware\DabDash\Model\ReviewsManage200Response
```

Show or configure verified-purchase product reviews for a tenant, and preview or apply moderation changes to one review. Customers can review products only after a delivered order. Hidden reviews are excluded from the product rating.  ACTIONS:   show (default): current review setting and moderation counts.   set: pass enabled to turn review collection on or off. dry_run defaults true.   hide or unhide: pass review_id. dry_run defaults true; applying a moderation   change also recalculates that product's rating.

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
$reviews_manage_request = new \ShadowSoftware\DabDash\Model\ReviewsManageRequest(); // \ShadowSoftware\DabDash\Model\ReviewsManageRequest

try {
    $result = $apiInstance->reviewsManage($reviews_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->reviewsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **reviews_manage_request** | [**\ShadowSoftware\DabDash\Model\ReviewsManageRequest**](../Model/ReviewsManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ReviewsManage200Response**](../Model/ReviewsManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `seoMaxImageManage()`

```php
seoMaxImageManage($seo_max_image_manage_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Manage the SEO Max product-image rollout without Artisan commands or seed data. Use action=status first. action=configure toggles AVIF product delivery and the AVIF-only image sitemap. action=backfill processes active products in bounded, resumable batches; dry_run=true only reports work. Repeat with next_cursor as after_id until it is null. action=submit notifies Google Search Console and IndexNow after the sitemap is live.

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
$seo_max_image_manage_request = new \ShadowSoftware\DabDash\Model\SeoMaxImageManageRequest(); // \ShadowSoftware\DabDash\Model\SeoMaxImageManageRequest

try {
    $result = $apiInstance->seoMaxImageManage($seo_max_image_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->seoMaxImageManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **seo_max_image_manage_request** | [**\ShadowSoftware\DabDash\Model\SeoMaxImageManageRequest**](../Model/SeoMaxImageManageRequest.md)|  | [optional] |

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

## `seoSettingsManage()`

```php
seoSettingsManage($seo_settings_manage_request): \ShadowSoftware\DabDash\Model\SeoSettingsManage200Response
```

Show or update a tenant's standard SEO and SEO MAX settings. Standard fields control homepage metadata, title templates, sitemap, Open Graph, and Twitter metadata. SEO MAX fields control automated blog targeting and publishing, plus image delivery flags.  ACTIONS:   show (default): current values, including the stored featured-image canvas path.   set: pass only fields to change. dry_run defaults true; pass dry_run=false to save. Enabling SEO MAX requires an active SEO MAX subscription or trial and complete schedule settings. Featured-image canvas uploads are handled by media tools and are read-only here.

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
$seo_settings_manage_request = new \ShadowSoftware\DabDash\Model\SeoSettingsManageRequest(); // \ShadowSoftware\DabDash\Model\SeoSettingsManageRequest

try {
    $result = $apiInstance->seoSettingsManage($seo_settings_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->seoSettingsManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **seo_settings_manage_request** | [**\ShadowSoftware\DabDash\Model\SeoSettingsManageRequest**](../Model/SeoSettingsManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\SeoSettingsManage200Response**](../Model/SeoSettingsManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `smartListManage()`

```php
smartListManage($smart_list_manage_request): \ShadowSoftware\DabDash\Model\SmartListManage200Response
```

Create, edit, preview, or delete Smart Lists (dynamic customer audiences).  ACTIONS:   list / show — discover Smart Lists (also available via contact_list_manage).   upsert — create (omit list_id) or update. Requires name + definition     {version:1, match:\"all\"|\"any\", conditions:[...]}.     Rule types: purchased_category, recent_category_buyers, lapsed_category_buyers,     not_purchased_category, purchased_product, purchase_count, spend_total,     top_spenders, dormant_customer, loyalty_lifetime_points, loyalty_tier.   preview — count matching customers for a definition (or existing list_id).   delete — dry_run default true; applying needs dry_run=false + confirm=true.  Attach Smart Lists to campaigns with campaign_upsert.contact_list_ids or to coupons via coupon_upsert.contact_list_id. Project to Klaviyo with tenant_klaviyo_manage.

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
$smart_list_manage_request = new \ShadowSoftware\DabDash\Model\SmartListManageRequest(); // \ShadowSoftware\DabDash\Model\SmartListManageRequest

try {
    $result = $apiInstance->smartListManage($smart_list_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->smartListManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **smart_list_manage_request** | [**\ShadowSoftware\DabDash\Model\SmartListManageRequest**](../Model/SmartListManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\SmartListManage200Response**](../Model/SmartListManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `suppressionManage()`

```php
suppressionManage($suppression_manage_request): \ShadowSoftware\DabDash\Model\SuppressionManage200Response
```

Inspect or lift email suppressions / campaign unsubscribes for a tenant.  ACTIONS:   list — recent suppressed customers (email_opt_out) and unsubscribed contacts.   lift_transactional — clear customer email_opt_out for an email.   resubscribe — restore contact.subscribed for campaign lists.   lift_all — both transactional + campaign for one email.  Mutating actions: dry_run default true; apply needs dry_run=false + confirm=true.

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
$suppression_manage_request = new \ShadowSoftware\DabDash\Model\SuppressionManageRequest(); // \ShadowSoftware\DabDash\Model\SuppressionManageRequest

try {
    $result = $apiInstance->suppressionManage($suppression_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->suppressionManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **suppression_manage_request** | [**\ShadowSoftware\DabDash\Model\SuppressionManageRequest**](../Model/SuppressionManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\SuppressionManage200Response**](../Model/SuppressionManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `tenantBlogUpsert()`

```php
tenantBlogUpsert($tenant_blog_upsert_request): \ShadowSoftware\DabDash\Model\ContactCleanup200Response
```

Update an existing tenant (vendor) blog post's fields — title, excerpt, body, category, featured_image, meta_title, meta_description, og_image, or is_published. Only the fields you pass are changed. Use tenant_blog_lookup first to find the post_id or slug.  IMAGE: pass media_id (from media_list / media_upload) to set featured_image from a tenant media-library asset. Raw featured_image URLs/paths are refused — ingest with media_upload first, then place by media_id (GitHub #184). Pass featured_image empty/null to clear. A superseded image this post previously owned is cleaned up from disk automatically once the change is saved.  dry_run (default true) previews the fields that would change without writing — call once with dry_run=true and review before dry_run=false.

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
$tenant_blog_upsert_request = new \ShadowSoftware\DabDash\Model\TenantBlogUpsertRequest(); // \ShadowSoftware\DabDash\Model\TenantBlogUpsertRequest

try {
    $result = $apiInstance->tenantBlogUpsert($tenant_blog_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MarketingApi->tenantBlogUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tenant_blog_upsert_request** | [**\ShadowSoftware\DabDash\Model\TenantBlogUpsertRequest**](../Model/TenantBlogUpsertRequest.md)|  | [optional] |

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
