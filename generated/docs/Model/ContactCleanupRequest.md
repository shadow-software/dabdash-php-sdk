# ContactCleanupRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**statuses** | **string[]** | Which validation statuses to purge. Defaults to [\&quot;invalid\&quot;]. Only \&quot;invalid\&quot; and \&quot;risky\&quot; are allowed; \&quot;unknown\&quot; is refused because those contacts are simply not graded yet. | [optional]
**list_id** | **int** | Limit the purge to one contact list. Omit to sweep every list in the store. | [optional]
**dry_run** | **bool** | Defaults to TRUE — reports what would be deleted without deleting. Pass false to actually delete. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
