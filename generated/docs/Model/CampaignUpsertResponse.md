# CampaignUpsertResponse

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** |  | [optional]
**campaign_id** | **int** |  | [optional]
**name** | **string** |  | [optional]
**channel** | **string** |  | [optional]
**subject** | **string** |  | [optional]
**fallback_first_name** | **string** |  | [optional]
**mode** | **string** |  | [optional]
**status** | **string** |  | [optional]
**audience_includes_customers** | **bool** |  | [optional]
**mailgun_domain_ids** | **int[]** |  | [optional]
**mailgun_pace** | **string** |  | [optional]
**contact_lists** | [**\ShadowSoftware\DabDash\Model\CampaignGetResponseContactListsInner[]**](CampaignGetResponseContactListsInner.md) |  | [optional]
**saved_webhook_id** | **int** |  | [optional]
**webhook_url** | **string** |  | [optional]
**throttle_per_minute** | **int** |  | [optional]
**throttle_per_hour** | **int** |  | [optional]
**send_interval_minutes** | **int** |  | [optional]
**send_window_enabled** | **bool** |  | [optional]
**message** | **string** |  | [optional]
**webhook_signing_secret** | **string** |  | [optional]
**sms_body_length** | **int** |  | [optional]
**html_body_length** | **int** |  | [optional]
**has_unsubscribe_token** | **bool** |  | [optional]
**spam_score** | **int** |  | [optional]
**spam_band** | **string** |  | [optional]
**spam_summary** | **string** |  | [optional]
**spam_can_send** | **bool** |  | [optional]
**spam_min_send_score** | **int** |  | [optional]
**spam_findings** | [**\ShadowSoftware\DabDash\Model\CampaignSpamScoreResponseFindingsInner[]**](CampaignSpamScoreResponseFindingsInner.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
