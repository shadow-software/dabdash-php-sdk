# CampaignLinkAuditRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**campaign_id** | **int** | Draft/campaign id to audit. Required unless html_body is passed. | [optional]
**html_body** | **string** | Optional HTML override (e.g. a body you are about to upsert). | [optional]
**check_http** | **bool** | When true, HEAD/GET each absolute storefront URL (default false). | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
