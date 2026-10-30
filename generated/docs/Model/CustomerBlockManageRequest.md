# CustomerBlockManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**customer_id** | **int** | Exact customer ID from customer_lookup; never guess from a name |
**action** | **string** |  |
**target** | **string** | Default both. Select email or phone explicitly if the other is missing/invalid. | [optional]
**reason** | **string** | Audit reason, 1–255 characters |
**dry_run** | **bool** | Default true: preview without changing any block | [optional]
**confirm** | **bool** | Must be true when applying an authorized mutation | [optional]
**expected_snapshot** | **string** | Snapshot from customer_block_status or preview; required when dry_run&#x3D;false | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
