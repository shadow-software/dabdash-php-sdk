# ShadowSoftware\DabDash\ReadApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**analyticsQuery()**](ReadApi.md#analyticsQuery) | **POST** /api/v1/tools/analytics_query | Run read-only analytics queries against the production database. Available reports: revenue_by_tenant, orders_by_status, top_products, revenue_over_time, customer_stats. Revenue dating uses RevenueAttribution (pass tenant_slug for delivered-mode tenants). |
| [**campaignAudienceInspect()**](ReadApi.md#campaignAudienceInspect) | **POST** /api/v1/tools/campaign_audience_inspect | Split the tenant&#39;s campaign audience into warm (≥1 past order) vs cold (no order history) recipients. Use this before drafting a newsletter or SMS in the Create Promotion flow: warm audiences can get exclusive codes and commercial copy; cold audiences need personalized, spam-safe language — never hard-sell or aggressive exclusive deals.  Returns counts only — it does not create or send a campaign. |
| [**campaignGet()**](ReadApi.md#campaignGet) | **POST** /api/v1/tools/campaign_get | Read a campaign back, including the message body you are about to edit.  Every other campaign tool reports html_body_length — a character count — so without this you would be rewriting a document you have never seen. Call this first whenever you are asked to change, finish, or comment on existing copy.  Email campaigns return html_body; text campaigns return sms_body. A rendered system template runs 10-32KB, so read once and edit from what you read rather than re-fetching between changes. |
| [**campaignSpamScore()**](ReadApi.md#campaignSpamScore) | **POST** /api/v1/tools/campaign_spam_score | Score vendor campaign copy for inbox risk (email HTML or SMS).  One score only: 0–100 (0 &#x3D; spam, 100 &#x3D; primary-inbox friendly). Live scoring uses first-party rules. Pass for_send&#x3D;true to run the same deep filter check used on send/schedule and fold it into that single number (never a second score).  Vendors cannot send or schedule below the platform minimum (default 80). Aim for 80+ before handoff; 85+ is excellent.  Pass campaign_id (loads draft content) OR inline channel + content fields. |
| [**catalogFlatteningAudit()**](ReadApi.md#catalogFlatteningAudit) | **POST** /api/v1/tools/catalog_flattening_audit | Read-only. Finds products whose sizes were split into separate products instead of tiers.  This happens when a catalog is imported from a store that put the size in the product NAME (\&quot;Blue Dream - 3.5G\&quot;, \&quot;Blue Dream - 7G\&quot;) instead of a size option column. The importer has no size column to read, so each size becomes its own product with a single \&quot;Default\&quot; option, and the store ends up with a long flat menu that cannot use weight pricing or mix &amp; match deals.  Returns each group of products that belong together (\&quot;family\&quot;), the sizes and prices found, and whether the group can be safely merged. A group is NOT mergeable when two of its products claim the same size — that must be resolved by hand first.  Nothing is changed. Use catalog_collapse to merge a group. |
| [**couponList()**](ReadApi.md#couponList) | **POST** /api/v1/tools/coupon_list | List a tenant&#39;s discount coupons with code, type, value, usage limits, redemption count, active state, and schedule window. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_list for those).  type:   percentage     → value is a percentage 0-100 off the order subtotal.   fixed          → value is dollars off the order subtotal.   free_delivery  → waives the delivery fee only; value is unused for this type.  Always call this before making coupon-related decisions to see current codes, usage caps, and whether a coupon has already been exhausted (used_count vs max_uses). |
| [**customerAddresses()**](ReadApi.md#customerAddresses) | **POST** /api/v1/tools/customer_addresses | Return a customer&#39;s saved addresses, coordinates, saved zones, and zone mismatch diagnostics. |
| [**customerList()**](ReadApi.md#customerList) | **POST** /api/v1/tools/customer_list | Page through all customers for a tenant, optionally filtered to those updated since a given time. Built for bulk sync — use customer_lookup instead for a single targeted search. |
| [**customerLookup()**](ReadApi.md#customerLookup) | **POST** /api/v1/tools/customer_lookup | Find customers by id, email, phone, or name and return their recent addresses, orders, and support context. |
| [**freebieList()**](ReadApi.md#freebieList) | **POST** /api/v1/tools/freebie_list | List a tenant&#39;s freebie rules (\&quot;spend $X, get a free item\&quot;) with id, name, spend threshold, the product/variation given away, quantity, stackable flag, active state, and schedule window. Freebies are evaluated on every cart change by FreebieService: a rule fires once its spend_threshold is met, adding &#x60;quantity&#x60; of the configured product/variation to the cart.  IMPORTANT: this reads the &#x60;freebies&#x60; table — the source of truth the storefront cart uses. It is NOT the legacy freebie summary promotion_audit surfaces alongside coupons/bundles; that view is stale display-only data built for a different purpose. Trust this tool for what actually applies at checkout.  is_merch_product: true when the freebie&#39;s product is tagged \&quot;merch\&quot; (Product.tags contains \&quot;merch\&quot;) — a branded giveaway item (hats, apparel) rather than sellable cannabis inventory. Prefer merch-tagged products over high-COGS flower when recommending a NEW freebie: giving away promo materials protects margin the same way a discount code doesn&#39;t.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order (each            rule still only fires once, at its configured quantity, regardless of how far above            its own threshold the cart is).   false  → this rule cannot combine with other freebie rules; if multiple non-stackable rules            qualify, FreebieService applies its own precedence to pick one.   This flag does NOT multiply a single rule&#39;s quantity by how many multiples of the threshold   the cart reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3. |
| [**googleAnalytics()**](ReadApi.md#googleAnalytics) | **POST** /api/v1/tools/google_analytics | Query Google Analytics (GA4) data for the platform (dabdash.com) or a specific tenant with a connected GA integration. Returns traffic overview, top pages, traffic sources, top events, and daily trend. |
| [**inventoryAuditLookup()**](ReadApi.md#inventoryAuditLookup) | **POST** /api/v1/tools/inventory_audit_lookup | Look up the historical inventory state of a list of products from the inventory_audit_logs table.  For each product, returns every variation_id ever logged in the audit trail (including variations that have since been deleted), the variation&#39;s last known stock_quantity strictly BEFORE the given cutoff timestamp, and that variation&#39;s most recent action+notes for context.  Use this tool to recover pre-incident stock values when variations have been overwritten or deleted by a destructive operation (e.g. an erroneous pricing structure assignment that wiped unit/simple variations and replaced them with weight tiers).  Output is grouped per product. Each product also includes its CURRENT variations and their stock for comparison so you can see the delta. |
| [**inventoryStatus()**](ReadApi.md#inventoryStatus) | **POST** /api/v1/tools/inventory_status | Get inventory status across all tenants or a specific tenant. Shows low stock alerts and out-of-stock products. |
| [**mailboxInspect()**](ReadApi.md#mailboxInspect) | **POST** /api/v1/tools/mailbox_inspect | Inspect a tenant inbound mailbox: sync watermark, last error, recent ingestion counts (inbound/outbound), and a healthy/bootstrap/stalled/quiet verdict. Pass a tenant_slug, or pass platform&#x3D;true for a platform-owned mailbox (tenant_id IS NULL). |
| [**metrcDiagnostics()**](ReadApi.md#metrcDiagnostics) | **POST** /api/v1/tools/metrc_diagnostics | Returns a JSON summary of Metrc compliance status for a tenant: integration mode, sync states, audit log counts by HTTP status, and pending/failed report counts. Pass a tenant_slug to inspect a specific tenant. |
| [**orderDashboard()**](ReadApi.md#orderDashboard) | **POST** /api/v1/tools/order_dashboard | Query orders across all tenants. Filter by status, order number, customer clues, date range, amount, or tenant. Returns order list with pricing context. |
| [**productImageStrainMatch()**](ReadApi.md#productImageStrainMatch) | **POST** /api/v1/tools/product_image_strain_match | Find products on a tenant&#39;s storefront that have no featured image, and propose a hosted platform strain photo for each by matching product name against the strain library. Read-only — never writes to a product; pass the results to product_image_strain_assign to actually apply them.  Only proposes strains whose photo is already hosted on the platform (cdn.strains.dabdash.com/strains/... — see StrainImageService::isHostedUrl). A name match against an unhosted/dead-remote strain is reported as match_method&#x3D;none rather than proposing a broken or third-party hotlinked image.  Match order per product: 1) the product&#39;s own strain_id FK, if set (match_method&#x3D;strain_id_fk, confidence&#x3D;exact) 2) case-insensitive exact name match (confidence&#x3D;exact) 3) prefix/contains name match, shortest strain name wins ties (confidence&#x3D;partial) 4) no match (match_method&#x3D;none, confidence&#x3D;none). |
| [**productInspect()**](ReadApi.md#productInspect) | **POST** /api/v1/tools/product_inspect | Inspect a specific product including every variation&#39;s price, compare_at_price, mix_match_tags, stock, and the tenant&#39;s mix &amp; match rule settings. Use this to audit pricing, sale state, and bundle configuration for support tickets. |
| [**productProfitability()**](ReadApi.md#productProfitability) | **POST** /api/v1/tools/product_profitability | Rank a tenant&#39;s products by real net margin using order-line COGS (order_items.cost_price), not price-tier approximations. Use this before recommending sales, coupons, freebies, or subscription mounting ladders — only promote SKUs with enough margin headroom.  Revenue dating follows RevenueAttribution (placed vs delivered) for the tenant. Freebie gift lines are excluded from COGS so giveaways do not distort product margins. Results include current catalog stock_status and a promo_headroom_ok flag (margin_percent &gt;&#x3D; min_margin_percent).  Sort: margin (default), revenue, or units. Pass a wide date_from for tenants with older imported history. |
| [**productUnitPriceSearch()**](ReadApi.md#productUnitPriceSearch) | **POST** /api/v1/tools/product_unit_price_search | Search and rank a tenant&#39;s catalog by computed per-unit price (e.g. price per gram or price per ounce), across every weight-family product (types \&quot;weight\&quot; and \&quot;matrix\&quot;) — something product_inspect cannot do because it only looks up one product at a time by id/name/sku.  For each active, in-stock variation with a weight_value, computes price_per_gram &#x3D; price_cents / weight_value, then scales it to the requested unit (default \&quot;oz\&quot; &#x3D; 28g, matching the storefront&#39;s weight-tier convention). Use this to answer \&quot;what&#39;s the cheapest/most expensive product per ounce\&quot;, \&quot;find products under $X/g\&quot;, or to rank the catalog by unit economics for pricing audits and promo targeting.  Only weight-family products (weight, matrix) have a meaningful per-gram price — unit-family products (simple, unit, matrix_unit) are excluded since their variations are priced per item, not per weight. |
| [**promotionAudit()**](ReadApi.md#promotionAudit) | **POST** /api/v1/tools/promotion_audit | Inspect coupons, freebies, mix and match rules, loyalty settings, and storewide sale state for overcharge or missed-discount support tickets. |
| [**pushNotificationDiagnostics()**](ReadApi.md#pushNotificationDiagnostics) | **POST** /api/v1/tools/push_notification_diagnostics | Diagnose push notification (FCM) delivery for a vendor. Surfaces token health, notification settings, recent send history with push/email flags, and a plain-language diagnosis of why pushes are or are not being delivered. |
| [**searchConsole()**](ReadApi.md#searchConsole) | **POST** /api/v1/tools/search_console | Query Google Search Console data for the platform (dabdash.com) or a specific tenant with a connected GSC integration. Returns search overview, top queries, top pages, and daily trend. |
| [**storeInfo()**](ReadApi.md#storeInfo) | **POST** /api/v1/tools/store_info | Identify the connected store — name, slug, timezone, currency, country, and subscription status. Use to validate an API token during setup. |
| [**strainLookup()**](ReadApi.md#strainLookup) | **POST** /api/v1/tools/strain_lookup | Search the platform strain database — the same catalog vendors search on the create-product page. Returns name, type, cannabinoids, effects, flavors, and whether a hosted photo exists. Use this BEFORE creating a product whose name looks like a strain, then pass strain_id to product_manage so the product is filled from that row. Not tenant-owned media; photos stay on the shared strain CDN. |
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


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
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
    echo 'Exception when calling ReadApi->campaignAudienceInspect: ', $e->getMessage(), PHP_EOL;
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


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
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
    echo 'Exception when calling ReadApi->campaignGet: ', $e->getMessage(), PHP_EOL;
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


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
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
    echo 'Exception when calling ReadApi->campaignSpamScore: ', $e->getMessage(), PHP_EOL;
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

List a tenant's freebie rules (\"spend $X, get a free item\") with id, name, spend threshold, the product/variation given away, quantity, stackable flag, active state, and schedule window. Freebies are evaluated on every cart change by FreebieService: a rule fires once its spend_threshold is met, adding `quantity` of the configured product/variation to the cart.  IMPORTANT: this reads the `freebies` table — the source of truth the storefront cart uses. It is NOT the legacy freebie summary promotion_audit surfaces alongside coupons/bundles; that view is stale display-only data built for a different purpose. Trust this tool for what actually applies at checkout.  is_merch_product: true when the freebie's product is tagged \"merch\" (Product.tags contains \"merch\") — a branded giveaway item (hats, apparel) rather than sellable cannabis inventory. Prefer merch-tagged products over high-COGS flower when recommending a NEW freebie: giving away promo materials protects margin the same way a discount code doesn't.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order (each            rule still only fires once, at its configured quantity, regardless of how far above            its own threshold the cart is).   false  → this rule cannot combine with other freebie rules; if multiple non-stackable rules            qualify, FreebieService applies its own precedence to pick one.   This flag does NOT multiply a single rule's quantity by how many multiples of the threshold   the cart reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.

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

## `productImageStrainMatch()`

```php
productImageStrainMatch($product_image_strain_match_request): \ShadowSoftware\DabDash\Model\ProductImageStrainMatch200Response
```

Find products on a tenant's storefront that have no featured image, and propose a hosted platform strain photo for each by matching product name against the strain library. Read-only — never writes to a product; pass the results to product_image_strain_assign to actually apply them.  Only proposes strains whose photo is already hosted on the platform (cdn.strains.dabdash.com/strains/... — see StrainImageService::isHostedUrl). A name match against an unhosted/dead-remote strain is reported as match_method=none rather than proposing a broken or third-party hotlinked image.  Match order per product: 1) the product's own strain_id FK, if set (match_method=strain_id_fk, confidence=exact) 2) case-insensitive exact name match (confidence=exact) 3) prefix/contains name match, shortest strain name wins ties (confidence=partial) 4) no match (match_method=none, confidence=none).

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
$product_image_strain_match_request = new \ShadowSoftware\DabDash\Model\ProductImageStrainMatchRequest(); // \ShadowSoftware\DabDash\Model\ProductImageStrainMatchRequest

try {
    $result = $apiInstance->productImageStrainMatch($product_image_strain_match_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->productImageStrainMatch: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_image_strain_match_request** | [**\ShadowSoftware\DabDash\Model\ProductImageStrainMatchRequest**](../Model/ProductImageStrainMatchRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductImageStrainMatch200Response**](../Model/ProductImageStrainMatch200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\ReadApi(
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
    echo 'Exception when calling ReadApi->productProfitability: ', $e->getMessage(), PHP_EOL;
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

## `productUnitPriceSearch()`

```php
productUnitPriceSearch($product_unit_price_search_request): \ShadowSoftware\DabDash\Model\ProductUnitPriceSearch200Response
```

Search and rank a tenant's catalog by computed per-unit price (e.g. price per gram or price per ounce), across every weight-family product (types \"weight\" and \"matrix\") — something product_inspect cannot do because it only looks up one product at a time by id/name/sku.  For each active, in-stock variation with a weight_value, computes price_per_gram = price_cents / weight_value, then scales it to the requested unit (default \"oz\" = 28g, matching the storefront's weight-tier convention). Use this to answer \"what's the cheapest/most expensive product per ounce\", \"find products under $X/g\", or to rank the catalog by unit economics for pricing audits and promo targeting.  Only weight-family products (weight, matrix) have a meaningful per-gram price — unit-family products (simple, unit, matrix_unit) are excluded since their variations are priced per item, not per weight.

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
$product_unit_price_search_request = new \ShadowSoftware\DabDash\Model\ProductUnitPriceSearchRequest(); // \ShadowSoftware\DabDash\Model\ProductUnitPriceSearchRequest

try {
    $result = $apiInstance->productUnitPriceSearch($product_unit_price_search_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->productUnitPriceSearch: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_unit_price_search_request** | [**\ShadowSoftware\DabDash\Model\ProductUnitPriceSearchRequest**](../Model/ProductUnitPriceSearchRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductUnitPriceSearch200Response**](../Model/ProductUnitPriceSearch200Response.md)

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

## `strainLookup()`

```php
strainLookup($strain_lookup_request): \ShadowSoftware\DabDash\Model\StrainLookup200Response
```

Search the platform strain database — the same catalog vendors search on the create-product page. Returns name, type, cannabinoids, effects, flavors, and whether a hosted photo exists. Use this BEFORE creating a product whose name looks like a strain, then pass strain_id to product_manage so the product is filled from that row. Not tenant-owned media; photos stay on the shared strain CDN.

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
$strain_lookup_request = new \ShadowSoftware\DabDash\Model\StrainLookupRequest(); // \ShadowSoftware\DabDash\Model\StrainLookupRequest

try {
    $result = $apiInstance->strainLookup($strain_lookup_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ReadApi->strainLookup: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **strain_lookup_request** | [**\ShadowSoftware\DabDash\Model\StrainLookupRequest**](../Model/StrainLookupRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\StrainLookup200Response**](../Model/StrainLookup200Response.md)

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
