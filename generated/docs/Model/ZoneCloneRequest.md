# ZoneCloneRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**source_tenant_slug** | **string** | Tenant whose polygon zones should be cloned. |
**replace_existing** | **bool** | Replace all existing target zones; required for a full coverage clone. | [optional]
**confirm** | **bool** | Required with replace_existing&#x3D;true when applying. | [optional]
**dry_run** | **bool** | Default true; preview without writing. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
