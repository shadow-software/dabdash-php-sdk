# ShadowSoftware\DabDash\PromotionsApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**bundleList()**](PromotionsApi.md#bundleList) | **POST** /api/v1/tools/bundle_list | List a tenant&#39;s bundle deals (\&quot;mix &amp; match\&quot; — e.g. \&quot;buy 4 for $77\&quot;) with id, name, trigger quantity, discount type/value, active state, schedule window, and attached variation count. Bundles are the live cart engine (MixMatchService): a bundle fires when a cart holds at least &#x60;quantity&#x60; units across its attached variations. Always call this before bundle_upsert to get the bundle id and confirm the current discount configuration.  IMPORTANT: this reads the &#x60;bundles&#x60; table — the source of truth the storefront cart uses. It is NOT the legacy &#x60;mix_match_rules&#x60; tenant setting that promotion_audit / product_inspect surface; those are stale display-only data. Trust this tool for what actually applies at checkout.  discount_type:   percent      → discount_value is a percentage 0–100, applied per unit.   fixed        → discount_value is dollars off PER UNIT.   fixed_total  → discount_value is the dollar TOTAL for the whole set (\&quot;$77 for 4\&quot;). |
| [**bundleUpsert()**](PromotionsApi.md#bundleUpsert) | **POST** /api/v1/tools/bundle_upsert | Create or update a bundle deal (mix &amp; match) on behalf of a tenant. Bundles are the live cart engine: a bundle fires when a cart holds at least &#x60;quantity&#x60; units across its attached variations.  UPDATE MODE (bundle_id provided):   Edits the bundle. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no bundle_id):   Creates a new bundle. name, quantity, discount_type, and discount_value are required.  DISCOUNT VALUE UNITS — read carefully, this is the common mistake:   discount_type &#x3D; \&quot;percent\&quot;      → discount_value is a percentage 0–100 (e.g. 20 &#x3D; 20% off each unit).   discount_type &#x3D; \&quot;fixed\&quot;        → discount_value is DOLLARS off PER UNIT (e.g. 5 &#x3D; $5 off each).   discount_type &#x3D; \&quot;fixed_total\&quot;  → discount_value is the DOLLARS TOTAL for the whole set                                     (e.g. quantity&#x3D;4, discount_value&#x3D;77 → \&quot;any 4 for $77\&quot;).   For fixed and fixed_total, pass dollars (e.g. 77 or 77.00) — the tool stores cents internally.   For percent, pass the percentage (e.g. 20), NOT a fraction.  VARIATIONS:   variation_ids + variation_mode control which product variations the bundle applies to.   mode \&quot;replace\&quot; (default) sets membership to exactly variation_ids; \&quot;add\&quot; attaches them to the   existing set; \&quot;detach\&quot; removes them. Variations not owned by the tenant are ignored.   Omit variation_ids entirely to leave membership untouched.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass null/omit   for an always-on bundle.  Always call bundle_list first to get the bundle_id and confirm the current configuration. |
| [**couponList()**](PromotionsApi.md#couponList) | **POST** /api/v1/tools/coupon_list | List a tenant&#39;s discount coupons with code, type, value, usage limits, redemption count, active state, and schedule window. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_list for those).  type:   percentage     → value is a percentage 0-100 off the order subtotal.   fixed          → value is dollars off the order subtotal.   free_delivery  → waives the delivery fee only; value is unused for this type.  Always call this before making coupon-related decisions to see current codes, usage caps, and whether a coupon has already been exhausted (used_count vs max_uses). |
| [**couponUpsert()**](PromotionsApi.md#couponUpsert) | **POST** /api/v1/tools/coupon_upsert | Create or update a discount coupon on behalf of a tenant. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_upsert for those).  UPDATE MODE (coupon_id provided):   Edits the coupon. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no coupon_id):   Creates a new coupon. code, type, and value are required.  VALUE UNITS:   type &#x3D; \&quot;percentage\&quot;     → value is a percentage 0-100 off the order subtotal.   type &#x3D; \&quot;fixed\&quot;          → value is DOLLARS off the order subtotal (e.g. 10 &#x3D; $10 off).   type &#x3D; \&quot;free_delivery\&quot;  → value is ignored (pass 0); this type only waives the delivery fee.  min_order is a dollar minimum order subtotal required to use the coupon (pass dollars, e.g. 25 for a $25 minimum — the tool stores cents internally). Omit or pass 0 for no minimum.  freebie_id links this coupon to an existing Freebie (see freebie_list/freebie_upsert), turning it into a \&quot;code-triggered freebie\&quot;: a customer who applies the code while below min_order has the code parked (kept attached, not rejected) and sees cart progress toward unlocking it, instead of the code being discarded. Pass null to unlink. The freebie must belong to the same tenant.  limit_match_by (\&quot;email\&quot;|\&quot;phone\&quot;|\&quot;both\&quot;) controls how max_uses_per_customer is enforced. Using \&quot;phone\&quot; or \&quot;both\&quot; REQUIRES the tenant&#39;s \&quot;Require phone at checkout\&quot; setting to be on — otherwise the update is rejected, since customers without a phone on file could otherwise reuse the coupon past its per-customer limit.  SCHEDULE:   starts_at / expires_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass   null/omit for an always-on coupon.  PRODUCT/CATEGORY SCOPING:   applies_to&#x3D;\&quot;products\&quot; or \&quot;categories\&quot; restricts the discount to matching cart lines only —   checkout math is fully scope-aware (a scoped coupon never discounts the whole cart). Pass   applies_to_ids as an array of product ids (when applies_to&#x3D;\&quot;products\&quot;) or category ids (when   applies_to&#x3D;\&quot;categories\&quot;) — all ids must belong to this tenant. Passing applies_to&#x3D;\&quot;products\&quot;/   \&quot;categories\&quot; with an empty or omitted applies_to_ids behaves the same as \&quot;all\&quot; (no scope   configured yet). Switching back to applies_to&#x3D;\&quot;all\&quot; does not automatically clear a   previously-set applies_to_ids — pass applies_to_ids&#x3D;[] explicitly to clear it.  SUBSCRIPTION MOUNTING DISCOUNT LADDERS (subscribe-and-save retention):   A coupon can carry a \&quot;mounting ladder\&quot; so its discount climbs with each successive order a   customer&#39;s delivery subscription generates, pegging at a ceiling — e.g. order 1 &#x3D; 0% off,   then +5% every order up to a 20% cap. This only affects orders generated for a subscription   linked to this coupon (DeliverySubscription.coupon_id) — it has no effect on ordinary   one-off checkout use of the code, which still uses type/value as normal.    subscription_ladder_id: link to an existing ladder (from another coupon) by id, or pass null   to unlink. Must belong to the same tenant.    ladder_start_percent / ladder_step_percent / ladder_cap_percent / ladder_start_order_index:   pass any of these to CREATE a new ladder inline (on coupon create) or EDIT the tiers of the   ladder already linked to this coupon (on update) — do not combine with subscription_ladder_id   in the same call. All four are whole-number percentages (0-100) except start_order_index,   which is the 1-based order number the climb begins at (orders before it stay at   ladder_start_percent). Omitted ladder_* fields default to 0% start / 5% step / 20% cap /   order 1 on create; on update, only the fields you pass are changed.  Always call coupon_list first to get the coupon_id and confirm the current configuration. |
| [**freebieList()**](PromotionsApi.md#freebieList) | **POST** /api/v1/tools/freebie_list | List a tenant&#39;s freebie rules (\&quot;spend $X, get a free item\&quot;) with id, name, spend threshold, the product/variation given away, quantity, stackable flag, active state, and schedule window. Freebies are evaluated on every cart change by FreebieService: a rule fires once its spend_threshold is met, adding &#x60;quantity&#x60; of the configured product/variation to the cart.  IMPORTANT: this reads the &#x60;freebies&#x60; table — the source of truth the storefront cart uses. It is NOT the legacy freebie summary promotion_audit surfaces alongside coupons/bundles; that view is stale display-only data built for a different purpose. Trust this tool for what actually applies at checkout.  is_merch_product: true when the freebie&#39;s product is tagged \&quot;merch\&quot; (Product.tags contains \&quot;merch\&quot;) — a branded giveaway item (hats, apparel) rather than sellable cannabis inventory. Prefer merch-tagged products over high-COGS flower when recommending a NEW freebie: giving away promo materials protects margin the same way a discount code doesn&#39;t.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order (each            rule still only fires once, at its configured quantity, regardless of how far above            its own threshold the cart is).   false  → this rule cannot combine with other freebie rules; if multiple non-stackable rules            qualify, FreebieService applies its own precedence to pick one.   This flag does NOT multiply a single rule&#39;s quantity by how many multiples of the threshold   the cart reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3. |
| [**freebieUpsert()**](PromotionsApi.md#freebieUpsert) | **POST** /api/v1/tools/freebie_upsert | Create or update a freebie rule (\&quot;spend $X, get a free item\&quot;) on behalf of a tenant. Freebies are evaluated on every cart change: a rule fires once its spend_threshold is met, adding &#x60;quantity&#x60; of the configured product/variation to the cart (distinct from bundles, which fire on cart CONTENTS/quantity — use bundle_upsert for those).  UPDATE MODE (freebie_id provided):   Edits the freebie. Only the fields you pass are changed; omitted fields are left as-is,   EXCEPT category_ids, which — like bundle_upsert&#39;s variation_ids — fully replaces the category   set whenever passed (pass an empty array to clear all categories).  CREATE MODE (no freebie_id):   Creates a new freebie. name, product_id, spend_threshold, and quantity are required.  product_id and variation_id (if given) MUST belong to the same tenant — foreign ids are rejected, not silently ignored (unlike bundle_upsert&#39;s variation_ids, since a freebie needs exactly one product to give away, not a set).  spend_threshold is entered in DOLLARS (e.g. 50 for a $50 minimum spend) — the tool stores cents internally.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order.   false  → this rule cannot combine with other freebie rules.   Does NOT multiply this rule&#39;s own quantity by how many multiples of spend_threshold the cart   reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass   null/omit for an always-on freebie.  Always call freebie_list first to get the freebie_id and confirm the current configuration. |
| [**promotionAudit()**](PromotionsApi.md#promotionAudit) | **POST** /api/v1/tools/promotion_audit | Inspect coupons, freebies, mix and match rules, loyalty settings, and storewide sale state for overcharge or missed-discount support tickets. |
| [**widgetManage()**](PromotionsApi.md#widgetManage) | **POST** /api/v1/tools/widget_manage | List, create, update, or delete a tenant&#39;s homepage marketing widgets (the hero slider cards linking to a product, category, featured products, or a mix &amp; match tag).  ACTIONS:   list   (default): return every widget with id, title, subtitle, link_type, target, sort_order,          is_active, and image_url. Always call this first to find a widget_id.   create: requires title. link_type + its matching id/tag is optional but recommended so the          widget&#39;s CTA actually goes somewhere (see LINK_TYPE below). Defaults to \&quot;featured\&quot;          (no target) when omitted.   update: requires widget_id. Only the fields you pass are changed.   delete: requires widget_id and confirm&#x3D;true.  LINK_TYPE — pairs with exactly one target field:   \&quot;product\&quot;    → product_id   \&quot;category\&quot;   → category_id   \&quot;mix_match\&quot;  → mix_match_tag   \&quot;featured\&quot;   → no target needed (links to the featured-products listing)  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites the headline/logo overlay onto). Omit both to leave images untouched.  Always call action&#x3D;list first to confirm widget_id before update or delete. |


## `bundleList()`

```php
bundleList($bundle_list_request): \ShadowSoftware\DabDash\Model\BundleList200Response
```

List a tenant's bundle deals (\"mix & match\" — e.g. \"buy 4 for $77\") with id, name, trigger quantity, discount type/value, active state, schedule window, and attached variation count. Bundles are the live cart engine (MixMatchService): a bundle fires when a cart holds at least `quantity` units across its attached variations. Always call this before bundle_upsert to get the bundle id and confirm the current discount configuration.  IMPORTANT: this reads the `bundles` table — the source of truth the storefront cart uses. It is NOT the legacy `mix_match_rules` tenant setting that promotion_audit / product_inspect surface; those are stale display-only data. Trust this tool for what actually applies at checkout.  discount_type:   percent      → discount_value is a percentage 0–100, applied per unit.   fixed        → discount_value is dollars off PER UNIT.   fixed_total  → discount_value is the dollar TOTAL for the whole set (\"$77 for 4\").

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\PromotionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bundle_list_request = new \ShadowSoftware\DabDash\Model\BundleListRequest(); // \ShadowSoftware\DabDash\Model\BundleListRequest

try {
    $result = $apiInstance->bundleList($bundle_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PromotionsApi->bundleList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_list_request** | [**\ShadowSoftware\DabDash\Model\BundleListRequest**](../Model/BundleListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleList200Response**](../Model/BundleList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `bundleUpsert()`

```php
bundleUpsert($bundle_upsert_request): \ShadowSoftware\DabDash\Model\BundleUpsert200Response
```

Create or update a bundle deal (mix & match) on behalf of a tenant. Bundles are the live cart engine: a bundle fires when a cart holds at least `quantity` units across its attached variations.  UPDATE MODE (bundle_id provided):   Edits the bundle. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no bundle_id):   Creates a new bundle. name, quantity, discount_type, and discount_value are required.  DISCOUNT VALUE UNITS — read carefully, this is the common mistake:   discount_type = \"percent\"      → discount_value is a percentage 0–100 (e.g. 20 = 20% off each unit).   discount_type = \"fixed\"        → discount_value is DOLLARS off PER UNIT (e.g. 5 = $5 off each).   discount_type = \"fixed_total\"  → discount_value is the DOLLARS TOTAL for the whole set                                     (e.g. quantity=4, discount_value=77 → \"any 4 for $77\").   For fixed and fixed_total, pass dollars (e.g. 77 or 77.00) — the tool stores cents internally.   For percent, pass the percentage (e.g. 20), NOT a fraction.  VARIATIONS:   variation_ids + variation_mode control which product variations the bundle applies to.   mode \"replace\" (default) sets membership to exactly variation_ids; \"add\" attaches them to the   existing set; \"detach\" removes them. Variations not owned by the tenant are ignored.   Omit variation_ids entirely to leave membership untouched.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant's timezone and stored as UTC. Pass null/omit   for an always-on bundle.  Always call bundle_list first to get the bundle_id and confirm the current configuration.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\PromotionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$bundle_upsert_request = new \ShadowSoftware\DabDash\Model\BundleUpsertRequest(); // \ShadowSoftware\DabDash\Model\BundleUpsertRequest

try {
    $result = $apiInstance->bundleUpsert($bundle_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PromotionsApi->bundleUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_upsert_request** | [**\ShadowSoftware\DabDash\Model\BundleUpsertRequest**](../Model/BundleUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleUpsert200Response**](../Model/BundleUpsert200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\PromotionsApi(
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
    echo 'Exception when calling PromotionsApi->couponList: ', $e->getMessage(), PHP_EOL;
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

## `couponUpsert()`

```php
couponUpsert($coupon_upsert_request): \ShadowSoftware\DabDash\Model\CouponUpsert200Response
```

Create or update a discount coupon on behalf of a tenant. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_upsert for those).  UPDATE MODE (coupon_id provided):   Edits the coupon. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no coupon_id):   Creates a new coupon. code, type, and value are required.  VALUE UNITS:   type = \"percentage\"     → value is a percentage 0-100 off the order subtotal.   type = \"fixed\"          → value is DOLLARS off the order subtotal (e.g. 10 = $10 off).   type = \"free_delivery\"  → value is ignored (pass 0); this type only waives the delivery fee.  min_order is a dollar minimum order subtotal required to use the coupon (pass dollars, e.g. 25 for a $25 minimum — the tool stores cents internally). Omit or pass 0 for no minimum.  freebie_id links this coupon to an existing Freebie (see freebie_list/freebie_upsert), turning it into a \"code-triggered freebie\": a customer who applies the code while below min_order has the code parked (kept attached, not rejected) and sees cart progress toward unlocking it, instead of the code being discarded. Pass null to unlink. The freebie must belong to the same tenant.  limit_match_by (\"email\"|\"phone\"|\"both\") controls how max_uses_per_customer is enforced. Using \"phone\" or \"both\" REQUIRES the tenant's \"Require phone at checkout\" setting to be on — otherwise the update is rejected, since customers without a phone on file could otherwise reuse the coupon past its per-customer limit.  SCHEDULE:   starts_at / expires_at are interpreted in the tenant's timezone and stored as UTC. Pass   null/omit for an always-on coupon.  PRODUCT/CATEGORY SCOPING:   applies_to=\"products\" or \"categories\" restricts the discount to matching cart lines only —   checkout math is fully scope-aware (a scoped coupon never discounts the whole cart). Pass   applies_to_ids as an array of product ids (when applies_to=\"products\") or category ids (when   applies_to=\"categories\") — all ids must belong to this tenant. Passing applies_to=\"products\"/   \"categories\" with an empty or omitted applies_to_ids behaves the same as \"all\" (no scope   configured yet). Switching back to applies_to=\"all\" does not automatically clear a   previously-set applies_to_ids — pass applies_to_ids=[] explicitly to clear it.  SUBSCRIPTION MOUNTING DISCOUNT LADDERS (subscribe-and-save retention):   A coupon can carry a \"mounting ladder\" so its discount climbs with each successive order a   customer's delivery subscription generates, pegging at a ceiling — e.g. order 1 = 0% off,   then +5% every order up to a 20% cap. This only affects orders generated for a subscription   linked to this coupon (DeliverySubscription.coupon_id) — it has no effect on ordinary   one-off checkout use of the code, which still uses type/value as normal.    subscription_ladder_id: link to an existing ladder (from another coupon) by id, or pass null   to unlink. Must belong to the same tenant.    ladder_start_percent / ladder_step_percent / ladder_cap_percent / ladder_start_order_index:   pass any of these to CREATE a new ladder inline (on coupon create) or EDIT the tiers of the   ladder already linked to this coupon (on update) — do not combine with subscription_ladder_id   in the same call. All four are whole-number percentages (0-100) except start_order_index,   which is the 1-based order number the climb begins at (orders before it stay at   ladder_start_percent). Omitted ladder_* fields default to 0% start / 5% step / 20% cap /   order 1 on create; on update, only the fields you pass are changed.  Always call coupon_list first to get the coupon_id and confirm the current configuration.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\PromotionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$coupon_upsert_request = new \ShadowSoftware\DabDash\Model\CouponUpsertRequest(); // \ShadowSoftware\DabDash\Model\CouponUpsertRequest

try {
    $result = $apiInstance->couponUpsert($coupon_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PromotionsApi->couponUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **coupon_upsert_request** | [**\ShadowSoftware\DabDash\Model\CouponUpsertRequest**](../Model/CouponUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CouponUpsert200Response**](../Model/CouponUpsert200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\PromotionsApi(
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
    echo 'Exception when calling PromotionsApi->freebieList: ', $e->getMessage(), PHP_EOL;
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

## `freebieUpsert()`

```php
freebieUpsert($freebie_upsert_request): \ShadowSoftware\DabDash\Model\FreebieUpsert200Response
```

Create or update a freebie rule (\"spend $X, get a free item\") on behalf of a tenant. Freebies are evaluated on every cart change: a rule fires once its spend_threshold is met, adding `quantity` of the configured product/variation to the cart (distinct from bundles, which fire on cart CONTENTS/quantity — use bundle_upsert for those).  UPDATE MODE (freebie_id provided):   Edits the freebie. Only the fields you pass are changed; omitted fields are left as-is,   EXCEPT category_ids, which — like bundle_upsert's variation_ids — fully replaces the category   set whenever passed (pass an empty array to clear all categories).  CREATE MODE (no freebie_id):   Creates a new freebie. name, product_id, spend_threshold, and quantity are required.  product_id and variation_id (if given) MUST belong to the same tenant — foreign ids are rejected, not silently ignored (unlike bundle_upsert's variation_ids, since a freebie needs exactly one product to give away, not a set).  spend_threshold is entered in DOLLARS (e.g. 50 for a $50 minimum spend) — the tool stores cents internally.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order.   false  → this rule cannot combine with other freebie rules.   Does NOT multiply this rule's own quantity by how many multiples of spend_threshold the cart   reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant's timezone and stored as UTC. Pass   null/omit for an always-on freebie.  Always call freebie_list first to get the freebie_id and confirm the current configuration.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\PromotionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$freebie_upsert_request = new \ShadowSoftware\DabDash\Model\FreebieUpsertRequest(); // \ShadowSoftware\DabDash\Model\FreebieUpsertRequest

try {
    $result = $apiInstance->freebieUpsert($freebie_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PromotionsApi->freebieUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **freebie_upsert_request** | [**\ShadowSoftware\DabDash\Model\FreebieUpsertRequest**](../Model/FreebieUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\FreebieUpsert200Response**](../Model/FreebieUpsert200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\PromotionsApi(
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
    echo 'Exception when calling PromotionsApi->promotionAudit: ', $e->getMessage(), PHP_EOL;
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

## `widgetManage()`

```php
widgetManage($widget_manage_request): \ShadowSoftware\DabDash\Model\WidgetManage200Response
```

List, create, update, or delete a tenant's homepage marketing widgets (the hero slider cards linking to a product, category, featured products, or a mix & match tag).  ACTIONS:   list   (default): return every widget with id, title, subtitle, link_type, target, sort_order,          is_active, and image_url. Always call this first to find a widget_id.   create: requires title. link_type + its matching id/tag is optional but recommended so the          widget's CTA actually goes somewhere (see LINK_TYPE below). Defaults to \"featured\"          (no target) when omitted.   update: requires widget_id. Only the fields you pass are changed.   delete: requires widget_id and confirm=true.  LINK_TYPE — pairs with exactly one target field:   \"product\"    → product_id   \"category\"   → category_id   \"mix_match\"  → mix_match_tag   \"featured\"   → no target needed (links to the featured-products listing)  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites the headline/logo overlay onto). Omit both to leave images untouched.  Always call action=list first to confirm widget_id before update or delete.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\PromotionsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$widget_manage_request = new \ShadowSoftware\DabDash\Model\WidgetManageRequest(); // \ShadowSoftware\DabDash\Model\WidgetManageRequest

try {
    $result = $apiInstance->widgetManage($widget_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PromotionsApi->widgetManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **widget_manage_request** | [**\ShadowSoftware\DabDash\Model\WidgetManageRequest**](../Model/WidgetManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\WidgetManage200Response**](../Model/WidgetManage200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
