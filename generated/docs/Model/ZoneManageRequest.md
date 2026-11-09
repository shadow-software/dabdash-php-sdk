# ZoneManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;list\&quot; (default) or \&quot;delete\&quot;. | [optional]
**zone_ids** | **mixed[]** | List of zone IDs to delete. Required for action&#x3D;delete. | [optional]
**confirm** | **bool** | Required (true) for action&#x3D;delete. | [optional]
**force** | **bool** | If true, deletes a zone even if orders/customer addresses/delivery slots still reference it (those rows are NOT deleted — their zone_id becomes orphaned). Use only after confirming that is acceptable. | [optional]
**dry_run** | **bool** | Default true for action&#x3D;delete. Report what would happen without writing. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
