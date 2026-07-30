# PricingStructureRestoreRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**product_id** | **int** | The product whose pricing structure and variations will be rebuilt. |
**tracking_type** | **string** | Tracking type for the new inline structure: simple, unit, weight, matrix, matrix_unit. |
**inventory_mode** | **string** | Inventory mode override: \&quot;product\&quot; or \&quot;variation\&quot;. If omitted, defaults are applied per tracking_type rules (weight→product, matrix→variation). | [optional]
**product_stock_quantity** | **float** | When inventory_mode&#x3D;product, the value to set on products.stock_quantity. Ignored otherwise. | [optional]
**tiers** | **mixed[]** | Array of tiers. Each: {name (string, required), price (dollars, required), compare_at_price (dollars, nullable), cost_price (dollars, nullable), weight_grams (number, required for weight/matrix), mix_match_tags (array, nullable), stock_quantity (number, required), restore_variation_id (int, optional — re-uses an existing variation row).} |
**delete_unreferenced** | **bool** | If true, hard-deletes variations not referenced in tiers. Default false (deactivates them). | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
