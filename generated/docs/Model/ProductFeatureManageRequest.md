# ProductFeatureManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;list\&quot; (default) or \&quot;set\&quot;. | [optional]
**section** | **string** | \&quot;featured\&quot; (default), \&quot;deals\&quot;, or \&quot;new\&quot;. | [optional]
**product_ids** | **int[]** | Product ids to update. Required for action&#x3D;set. | [optional]
**featured** | **bool** | Value to set on the section flag for product_ids. Required for action&#x3D;set. | [optional]
**starts_at** | **string** | Optional start date (YYYY-MM-DD, tenant timezone). Null/omit &#x3D; now. Only used when featured&#x3D;true. | [optional]
**ends_at** | **string** | Optional end date (YYYY-MM-DD, tenant timezone, end of day UTC). Null/omit &#x3D; never. Must be on/after starts_at. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
