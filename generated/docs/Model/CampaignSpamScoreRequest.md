# CampaignSpamScoreRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**campaign_id** | **int** | Load content from this campaign. Omit to score inline subject/html_body or sms_body. | [optional]
**channel** | **string** | \&quot;email\&quot; or \&quot;sms\&quot;. Required when scoring inline content without campaign_id. | [optional]
**subject** | **string** | Email subject (inline mode). | [optional]
**html_body** | **string** | Email HTML body (inline mode). | [optional]
**sms_body** | **string** | SMS body (inline mode). | [optional]
**for_send** | **bool** | If true, fold the send-time deep check into the same 0–100 score (email only). Default false (live rules). | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
