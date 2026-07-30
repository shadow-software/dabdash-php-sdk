# PricingStructureDeleteRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**structure_ids** | **mixed[]** | List of structure IDs to delete. Bundle structures are always accepted; inline structures are only accepted when product_count&#x3D;0. |
**force** | **bool** | If true, deletes even if products are still assigned (they will be orphaned — use only after migrating products). Requires orphan_products_acknowledged&#x3D;true alongside. | [optional]
**orphan_products_acknowledged** | **bool** | Explicit acknowledgment that force&#x3D;true will orphan products. Required when force&#x3D;true and any structure has product_count &gt; 0. Defaults to false. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
