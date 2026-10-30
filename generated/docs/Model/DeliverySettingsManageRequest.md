# DeliverySettingsManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;show\&quot; (default) or \&quot;set\&quot;. | [optional]
**delivery_logistics_enabled** | **bool** |  | [optional]
**driver_self_serve_claim_enabled** | **bool** |  | [optional]
**driver_self_serve_cancel_enabled** | **bool** |  | [optional]
**driver_self_serve_swap_enabled** | **bool** |  | [optional]
**driver_self_serve_cancel_lead_minutes** | **int** | 15–10080 minutes. | [optional]
**customer_tracking_visibility** | **string** | disabled, link_only, status_and_eta, or full_with_map. | [optional]
**tracking_show_driver_photo** | **bool** |  | [optional]
**tracking_show_items** | **bool** |  | [optional]
**proof_of_delivery_requirement** | **string** | none, photo, signature, either, or both. | [optional]
**proof_retention_days** | **int** | 7–365 days. | [optional]
**driver_notifications_disabled** | **string[]** |  | [optional]
**auto_dispatch_propose_enabled** | **bool** |  | [optional]
**auto_dispatch_confirm_enabled** | **bool** |  | [optional]
**auto_dispatch_confirm_delay_minutes** | **int** | 0–120 minutes. | [optional]
**dry_run** | **bool** | Defaults to true for set. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
