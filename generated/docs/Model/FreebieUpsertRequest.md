# FreebieUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**freebie_id** | **int** | ID of an existing freebie to update. Omit to create a new freebie. | [optional]
**name** | **string** | Freebie name shown to vendors (e.g. \&quot;Spend $50 Get Papers\&quot;). Required when creating. | [optional]
**product_id** | **int** | The product given away. Must belong to this tenant. Required when creating. | [optional]
**variation_id** | **int** | Optional specific variation of product_id to give away. Must belong to this tenant. Pass null to clear. | [optional]
**spend_threshold** | **float** | Minimum cart spend in dollars that triggers this freebie. Required when creating. | [optional]
**quantity** | **int** | How many units of the product/variation are given away once triggered. Required when creating. | [optional]
**is_stackable** | **bool** | Whether this rule can combine with other freebie rules on the same order. Defaults to false. | [optional]
**is_active** | **bool** | Whether the freebie is live. Defaults to true on create; left unchanged on update unless passed. | [optional]
**starts_at** | **string** | Optional start date (tenant timezone, stored as UTC). Pass null to clear. | [optional]
**ends_at** | **string** | Optional end date (tenant timezone, stored as UTC). Must be on/after starts_at. Pass null to clear. | [optional]
**category_ids** | **mixed[]** | Category IDs to scope this freebie to. Must belong to this tenant; foreign ids are ignored. Omit to leave unchanged; pass an empty array to clear all. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
