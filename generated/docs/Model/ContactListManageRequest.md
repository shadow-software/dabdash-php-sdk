# ContactListManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;list\&quot;, \&quot;show\&quot;, \&quot;create_manual\&quot;, \&quot;rename\&quot;, or \&quot;delete\&quot;. | [optional]
**list_id** | **int** | Required for show/rename/delete. | [optional]
**name** | **string** | Required for create_manual; optional for rename. | [optional]
**description** | **string** | Optional description. | [optional]
**default_type** | **string** | \&quot;lead\&quot; or \&quot;customer\&quot; (manual lists). | [optional]
**kind** | **string** | Optional filter for list: \&quot;manual\&quot; or \&quot;smart\&quot;. | [optional]
**dry_run** | **bool** | Default true for delete. | [optional]
**confirm** | **bool** | Required true to delete. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
