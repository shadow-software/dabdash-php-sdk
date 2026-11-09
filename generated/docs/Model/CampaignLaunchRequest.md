# CampaignLaunchRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**campaign_id** | **int** | Campaign id. |
**action** | **string** | \&quot;send_now\&quot;, \&quot;schedule\&quot;, or \&quot;cancel\&quot;. |
**scheduled_at** | **string** | Required for schedule: local datetime in the store timezone (e.g. \&quot;2026-11-10 09:00\&quot;). | [optional]
**dry_run** | **bool** | Default true. | [optional]
**confirm** | **bool** | Must be true to apply (with dry_run&#x3D;false). | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
