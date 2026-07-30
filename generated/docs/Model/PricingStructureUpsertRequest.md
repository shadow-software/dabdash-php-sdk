# PricingStructureUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**structure_id** | **int** | ID of an existing BUNDLE structure to update. Must NOT be a hidden (inline) structure. Omit to create a new bundle or to edit inline via product_slug/product_id. | [optional]
**product_slug** | **string** | Product slug for inline (1:1) mode. Identifies which product&#39;s hidden structure to edit. Mutually exclusive with structure_id. | [optional]
**product_id** | **int** | Product id for inline (1:1) mode. Alternative to product_slug. Mutually exclusive with structure_id. | [optional]
**name** | **string** | Structure name. Required when creating a bundle. Ignored for inline structures (name is derived from the product name). | [optional]
**tracking_type** | **string** | Pricing type: simple, weight, unit, matrix, or matrix_unit. Required when creating. For updates, changing tracking_type re-syncs all linked products. | [optional]
**tiers** | **mixed[]** | Replacement tier list. Each tier: {name (string, required), weight_grams (number, required for weight/matrix), price (dollars, required), compare_at_price (dollars, nullable), cost_price (dollars, nullable), mix_match_tags (array of strings, nullable)}. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
