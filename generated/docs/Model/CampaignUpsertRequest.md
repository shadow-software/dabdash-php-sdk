# CampaignUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**campaign_id** | **int** | ID of an existing campaign to edit. Omit to create a new draft. | [optional]
**name** | **string** | Internal campaign name (max 120 chars). Required when creating. | [optional]
**channel** | **string** | \&quot;email\&quot; (default) or \&quot;sms\&quot; (text blast). Only set on create. | [optional]
**subject** | **string** | Email subject line shown to recipients (max 200 chars). Required when creating an email campaign. | [optional]
**html_body** | **string** | Full email HTML. Sanitized on save. Use {{unsubscribe_url}}, {{first_name}}, {{last_name}} tokens. | [optional]
**sms_body** | **string** | Text message body for SMS campaigns (max 1600 chars). Required when creating a text campaign. | [optional]
**plain_body** | **string** | Optional plain-text alternative. Auto-derived from the HTML at send time if omitted. | [optional]
**mode** | **string** | \&quot;smtp\&quot; (default) or \&quot;webhook\&quot;. Left unchanged on update unless passed. | [optional]
**audience_includes_customers** | **bool** | Whether to include the tenant&#39;s customers in the audience. Defaults to true on create. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
