# CampaignRecipientsRequeueRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**campaign_id** | **int** | ID of the campaign to unstick. |
**include_failed** | **bool** | Reset failed recipients to pending. Default true. | [optional]
**reset_stale_sending** | **bool** | Reset stuck status&#x3D;sending recipients to pending. Default true. | [optional]
**retry_sending_log_errors** | **bool** | Retry recipients listed in the campaign sending error log (retryable types only). Default false. | [optional]
**dispatch_limit** | **int** | Max immediate send jobs when retry_sending_log_errors&#x3D;true (default 200, max 500). | [optional]
**dry_run** | **bool** | If true (default), report counts without writing. Pass false to apply. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
