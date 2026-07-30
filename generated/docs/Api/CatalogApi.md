# ShadowSoftware\DabDash\CatalogApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**catalogCollapse()**](CatalogApi.md#catalogCollapse) | **POST** /api/v1/tools/catalog_collapse | Merges a group of size-split products into ONE product with size options.  Turns \&quot;Blue Dream - 3.5G\&quot;, \&quot;Blue Dream - 7G\&quot;, \&quot;Blue Dream - 28G\&quot; (three separate products, each with one \&quot;Default\&quot; option) into a single \&quot;Blue Dream\&quot; product that sells by weight with 3.5g / 7g / 28g options. Stock is added together into one shared pool, and each old price becomes the price of its size.  Run catalog_flattening_audit first to see the groups and their exact base_name.  SAFETY: - dry_run defaults to TRUE. Nothing changes until you pass dry_run&#x3D;false. The dry run returns   the exact sizes, prices, stock, and which products would be removed. - Past orders are never affected — they keep the product name and price the customer saw. - Refused when two products in the group claim the same size, or when any product belongs to   another store. - This removes the duplicate products. It cannot be undone from here. |
| [**catalogFlatteningAudit()**](CatalogApi.md#catalogFlatteningAudit) | **POST** /api/v1/tools/catalog_flattening_audit | Read-only. Finds products whose sizes were split into separate products instead of tiers.  This happens when a catalog is imported from a store that put the size in the product NAME (\&quot;Blue Dream - 3.5G\&quot;, \&quot;Blue Dream - 7G\&quot;) instead of a size option column. The importer has no size column to read, so each size becomes its own product with a single \&quot;Default\&quot; option, and the store ends up with a long flat menu that cannot use weight pricing or mix &amp; match deals.  Returns each group of products that belong together (\&quot;family\&quot;), the sizes and prices found, and whether the group can be safely merged. A group is NOT mergeable when two of its products claim the same size — that must be resolved by hand first.  Nothing is changed. Use catalog_collapse to merge a group. |
| [**categoryManage()**](CatalogApi.md#categoryManage) | **POST** /api/v1/tools/category_manage | List, create, update, or delete a tenant&#39;s storefront categories.  ACTIONS:   list   (default): return every category with id, name, slug, parent, sort_order,          is_active, is_featured, and image_url. Always call this first to find a          category_id before update/delete, and to check for slug collisions before create.   create: requires name (slug is auto-generated from name if omitted).   update: requires category_id. Only the fields you pass are changed.   delete: requires category_id and confirm&#x3D;true. Refuses if the category still has          products or children attached (detach or reassign them first).  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites branding onto). Omit both to leave images untouched.  Always call action&#x3D;list first to confirm category_id / slug before update or delete. |
| [**inventoryAuditLookup()**](CatalogApi.md#inventoryAuditLookup) | **POST** /api/v1/tools/inventory_audit_lookup | Look up the historical inventory state of a list of products from the inventory_audit_logs table.  For each product, returns every variation_id ever logged in the audit trail (including variations that have since been deleted), the variation&#39;s last known stock_quantity strictly BEFORE the given cutoff timestamp, and that variation&#39;s most recent action+notes for context.  Use this tool to recover pre-incident stock values when variations have been overwritten or deleted by a destructive operation (e.g. an erroneous pricing structure assignment that wiped unit/simple variations and replaced them with weight tiers).  Output is grouped per product. Each product also includes its CURRENT variations and their stock for comparison so you can see the delta. |
| [**inventoryStatus()**](CatalogApi.md#inventoryStatus) | **POST** /api/v1/tools/inventory_status | Get inventory status across all tenants or a specific tenant. Shows low stock alerts and out-of-stock products. |
| [**pricingStructureAssign()**](CatalogApi.md#pricingStructureAssign) | **POST** /api/v1/tools/pricing_structure_assign | Assign a shared bundle pricing structure to one or more products. Re-syncs variations for each reassigned product. Automatically deletes orphaned inline (hidden) structures when replacing them.  SAFETY RULES enforced by this tool: - The target structure_id must be a BUNDLE (is_hidden&#x3D;false). Inline structures cannot be assigned   to products this way — that would violate the 1:1 contract. - If a product&#39;s current structure is inline (hidden) and this product is its only consumer   (product_count &#x3D;&#x3D; 1), the old inline structure is deleted automatically. - If a product&#39;s current structure is inline but product_count &gt; 1, assignment is refused for that   product with an explanation — this is a data anomaly that needs manual resolution. - Each product result includes a status: assigned | skipped (already on this structure) | refused.  Use pricing_structure_list to get valid structure IDs before calling this tool. |
| [**pricingStructureDelete()**](CatalogApi.md#pricingStructureDelete) | **POST** /api/v1/tools/pricing_structure_delete | Delete one or more pricing structures by ID.  SAFETY RULES enforced by this tool: - BUNDLE structures (is_hidden&#x3D;false): always deletable unless products are still assigned.   Pass force&#x3D;true to delete even when products are assigned (use only after migrating them). - INLINE structures (is_hidden&#x3D;true): only deletable when product_count&#x3D;0 (orphaned).   Inline structures with products attached cannot be deleted — use pricing_structure_assign   to move the product to a bundle first, which auto-cleans the inline structure. |
| [**pricingStructureList()**](CatalogApi.md#pricingStructureList) | **POST** /api/v1/tools/pricing_structure_list | List all pricing structures for a tenant with their kind (inline|bundle), product count, tracking type, and tier summary. Always call this before pricing_structure_upsert or pricing_structure_assign to get structure IDs and confirm which structures are bundles vs inline (1:1 product) structures.  kind&#x3D;inline  → hidden 1:1 structure tied to exactly one product (is_hidden&#x3D;true) kind&#x3D;bundle  → shared structure visible on /admin/pricing, used by 0 or more products (is_hidden&#x3D;false) |
| [**pricingStructureRestore()**](CatalogApi.md#pricingStructureRestore) | **POST** /api/v1/tools/pricing_structure_restore | Surgical restore tool. Rebuilds a single product&#39;s pricing structure and variations EXACTLY to a specified state. Bypasses the standard syncVariationsForProduct routine — you control every field.  Use this AFTER inventory_audit_lookup has revealed the pre-incident state of variations whose names/prices/stock were destroyed by an erroneous bundle reassignment.  Behaviour: - Creates a NEW inline (hidden, 1:1) pricing structure with the given tracking_type, tier   definitions, and a name like \&quot;Product: &lt;product_name&gt; (Hidden)\&quot;. Old structure linkage is   replaced. The previous structure is NOT deleted by this tool. - For each tier in the spec, finds-or-creates a variation. Matching is by &#x60;restore_variation_id&#x60;   if provided, else by name. If found, the variation is updated in place (preserving its id and   its audit-log history). If not found, a new variation is created. - Stock_quantity is set EXACTLY to the value specified — this is the whole point of the tool. - Sets product.tracking_type, product.inventory_mode, product.base_unit per the new structure. - Variations on the product not referenced by any tier in the spec are DEACTIVATED (is_active&#x3D;false)   so they stop being shown but their audit history is retained. Pass &#x60;delete_unreferenced&#x3D;true&#x60; to   hard-delete them instead.  SAFETY: - Wrap each call in its own transaction. - Will refuse if the new tracking_type is incompatible with stored cost data. - Inline-only by design — bundles are not recreated by this tool. Use pricing_structure_assign   to put the product on a bundle if that&#39;s what you want. |
| [**pricingStructureUpsert()**](CatalogApi.md#pricingStructureUpsert) | **POST** /api/v1/tools/pricing_structure_upsert | Create or edit a pricing structure&#39;s tiers, name, and tracking type. Operates in three modes:  BUNDLE MODE (structure_id provided, structure is not hidden):   Edit a shared bundle structure visible on /admin/pricing. Tiers are replaced and ALL products   linked to the bundle are re-synced. Returns how many products were affected as a warning.  INLINE MODE (product_slug or product_id provided, no structure_id):   Edit the hidden 1:1 pricing structure for a single product. Tiers are replaced and variations   are re-synced for that product only. Refuses if the product currently uses a bundle structure —   use pricing_structure_assign to detach from the bundle first.  CREATE BUNDLE MODE (no structure_id, no product_slug/product_id):   Create a new shared bundle structure. Does not link it to any products.  SAFETY RULES enforced by this tool: - Never accepts structure_id pointing to a hidden (inline) structure — always go via product_slug/product_id. - Tiers for weight/matrix types must have weight_grams &gt; 0. - Tiers for simple type: only the first tier is used; name and weight_grams are normalised. - Prices are accepted as dollar amounts (e.g. 12.99) and converted to cents internally. |
| [**productInspect()**](CatalogApi.md#productInspect) | **POST** /api/v1/tools/product_inspect | Inspect a specific product including every variation&#39;s price, compare_at_price, mix_match_tags, stock, and the tenant&#39;s mix &amp; match rule settings. Use this to audit pricing, sale state, and bundle configuration for support tickets. |
| [**productUpdateBySku()**](CatalogApi.md#productUpdateBySku) | **POST** /api/v1/tools/product_update_by_sku | Update a simple product&#39;s stock quantity and/or price by SKU — the inventory-sync path for an external POS. v1 scope: SIMPLE products only (single implicit unit, no weight/variant tiers). Every other pricing type (weight, unit, matrix, matrix_unit) is rejected with a clear message; those need per-tier/per-variant targeting that a flat SKU+quantity+price payload cannot express safely. Always call product_inspect with sku first to confirm which product/type you are targeting. |


## `catalogCollapse()`

```php
catalogCollapse($catalog_collapse_request): \ShadowSoftware\DabDash\Model\CatalogCollapse200Response
```

Merges a group of size-split products into ONE product with size options.  Turns \"Blue Dream - 3.5G\", \"Blue Dream - 7G\", \"Blue Dream - 28G\" (three separate products, each with one \"Default\" option) into a single \"Blue Dream\" product that sells by weight with 3.5g / 7g / 28g options. Stock is added together into one shared pool, and each old price becomes the price of its size.  Run catalog_flattening_audit first to see the groups and their exact base_name.  SAFETY: - dry_run defaults to TRUE. Nothing changes until you pass dry_run=false. The dry run returns   the exact sizes, prices, stock, and which products would be removed. - Past orders are never affected — they keep the product name and price the customer saw. - Refused when two products in the group claim the same size, or when any product belongs to   another store. - This removes the duplicate products. It cannot be undone from here.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$catalog_collapse_request = new \ShadowSoftware\DabDash\Model\CatalogCollapseRequest(); // \ShadowSoftware\DabDash\Model\CatalogCollapseRequest

try {
    $result = $apiInstance->catalogCollapse($catalog_collapse_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->catalogCollapse: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **catalog_collapse_request** | [**\ShadowSoftware\DabDash\Model\CatalogCollapseRequest**](../Model/CatalogCollapseRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CatalogCollapse200Response**](../Model/CatalogCollapse200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
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
    echo 'Exception when calling CatalogApi->catalogFlatteningAudit: ', $e->getMessage(), PHP_EOL;
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

## `categoryManage()`

```php
categoryManage($category_manage_request): \ShadowSoftware\DabDash\Model\CategoryManage200Response
```

List, create, update, or delete a tenant's storefront categories.  ACTIONS:   list   (default): return every category with id, name, slug, parent, sort_order,          is_active, is_featured, and image_url. Always call this first to find a          category_id before update/delete, and to check for slug collisions before create.   create: requires name (slug is auto-generated from name if omitted).   update: requires category_id. Only the fields you pass are changed.   delete: requires category_id and confirm=true. Refuses if the category still has          products or children attached (detach or reassign them first).  IMAGES: pass media_id (from media_list / media_upload) to set image_path (the final, customer-facing image) or base_image_path (the unbranded source canvas SwagImagesService composites branding onto). Omit both to leave images untouched.  Always call action=list first to confirm category_id / slug before update or delete.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$category_manage_request = new \ShadowSoftware\DabDash\Model\CategoryManageRequest(); // \ShadowSoftware\DabDash\Model\CategoryManageRequest

try {
    $result = $apiInstance->categoryManage($category_manage_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->categoryManage: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **category_manage_request** | [**\ShadowSoftware\DabDash\Model\CategoryManageRequest**](../Model/CategoryManageRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\CategoryManage200Response**](../Model/CategoryManage200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
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
    echo 'Exception when calling CatalogApi->inventoryAuditLookup: ', $e->getMessage(), PHP_EOL;
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


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
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
    echo 'Exception when calling CatalogApi->inventoryStatus: ', $e->getMessage(), PHP_EOL;
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

## `pricingStructureAssign()`

```php
pricingStructureAssign($pricing_structure_assign_request): \ShadowSoftware\DabDash\Model\PricingStructureAssign200Response
```

Assign a shared bundle pricing structure to one or more products. Re-syncs variations for each reassigned product. Automatically deletes orphaned inline (hidden) structures when replacing them.  SAFETY RULES enforced by this tool: - The target structure_id must be a BUNDLE (is_hidden=false). Inline structures cannot be assigned   to products this way — that would violate the 1:1 contract. - If a product's current structure is inline (hidden) and this product is its only consumer   (product_count == 1), the old inline structure is deleted automatically. - If a product's current structure is inline but product_count > 1, assignment is refused for that   product with an explanation — this is a data anomaly that needs manual resolution. - Each product result includes a status: assigned | skipped (already on this structure) | refused.  Use pricing_structure_list to get valid structure IDs before calling this tool.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_assign_request = new \ShadowSoftware\DabDash\Model\PricingStructureAssignRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureAssignRequest

try {
    $result = $apiInstance->pricingStructureAssign($pricing_structure_assign_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->pricingStructureAssign: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_assign_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureAssignRequest**](../Model/PricingStructureAssignRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureAssign200Response**](../Model/PricingStructureAssign200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureDelete()`

```php
pricingStructureDelete($pricing_structure_delete_request): \ShadowSoftware\DabDash\Model\PricingStructureDelete200Response
```

Delete one or more pricing structures by ID.  SAFETY RULES enforced by this tool: - BUNDLE structures (is_hidden=false): always deletable unless products are still assigned.   Pass force=true to delete even when products are assigned (use only after migrating them). - INLINE structures (is_hidden=true): only deletable when product_count=0 (orphaned).   Inline structures with products attached cannot be deleted — use pricing_structure_assign   to move the product to a bundle first, which auto-cleans the inline structure.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_delete_request = new \ShadowSoftware\DabDash\Model\PricingStructureDeleteRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureDeleteRequest

try {
    $result = $apiInstance->pricingStructureDelete($pricing_structure_delete_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->pricingStructureDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_delete_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureDeleteRequest**](../Model/PricingStructureDeleteRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureDelete200Response**](../Model/PricingStructureDelete200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureList()`

```php
pricingStructureList($pricing_structure_list_request): \ShadowSoftware\DabDash\Model\PricingStructureList200Response
```

List all pricing structures for a tenant with their kind (inline|bundle), product count, tracking type, and tier summary. Always call this before pricing_structure_upsert or pricing_structure_assign to get structure IDs and confirm which structures are bundles vs inline (1:1 product) structures.  kind=inline  → hidden 1:1 structure tied to exactly one product (is_hidden=true) kind=bundle  → shared structure visible on /admin/pricing, used by 0 or more products (is_hidden=false)

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_list_request = new \ShadowSoftware\DabDash\Model\PricingStructureListRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureListRequest

try {
    $result = $apiInstance->pricingStructureList($pricing_structure_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->pricingStructureList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_list_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureListRequest**](../Model/PricingStructureListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureList200Response**](../Model/PricingStructureList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureRestore()`

```php
pricingStructureRestore($pricing_structure_restore_request): \ShadowSoftware\DabDash\Model\PricingStructureRestore200Response
```

Surgical restore tool. Rebuilds a single product's pricing structure and variations EXACTLY to a specified state. Bypasses the standard syncVariationsForProduct routine — you control every field.  Use this AFTER inventory_audit_lookup has revealed the pre-incident state of variations whose names/prices/stock were destroyed by an erroneous bundle reassignment.  Behaviour: - Creates a NEW inline (hidden, 1:1) pricing structure with the given tracking_type, tier   definitions, and a name like \"Product: <product_name> (Hidden)\". Old structure linkage is   replaced. The previous structure is NOT deleted by this tool. - For each tier in the spec, finds-or-creates a variation. Matching is by `restore_variation_id`   if provided, else by name. If found, the variation is updated in place (preserving its id and   its audit-log history). If not found, a new variation is created. - Stock_quantity is set EXACTLY to the value specified — this is the whole point of the tool. - Sets product.tracking_type, product.inventory_mode, product.base_unit per the new structure. - Variations on the product not referenced by any tier in the spec are DEACTIVATED (is_active=false)   so they stop being shown but their audit history is retained. Pass `delete_unreferenced=true` to   hard-delete them instead.  SAFETY: - Wrap each call in its own transaction. - Will refuse if the new tracking_type is incompatible with stored cost data. - Inline-only by design — bundles are not recreated by this tool. Use pricing_structure_assign   to put the product on a bundle if that's what you want.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_restore_request = new \ShadowSoftware\DabDash\Model\PricingStructureRestoreRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureRestoreRequest

try {
    $result = $apiInstance->pricingStructureRestore($pricing_structure_restore_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->pricingStructureRestore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_restore_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureRestoreRequest**](../Model/PricingStructureRestoreRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureRestore200Response**](../Model/PricingStructureRestore200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `pricingStructureUpsert()`

```php
pricingStructureUpsert($pricing_structure_upsert_request): \ShadowSoftware\DabDash\Model\PricingStructureUpsert200Response
```

Create or edit a pricing structure's tiers, name, and tracking type. Operates in three modes:  BUNDLE MODE (structure_id provided, structure is not hidden):   Edit a shared bundle structure visible on /admin/pricing. Tiers are replaced and ALL products   linked to the bundle are re-synced. Returns how many products were affected as a warning.  INLINE MODE (product_slug or product_id provided, no structure_id):   Edit the hidden 1:1 pricing structure for a single product. Tiers are replaced and variations   are re-synced for that product only. Refuses if the product currently uses a bundle structure —   use pricing_structure_assign to detach from the bundle first.  CREATE BUNDLE MODE (no structure_id, no product_slug/product_id):   Create a new shared bundle structure. Does not link it to any products.  SAFETY RULES enforced by this tool: - Never accepts structure_id pointing to a hidden (inline) structure — always go via product_slug/product_id. - Tiers for weight/matrix types must have weight_grams > 0. - Tiers for simple type: only the first tier is used; name and weight_grams are normalised. - Prices are accepted as dollar amounts (e.g. 12.99) and converted to cents internally.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$pricing_structure_upsert_request = new \ShadowSoftware\DabDash\Model\PricingStructureUpsertRequest(); // \ShadowSoftware\DabDash\Model\PricingStructureUpsertRequest

try {
    $result = $apiInstance->pricingStructureUpsert($pricing_structure_upsert_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->pricingStructureUpsert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pricing_structure_upsert_request** | [**\ShadowSoftware\DabDash\Model\PricingStructureUpsertRequest**](../Model/PricingStructureUpsertRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\PricingStructureUpsert200Response**](../Model/PricingStructureUpsert200Response.md)

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


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
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
    echo 'Exception when calling CatalogApi->productInspect: ', $e->getMessage(), PHP_EOL;
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

## `productUpdateBySku()`

```php
productUpdateBySku($product_update_by_sku_request): \ShadowSoftware\DabDash\Model\ProductUpdateBySku200Response
```

Update a simple product's stock quantity and/or price by SKU — the inventory-sync path for an external POS. v1 scope: SIMPLE products only (single implicit unit, no weight/variant tiers). Every other pricing type (weight, unit, matrix, matrix_unit) is rejected with a clear message; those need per-tier/per-variant targeting that a flat SKU+quantity+price payload cannot express safely. Always call product_inspect with sku first to confirm which product/type you are targeting.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\DabDash\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\DabDash\Api\CatalogApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$product_update_by_sku_request = new \ShadowSoftware\DabDash\Model\ProductUpdateBySkuRequest(); // \ShadowSoftware\DabDash\Model\ProductUpdateBySkuRequest

try {
    $result = $apiInstance->productUpdateBySku($product_update_by_sku_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CatalogApi->productUpdateBySku: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **product_update_by_sku_request** | [**\ShadowSoftware\DabDash\Model\ProductUpdateBySkuRequest**](../Model/ProductUpdateBySkuRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\DabDash\Model\ProductUpdateBySku200Response**](../Model/ProductUpdateBySku200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
