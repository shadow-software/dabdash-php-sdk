# ProductManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;create\&quot; (default) or \&quot;update\&quot;. | [optional]
**product_id** | **int** | Existing product id required for update. | [optional]
**name** | **string** | Product name shown to customers. Required for create. | [optional]
**price** | **float** | Unit price in dollars (e.g. 24.99). Omit to leave $0 until edited. | [optional]
**sku** | **string** | SKU for the default variation. | [optional]
**stock_quantity** | **float** | On-hand unit count. Omit to leave unset. | [optional]
**category_ids** | **int[]** | Storefront category ids to attach. | [optional]
**description** | **string** | Product description. Strain enrich fills this when omitted. | [optional]
**meta_title** | **string** | SEO title override (max 60). Empty string clears. | [optional]
**meta_description** | **string** | SEO meta description override (max 160). Empty string clears. | [optional]
**strain_id** | **int** | Platform strain id from strain_lookup. Copies strain data onto the product. | [optional]
**skip_strain_enrich** | **bool** | If true, do not auto-match or apply a strain from the product name. | [optional]
**expected_variation_count** | **int** | Required with variations: exact number of variation rows; partial payloads are rejected. | [optional]
**variations** | [**\ShadowSoftware\DabDash\Model\CatalogImportRequestProductsInnerVariationsInner[]**](CatalogImportRequestProductsInnerVariationsInner.md) | Complete named variant rows. Each row requires name, price, and stock_quantity. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
