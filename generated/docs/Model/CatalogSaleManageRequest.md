# CatalogSaleManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;show\&quot; (default), \&quot;apply\&quot;, or \&quot;remove\&quot;. | [optional]
**type** | **string** | \&quot;percent\&quot; or \&quot;flat\&quot;. Required for apply. | [optional]
**amount** | **float** | Percent 0-100, or dollars off the Was price. Required for apply. | [optional]
**category_ids** | **int[]** | Category ids. Subcategories are included. Required for apply. | [optional]
**mode** | **string** | For remove: \&quot;restore\&quot; (default) puts the remembered sale back. \&quot;keep\&quot; makes the new price regular and forgets the old sale. | [optional]
**dry_run** | **bool** | Defaults true for apply and remove. | [optional]
**confirm** | **bool** | Must be true when dry_run&#x3D;false. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
