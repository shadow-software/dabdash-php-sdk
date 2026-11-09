# ProductSearchRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**name_query** | **string** | Partial product name match (e.g. \&quot;Smalls\&quot;). Optional if category_id/slug is set. | [optional]
**category_id** | **int** | Restrict to products in this category id. | [optional]
**category_slug** | **string** | Restrict to products in this category slug (e.g. \&quot;all-flower\&quot;). | [optional]
**status** | **string** | \&quot;active\&quot;, \&quot;draft\&quot;, \&quot;archived\&quot;, or \&quot;all\&quot; (default). | [optional]
**limit** | **int** | Max results (default 50, max 100). | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
