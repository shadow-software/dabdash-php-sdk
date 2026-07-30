# ProductInspectRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**product_id** | **int** | Exact product id (optional if name_query or sku supplied) | [optional]
**name_query** | **string** | Partial product name match (optional if product_id or sku supplied) | [optional]
**sku** | **string** | Exact SKU of a variation belonging to the product (optional if product_id or name_query supplied). SKUs are unique per tenant. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
