# ShadowSoftware\DabDash\PromotionsApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**bundleHealth()**](PromotionsApi.md#bundleHealth) | **POST** /api/v1/tools/bundle_health | Read-only. Tenant-wide bundle health audit — the MCP equivalent of the vendor-facing /admin/bundles/health page (GitHub #395). Surfaces price-ladder conflicts: a variation currently sitting in 2+ active &#x60;fixed_total&#x60; bundles at different price points, which is exactly the \&quot;14 variations stuck in two price rungs\&quot; leak the original issue was about. A synchronous guard already prevents most NEW conflicts at save time, but this catches drift from bulk imports, seeders, or any write that bypassed Eloquent.  Each conflict lists the variation and every bundle it&#39;s in with that bundle&#39;s shared member price (its \&quot;rung\&quot;), so you can tell which bundle is now stale for that variation and remove it (via bundle_membership action&#x3D;exclude, or bundle_upsert variation_mode&#x3D;detach for a manual bundle).  Call bundle_list first if you need bundle names/ids beyond what&#39;s returned here. |
| [**bundleList()**](PromotionsApi.md#bundleList) | **POST** /api/v1/tools/bundle_list | List a tenant&#39;s bundle deals (\&quot;mix &amp; match\&quot; — e.g. \&quot;buy 4 for $77\&quot;) with id, name, trigger quantity, discount type/value, active state, schedule window, and attached variation count. Bundles are the live cart engine (MixMatchService): a bundle fires when a cart holds at least &#x60;quantity&#x60; units across its attached variations. Always call this before bundle_upsert to get the bundle id and confirm the current discount configuration.  IMPORTANT: this reads the &#x60;bundles&#x60; table — the source of truth the storefront cart uses. It is NOT the legacy &#x60;mix_match_rules&#x60; tenant setting that promotion_audit / product_inspect surface; those are stale display-only data. Trust this tool for what actually applies at checkout.  discount_type:   percent      → discount_value is a percentage 0–100, applied per unit.   fixed        → discount_value is dollars off PER UNIT.   fixed_total  → discount_value is the dollar TOTAL for the whole set (\&quot;$77 for 4\&quot;). |
| [**bundleMembership()**](PromotionsApi.md#bundleMembership) | **POST** /api/v1/tools/bundle_membership | Override one variation&#39;s membership in one bundle, independent of whatever a bundle&#39;s rule (GitHub #395 \&quot;smart collections\&quot;) would otherwise decide. Precedence is always excluded &gt; pinned &gt; rule — this is how you handle \&quot;this tenant is mostly automatic but this one variation needs to stay in/out no matter what the rule says\&quot;.  action:   pin       Force this variation INTO the bundle. Survives every future rule recompute. Also             clears any existing exclusion for it (a deliberate \&quot;include this\&quot; beats an earlier             \&quot;exclude this\&quot;).   unpin     Remove an explicit pin. If the bundle&#39;s rule still matches this variation, the next             recompute (which runs automatically here) puts it back as an automatic (\&quot;rule\&quot;) member;             otherwise it simply falls out.   exclude   Force this variation OUT of the bundle, immediately, even if it&#39;s currently pinned or             rule-matched. Never re-added by a rule recompute until unexcluded.   unexclude Lift an exclusion. If the rule still matches, the next recompute (run automatically             here) re-adds it as an automatic member; it is NOT automatically pinned.  pin/exclude/unpin/unexclude all work on fully-manual bundles too (a bundle with no rule) — they just directly add/remove the pivot row in that case, same as the vendor bundle picker&#39;s pin/exclude buttons.  Call bundle_list or bundle_rule_preview first to find the right bundle_id and variation_id. |
| [**bundleRulePreview()**](PromotionsApi.md#bundleRulePreview) | **POST** /api/v1/tools/bundle_rule_preview | Read-only. Shows what a bundle membership rule (\&quot;smart collection\&quot; — GitHub #395) would match, WITHOUT writing anything. Use this before bundle_upsert to sanity-check a rule against a live tenant&#39;s catalog rather than guessing blind.  Two ways to call it:   - Pass &#x60;bundle_id&#x60; to preview a bundle&#39;s CURRENTLY STORED rule (what recompute() would do right now).   - Pass &#x60;rules&#x60; (and no bundle_id) to preview a HYPOTHETICAL rule that hasn&#39;t been saved anywhere —     \&quot;what would this match if I set it up this way\&quot;. The rule is evaluated in-memory only. Passing both previews the inline &#x60;rules&#x60; against that bundle&#39;s tenant, ignoring its stored rule.  Each condition is {field, operator, value} — the same shape bundle_upsert&#39;s &#x60;rules&#x60; field takes:   field: unit_price (cents) | category_id | exclude_category_id | weight_value (grams) | mix_match_tag | product_status          (draft|active|archived) | tracking_type (weight|unit|matrix)   operator: eq (default) | lt | lte | gt | gte | between (value must be [min, max]) Conditions are AND-ed. An empty rules list matches nothing (a bundle only auto-populates once it has at least one condition).  Returns the total match count plus up to 20 sample variations (id, product name, size/weight, current price) so you can eyeball whether the rule caught the right things before committing it. |
| [**bundleUpsert()**](PromotionsApi.md#bundleUpsert) | **POST** /api/v1/tools/bundle_upsert | Create or update a bundle deal (mix &amp; match) on behalf of a tenant. Bundles are the live cart engine: a bundle fires when a cart holds at least &#x60;quantity&#x60; units across its attached variations.  UPDATE MODE (bundle_id provided):   Edits the bundle. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no bundle_id):   Creates a new bundle. name, quantity, discount_type, and discount_value are required.  DISCOUNT VALUE UNITS — read carefully, this is the common mistake:   discount_type &#x3D; \&quot;percent\&quot;      → discount_value is a percentage 0–100 (e.g. 20 &#x3D; 20% off each unit).   discount_type &#x3D; \&quot;fixed\&quot;        → discount_value is DOLLARS off PER UNIT (e.g. 5 &#x3D; $5 off each).   discount_type &#x3D; \&quot;fixed_total\&quot;  → discount_value is the DOLLARS TOTAL for the whole set                                     (e.g. quantity&#x3D;4, discount_value&#x3D;77 → \&quot;any 4 for $77\&quot;).   For fixed and fixed_total, pass dollars (e.g. 77 or 77.00) — the tool stores cents internally.   For percent, pass the percentage (e.g. 20), NOT a fraction.  VARIATIONS:   variation_ids + variation_mode control which product variations the bundle applies to.   mode \&quot;replace\&quot; (default) sets membership to exactly variation_ids; \&quot;add\&quot; attaches them to the   existing set; \&quot;detach\&quot; removes them. Variations not owned by the tenant are ignored.   Omit variation_ids entirely to leave membership untouched.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass null/omit   for an always-on bundle.  RULES (smart collections — GitHub #395):   Pass &#x60;rules&#x60; to turn this bundle into a rule-based (\&quot;smart collection\&quot;) bundle: membership is   computed automatically from a list of AND-ed conditions instead of a fixed variation_ids list.   Each condition is {field, operator, value}:     field: unit_price (cents) | category_id | exclude_category_id | weight_value (grams) | mix_match_tag | product_status            (draft|active|archived) | tracking_type (weight|unit|matrix)     operator: eq (default) | lt | lte | gt | gte | between (value must be [min, max])   On save, membership is recomputed immediately: variations matching every condition are added as   \&quot;rule\&quot; members. Pinned members and explicit exclusions (see bundle_membership) are never touched   or overridden by a rule — excluded beats pinned beats rule. The response reports how many   variations were added/removed by this recompute.   ALWAYS call bundle_rule_preview with your intended rules BEFORE saving them here — it shows   exactly what would match without writing anything, so you don&#39;t fire a rule blind against a   live tenant&#39;s catalog.   Passing &#x60;rules: []&#x60; or &#x60;rules: null&#x60; explicitly clears the bundle back to fully manual: existing   membership is left as-is (nothing is auto-removed), but it stops auto-updating and variation_ids   takes over again.   Omit &#x60;rules&#x60; entirely to leave a bundle&#39;s current rule state (manual or automatic) untouched.  Always call bundle_list first to get the bundle_id and confirm the current configuration. |
| [**couponList()**](PromotionsApi.md#couponList) | **POST** /api/v1/tools/coupon_list | List a tenant&#39;s discount coupons with code, type, value, usage limits, redemption count, active state, and schedule window. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_list for those).  type:   percentage     → value is a percentage 0-100 off the order subtotal.   fixed          → value is dollars off the order subtotal.   free_delivery  → waives the delivery fee only; value is unused for this type.  Always call this before making coupon-related decisions to see current codes, usage caps, and whether a coupon has already been exhausted (used_count vs max_uses). |
| [**couponUpsert()**](PromotionsApi.md#couponUpsert) | **POST** /api/v1/tools/coupon_upsert | Create or update a discount coupon on behalf of a tenant. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_upsert for those).  UPDATE MODE (coupon_id provided):   Edits the coupon. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no coupon_id):   Creates a new coupon. code, type, and value are required.  VALUE UNITS:   type &#x3D; \&quot;percentage\&quot;     → value is a percentage 0-100 off the order subtotal.   type &#x3D; \&quot;fixed\&quot;          → value is DOLLARS off the order subtotal (e.g. 10 &#x3D; $10 off).   type &#x3D; \&quot;free_delivery\&quot;  → value is ignored (pass 0); this type only waives the delivery fee.  min_order is a dollar minimum order subtotal required to use the coupon (pass dollars, e.g. 25 for a $25 minimum — the tool stores cents internally). Omit or pass 0 for no minimum.  freebie_id links this coupon to an existing Freebie (see freebie_list/freebie_upsert), turning it into a \&quot;code-triggered freebie\&quot;: a customer who applies the code while below min_order has the code parked (kept attached, not rejected) and sees cart progress toward unlocking it, instead of the code being discarded. Pass null to unlink. The freebie must belong to the same tenant.  limit_match_by (\&quot;email\&quot;|\&quot;phone\&quot;|\&quot;both\&quot;) controls how max_uses_per_customer is enforced. Using \&quot;phone\&quot; or \&quot;both\&quot; REQUIRES the tenant&#39;s \&quot;Require phone at checkout\&quot; setting to be on — otherwise the update is rejected, since customers without a phone on file could otherwise reuse the coupon past its per-customer limit.  SCHEDULE:   starts_at / expires_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass   null/omit for an always-on coupon.  PRODUCT/CATEGORY SCOPING:   applies_to&#x3D;\&quot;products\&quot; or \&quot;categories\&quot; restricts the discount to matching cart lines only —   checkout math is fully scope-aware (a scoped coupon never discounts the whole cart). Pass   applies_to_ids as an array of product ids (when applies_to&#x3D;\&quot;products\&quot;) or category ids (when   applies_to&#x3D;\&quot;categories\&quot;) — all ids must belong to this tenant. Passing applies_to&#x3D;\&quot;products\&quot;/   \&quot;categories\&quot; with an empty or omitted applies_to_ids behaves the same as \&quot;all\&quot; (no scope   configured yet). Switching back to applies_to&#x3D;\&quot;all\&quot; does not automatically clear a   previously-set applies_to_ids — pass applies_to_ids&#x3D;[] explicitly to clear it.  SUBSCRIPTION MOUNTING DISCOUNT LADDERS (subscribe-and-save retention):   A coupon can carry a \&quot;mounting ladder\&quot; so its discount climbs with each successive order a   customer&#39;s delivery subscription generates, pegging at a ceiling — e.g. order 1 &#x3D; 0% off,   then +5% every order up to a 20% cap. This only affects orders generated for a subscription   linked to this coupon (DeliverySubscription.coupon_id) — it has no effect on ordinary   one-off checkout use of the code, which still uses type/value as normal.    subscription_ladder_id: link to an existing ladder (from another coupon) by id, or pass null   to unlink. Must belong to the same tenant.    ladder_start_percent / ladder_step_percent / ladder_cap_percent / ladder_start_order_index:   pass any of these to CREATE a new ladder inline (on coupon create) or EDIT the tiers of the   ladder already linked to this coupon (on update) — do not combine with subscription_ladder_id   in the same call. All four are whole-number percentages (0-100) except start_order_index,   which is the 1-based order number the climb begins at (orders before it stay at   ladder_start_percent). Omitted ladder_* fields default to 0% start / 5% step / 20% cap /   order 1 on create; on update, only the fields you pass are changed.  contact_list_id optionally restricts redemption to a tenant-owned manual or Smart List. Smart Lists are re-evaluated live; manual-list customers and leads are matched by contact email. The restriction is checked at cart apply and checkout redemption time. Pass null to remove it.  Always call coupon_list first to get the coupon_id and confirm the current configuration. |
| [**freebieList()**](PromotionsApi.md#freebieList) | **POST** /api/v1/tools/freebie_list | List a tenant&#39;s freebie rules (\&quot;spend $X, get a free item\&quot;) with id, name, spend threshold, the product/variation given away, quantity, stackable flag, active state, and schedule window. Freebies are evaluated on every cart change by FreebieService: a rule fires once its spend_threshold is met, adding &#x60;quantity&#x60; of the configured product/variation to the cart.  IMPORTANT: this reads the &#x60;freebies&#x60; table — the source of truth the storefront cart uses. It is NOT the legacy freebie summary promotion_audit surfaces alongside coupons/bundles; that view is stale display-only data built for a different purpose. Trust this tool for what actually applies at checkout.  is_merch_product: true when the freebie&#39;s product is tagged \&quot;merch\&quot; (Product.tags contains \&quot;merch\&quot;) — a branded giveaway item (hats, apparel) rather than sellable cannabis inventory. Prefer merch-tagged products over high-COGS flower when recommending a NEW freebie: giving away promo materials protects margin the same way a discount code doesn&#39;t.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order (each            rule still only fires once, at its configured quantity, regardless of how far above            its own threshold the cart is). When several stackable rules gift the SAME            variation, they collapse to one cart line whose quantity is the SUM of those rules            (cart_items is unique on cart_id+variation_id — without summing, higher tiers were            silently dropped).   false  → this rule cannot combine with other freebie rules that gift the same variation; if            multiple non-stackable rules share a SKU, the highest spend_threshold wins.   This flag does NOT multiply a single rule&#39;s quantity by how many multiples of the threshold   the cart reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3. |
| [**freebieUpsert()**](PromotionsApi.md#freebieUpsert) | **POST** /api/v1/tools/freebie_upsert | Create or update a freebie rule (\&quot;spend $X, get a free item\&quot;) on behalf of a tenant. Freebies are evaluated on every cart change: a rule fires once its spend_threshold is met, adding &#x60;quantity&#x60; of the configured product/variation to the cart (distinct from bundles, which fire on cart CONTENTS/quantity — use bundle_upsert for those).  UPDATE MODE (freebie_id provided):   Edits the freebie. Only the fields you pass are changed; omitted fields are left as-is,   EXCEPT category_ids, which — like bundle_upsert&#39;s variation_ids — fully replaces the category   set whenever passed (pass an empty array to clear all categories).  CREATE MODE (no freebie_id):   Creates a new freebie. name, product_id, spend_threshold, and quantity are required.  product_id and variation_id (if given) MUST belong to the same tenant — foreign ids are rejected, not silently ignored (unlike bundle_upsert&#39;s variation_ids, since a freebie needs exactly one product to give away, not a set).  spend_threshold is entered in DOLLARS (e.g. 50 for a $50 minimum spend) — the tool stores cents internally.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order.   false  → this rule cannot combine with other freebie rules.   Does NOT multiply this rule&#39;s own quantity by how many multiples of spend_threshold the cart   reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant&#39;s timezone and stored as UTC. Pass   null/omit for an always-on freebie.  Always call freebie_list first to get the freebie_id and confirm the current configuration. |
| [**loyaltyManage()**](PromotionsApi.md#loyaltyManage) | **POST** /api/v1/tools/loyalty_manage | Show or configure a store&#39;s points-based loyalty program, including vendor-defined lifetime-point tiers with automatic percentage discounts.  ACTIONS:   show (default): current settings.   set: pass only what changes. dry_run defaults true; pass dry_run&#x3D;false to save.   set_customer_lifetime: set one explicitly sourced customer&#39;s lifetime total. This is     for a one-time migration and is dry-run by default; it never changes spendable balance. |
| [**promotionAudit()**](PromotionsApi.md#promotionAudit) | **POST** /api/v1/tools/promotion_audit | Inspect coupons, freebies, mix and match rules, loyalty settings, and storewide sale state for overcharge or missed-discount support tickets. |
| [**widgetManage()**](PromotionsApi.md#widgetManage) | **POST** /api/v1/tools/widget_manage | List, create, update, or delete a tenant&#39;s homepage marketing widgets (the hero slider cards linking to a product, category, featured products, or a mix &amp; match tag).  ACTIONS:   list   (default): return every widget with id, title, subtitle, link_type, target, sort_order,          is_active, and image_url. Always call this first to find a widget_id.   create: requires title. link_type + its matching id/tag is optional but recommended so the          widget&#39;s CTA actually goes somewhere (see LINK_TYPE below). Defaults to \&quot;featured\&quot;          (no target) when omitted.   update: requires widget_id. Only the fields you pass are changed.   delete: requires widget_id and confirm&#x3D;true.  LINK_TYPE — pairs with exactly one target field:   \&quot;product\&quot;    → product_id   \&quot;category\&quot;   → category_id   \&quot;mix_match\&quot;  → mix_match_tag   \&quot;featured\&quot;   → no target needed (links to the featured-products listing)  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites the headline/logo overlay onto). Omit both to leave images untouched.  Always call action&#x3D;list first to confirm widget_id before update or delete. |


## `bundleHealth()`

```php
bundleHealth($body): \ShadowSoftware\DabDash\Model\BundleHealth200Response
```

Read-only. Tenant-wide bundle health audit — the MCP equivalent of the vendor-facing /admin/bundles/health page (GitHub #395). Surfaces price-ladder conflicts: a variation currently sitting in 2+ active `fixed_total` bundles at different price points, which is exactly the \"14 variations stuck in two price rungs\" leak the original issue was about. A synchronous guard already prevents most NEW conflicts at save time, but this catches drift from bulk imports, seeders, or any write that bypassed Eloquent.  Each conflict lists the variation and every bundle it's in with that bundle's shared member price (its \"rung\"), so you can tell which bundle is now stale for that variation and remove it (via bundle_membership action=exclude, or bundle_upsert variation_mode=detach for a manual bundle).  Call bundle_list first if you need bundle names/ids beyond what's returned here.

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
$body = array('key' => new \stdClass); // object

try {
    $result = $apiInstance->bundleHealth($body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PromotionsApi->bundleHealth: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **body** | **object**|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleHealth200Response**](../Model/BundleHealth200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

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

## `bundleMembership()`

```php
bundleMembership($bundle_membership_request): \ShadowSoftware\DabDash\Model\BundleMembership200Response
```

Override one variation's membership in one bundle, independent of whatever a bundle's rule (GitHub #395 \"smart collections\") would otherwise decide. Precedence is always excluded > pinned > rule — this is how you handle \"this tenant is mostly automatic but this one variation needs to stay in/out no matter what the rule says\".  action:   pin       Force this variation INTO the bundle. Survives every future rule recompute. Also             clears any existing exclusion for it (a deliberate \"include this\" beats an earlier             \"exclude this\").   unpin     Remove an explicit pin. If the bundle's rule still matches this variation, the next             recompute (which runs automatically here) puts it back as an automatic (\"rule\") member;             otherwise it simply falls out.   exclude   Force this variation OUT of the bundle, immediately, even if it's currently pinned or             rule-matched. Never re-added by a rule recompute until unexcluded.   unexclude Lift an exclusion. If the rule still matches, the next recompute (run automatically             here) re-adds it as an automatic member; it is NOT automatically pinned.  pin/exclude/unpin/unexclude all work on fully-manual bundles too (a bundle with no rule) — they just directly add/remove the pivot row in that case, same as the vendor bundle picker's pin/exclude buttons.  Call bundle_list or bundle_rule_preview first to find the right bundle_id and variation_id.

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
$bundle_membership_request = new \ShadowSoftware\DabDash\Model\BundleMembershipRequest(); // \ShadowSoftware\DabDash\Model\BundleMembershipRequest

try {
    $result = $apiInstance->bundleMembership($bundle_membership_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PromotionsApi->bundleMembership: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_membership_request** | [**\ShadowSoftware\DabDash\Model\BundleMembershipRequest**](../Model/BundleMembershipRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleMembership200Response**](../Model/BundleMembership200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `bundleRulePreview()`

```php
bundleRulePreview($bundle_rule_preview_request): \ShadowSoftware\DabDash\Model\BundleRulePreview200Response
```

Read-only. Shows what a bundle membership rule (\"smart collection\" — GitHub #395) would match, WITHOUT writing anything. Use this before bundle_upsert to sanity-check a rule against a live tenant's catalog rather than guessing blind.  Two ways to call it:   - Pass `bundle_id` to preview a bundle's CURRENTLY STORED rule (what recompute() would do right now).   - Pass `rules` (and no bundle_id) to preview a HYPOTHETICAL rule that hasn't been saved anywhere —     \"what would this match if I set it up this way\". The rule is evaluated in-memory only. Passing both previews the inline `rules` against that bundle's tenant, ignoring its stored rule.  Each condition is {field, operator, value} — the same shape bundle_upsert's `rules` field takes:   field: unit_price (cents) | category_id | exclude_category_id | weight_value (grams) | mix_match_tag | product_status          (draft|active|archived) | tracking_type (weight|unit|matrix)   operator: eq (default) | lt | lte | gt | gte | between (value must be [min, max]) Conditions are AND-ed. An empty rules list matches nothing (a bundle only auto-populates once it has at least one condition).  Returns the total match count plus up to 20 sample variations (id, product name, size/weight, current price) so you can eyeball whether the rule caught the right things before committing it.

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
$bundle_rule_preview_request = new \ShadowSoftware\DabDash\Model\BundleRulePreviewRequest(); // \ShadowSoftware\DabDash\Model\BundleRulePreviewRequest

try {
    $result = $apiInstance->bundleRulePreview($bundle_rule_preview_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PromotionsApi->bundleRulePreview: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **bundle_rule_preview_request** | [**\ShadowSoftware\DabDash\Model\BundleRulePreviewRequest**](../Model/BundleRulePreviewRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\BundleRulePreview200Response**](../Model/BundleRulePreview200Response.md)

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

Create or update a bundle deal (mix & match) on behalf of a tenant. Bundles are the live cart engine: a bundle fires when a cart holds at least `quantity` units across its attached variations.  UPDATE MODE (bundle_id provided):   Edits the bundle. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no bundle_id):   Creates a new bundle. name, quantity, discount_type, and discount_value are required.  DISCOUNT VALUE UNITS — read carefully, this is the common mistake:   discount_type = \"percent\"      → discount_value is a percentage 0–100 (e.g. 20 = 20% off each unit).   discount_type = \"fixed\"        → discount_value is DOLLARS off PER UNIT (e.g. 5 = $5 off each).   discount_type = \"fixed_total\"  → discount_value is the DOLLARS TOTAL for the whole set                                     (e.g. quantity=4, discount_value=77 → \"any 4 for $77\").   For fixed and fixed_total, pass dollars (e.g. 77 or 77.00) — the tool stores cents internally.   For percent, pass the percentage (e.g. 20), NOT a fraction.  VARIATIONS:   variation_ids + variation_mode control which product variations the bundle applies to.   mode \"replace\" (default) sets membership to exactly variation_ids; \"add\" attaches them to the   existing set; \"detach\" removes them. Variations not owned by the tenant are ignored.   Omit variation_ids entirely to leave membership untouched.  SCHEDULE:   starts_at / ends_at are interpreted in the tenant's timezone and stored as UTC. Pass null/omit   for an always-on bundle.  RULES (smart collections — GitHub #395):   Pass `rules` to turn this bundle into a rule-based (\"smart collection\") bundle: membership is   computed automatically from a list of AND-ed conditions instead of a fixed variation_ids list.   Each condition is {field, operator, value}:     field: unit_price (cents) | category_id | exclude_category_id | weight_value (grams) | mix_match_tag | product_status            (draft|active|archived) | tracking_type (weight|unit|matrix)     operator: eq (default) | lt | lte | gt | gte | between (value must be [min, max])   On save, membership is recomputed immediately: variations matching every condition are added as   \"rule\" members. Pinned members and explicit exclusions (see bundle_membership) are never touched   or overridden by a rule — excluded beats pinned beats rule. The response reports how many   variations were added/removed by this recompute.   ALWAYS call bundle_rule_preview with your intended rules BEFORE saving them here — it shows   exactly what would match without writing anything, so you don't fire a rule blind against a   live tenant's catalog.   Passing `rules: []` or `rules: null` explicitly clears the bundle back to fully manual: existing   membership is left as-is (nothing is auto-removed), but it stops auto-updating and variation_ids   takes over again.   Omit `rules` entirely to leave a bundle's current rule state (manual or automatic) untouched.  Always call bundle_list first to get the bundle_id and confirm the current configuration.

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

Create or update a discount coupon on behalf of a tenant. Coupons are customer-entered codes applied at checkout (distinct from bundles, which fire automatically on cart contents — use bundle_upsert for those).  UPDATE MODE (coupon_id provided):   Edits the coupon. Only the fields you pass are changed; omitted fields are left as-is.  CREATE MODE (no coupon_id):   Creates a new coupon. code, type, and value are required.  VALUE UNITS:   type = \"percentage\"     → value is a percentage 0-100 off the order subtotal.   type = \"fixed\"          → value is DOLLARS off the order subtotal (e.g. 10 = $10 off).   type = \"free_delivery\"  → value is ignored (pass 0); this type only waives the delivery fee.  min_order is a dollar minimum order subtotal required to use the coupon (pass dollars, e.g. 25 for a $25 minimum — the tool stores cents internally). Omit or pass 0 for no minimum.  freebie_id links this coupon to an existing Freebie (see freebie_list/freebie_upsert), turning it into a \"code-triggered freebie\": a customer who applies the code while below min_order has the code parked (kept attached, not rejected) and sees cart progress toward unlocking it, instead of the code being discarded. Pass null to unlink. The freebie must belong to the same tenant.  limit_match_by (\"email\"|\"phone\"|\"both\") controls how max_uses_per_customer is enforced. Using \"phone\" or \"both\" REQUIRES the tenant's \"Require phone at checkout\" setting to be on — otherwise the update is rejected, since customers without a phone on file could otherwise reuse the coupon past its per-customer limit.  SCHEDULE:   starts_at / expires_at are interpreted in the tenant's timezone and stored as UTC. Pass   null/omit for an always-on coupon.  PRODUCT/CATEGORY SCOPING:   applies_to=\"products\" or \"categories\" restricts the discount to matching cart lines only —   checkout math is fully scope-aware (a scoped coupon never discounts the whole cart). Pass   applies_to_ids as an array of product ids (when applies_to=\"products\") or category ids (when   applies_to=\"categories\") — all ids must belong to this tenant. Passing applies_to=\"products\"/   \"categories\" with an empty or omitted applies_to_ids behaves the same as \"all\" (no scope   configured yet). Switching back to applies_to=\"all\" does not automatically clear a   previously-set applies_to_ids — pass applies_to_ids=[] explicitly to clear it.  SUBSCRIPTION MOUNTING DISCOUNT LADDERS (subscribe-and-save retention):   A coupon can carry a \"mounting ladder\" so its discount climbs with each successive order a   customer's delivery subscription generates, pegging at a ceiling — e.g. order 1 = 0% off,   then +5% every order up to a 20% cap. This only affects orders generated for a subscription   linked to this coupon (DeliverySubscription.coupon_id) — it has no effect on ordinary   one-off checkout use of the code, which still uses type/value as normal.    subscription_ladder_id: link to an existing ladder (from another coupon) by id, or pass null   to unlink. Must belong to the same tenant.    ladder_start_percent / ladder_step_percent / ladder_cap_percent / ladder_start_order_index:   pass any of these to CREATE a new ladder inline (on coupon create) or EDIT the tiers of the   ladder already linked to this coupon (on update) — do not combine with subscription_ladder_id   in the same call. All four are whole-number percentages (0-100) except start_order_index,   which is the 1-based order number the climb begins at (orders before it stay at   ladder_start_percent). Omitted ladder_* fields default to 0% start / 5% step / 20% cap /   order 1 on create; on update, only the fields you pass are changed.  contact_list_id optionally restricts redemption to a tenant-owned manual or Smart List. Smart Lists are re-evaluated live; manual-list customers and leads are matched by contact email. The restriction is checked at cart apply and checkout redemption time. Pass null to remove it.  Always call coupon_list first to get the coupon_id and confirm the current configuration.

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

List a tenant's freebie rules (\"spend $X, get a free item\") with id, name, spend threshold, the product/variation given away, quantity, stackable flag, active state, and schedule window. Freebies are evaluated on every cart change by FreebieService: a rule fires once its spend_threshold is met, adding `quantity` of the configured product/variation to the cart.  IMPORTANT: this reads the `freebies` table — the source of truth the storefront cart uses. It is NOT the legacy freebie summary promotion_audit surfaces alongside coupons/bundles; that view is stale display-only data built for a different purpose. Trust this tool for what actually applies at checkout.  is_merch_product: true when the freebie's product is tagged \"merch\" (Product.tags contains \"merch\") — a branded giveaway item (hats, apparel) rather than sellable cannabis inventory. Prefer merch-tagged products over high-COGS flower when recommending a NEW freebie: giving away promo materials protects margin the same way a discount code doesn't.  is_stackable:   true   → this rule can fire alongside OTHER DIFFERENT freebie rules on the same order (each            rule still only fires once, at its configured quantity, regardless of how far above            its own threshold the cart is). When several stackable rules gift the SAME            variation, they collapse to one cart line whose quantity is the SUM of those rules            (cart_items is unique on cart_id+variation_id — without summing, higher tiers were            silently dropped).   false  → this rule cannot combine with other freebie rules that gift the same variation; if            multiple non-stackable rules share a SKU, the highest spend_threshold wins.   This flag does NOT multiply a single rule's quantity by how many multiples of the threshold   the cart reaches — a $50-threshold rule at $150 spent still gives quantity 1, not 3.

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

## `loyaltyManage()`

```php
loyaltyManage($loyalty_manage_request): \ShadowSoftware\DabDash\Model\LoyaltyManage200Response
```

Show or configure a store's points-based loyalty program, including vendor-defined lifetime-point tiers with automatic percentage discounts.  ACTIONS:   show (default): current settings.   set: pass only what changes. dry_run defaults true; pass dry_run=false to save.   set_customer_lifetime: set one explicitly sourced customer's lifetime total. This is     for a one-time migration and is dry-run by default; it never changes spendable balance.

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
$loyalty_manage_request = new \ShadowSoftware\DabDash\Model\LoyaltyManageRequest(); // \ShadowSoftware\DabDash\Model\LoyaltyManageRequest

try {
    $result = $apiInstance->loyaltyManage($loyalty_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PromotionsApi->loyaltyManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **loyalty_manage_request** | [**\ShadowSoftware\DabDash\Model\LoyaltyManageRequest**](../Model/LoyaltyManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\LoyaltyManage200Response**](../Model/LoyaltyManage200Response.md)

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
