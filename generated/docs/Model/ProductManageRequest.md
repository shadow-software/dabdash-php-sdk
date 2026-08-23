# ProductManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;create\&quot; (default). | [optional]
**name** | **string** | Product name shown to customers. Required for create. | [optional]
**price** | **float** | Unit price in dollars (e.g. 24.99). Omit to leave $0 until edited. | [optional]
**sku** | **string** | SKU for the default variation. | [optional]
**stock_quantity** | **float** | On-hand unit count. Omit to leave unset. | [optional]
**category_ids** | **int[]** | Storefront category ids to attach. | [optional]
**description** | **string** | Product description. Strain enrich fills this when omitted. | [optional]
**strain_id** | **int** | Platform strain id from strain_lookup. Copies strain data onto the product. | [optional]
**skip_strain_enrich** | **bool** | If true, do not auto-match or apply a strain from the product name. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
