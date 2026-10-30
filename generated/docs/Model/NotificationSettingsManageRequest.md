# NotificationSettingsManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;show\&quot; (default) or \&quot;set\&quot;. | [optional]
**notify_new_order** | **bool** |  | [optional]
**notify_new_order_push** | **bool** |  | [optional]
**notify_low_stock** | **bool** |  | [optional]
**notify_low_stock_push** | **bool** |  | [optional]
**notify_low_stock_digest** | **bool** |  | [optional]
**low_stock_digest_time** | **string** | 24-hour HH:MM. | [optional]
**low_stock_sold_window_days** | **int** | 1–180 days. | [optional]
**notify_restock_request** | **bool** |  | [optional]
**notify_new_enquiry** | **bool** | Contact form and enquiry email together. Off hides the storefront contact form and stops the email. | [optional]
**dry_run** | **bool** | Defaults to true for set. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
