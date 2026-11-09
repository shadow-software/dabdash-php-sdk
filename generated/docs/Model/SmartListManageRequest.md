# SmartListManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;list\&quot;, \&quot;show\&quot;, \&quot;upsert\&quot;, \&quot;preview\&quot;, or \&quot;delete\&quot;. | [optional]
**list_id** | **int** | Existing Smart List id for show/update/delete/preview. | [optional]
**name** | **string** | Required for upsert create/update. | [optional]
**description** | **string** |  | [optional]
**definition** | **object** | SmartListDefinition v1 object. | [optional]
**dry_run** | **bool** |  | [optional]
**confirm** | **bool** |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
