# CampaignUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**campaign_id** | **int** | ID of an existing campaign to edit. Omit to create a new draft. | [optional]
**name** | **string** | Internal campaign name (max 120 chars). Required when creating. | [optional]
**channel** | **string** | \&quot;email\&quot; (default) or \&quot;sms\&quot; (text blast). Only set on create. | [optional]
**subject** | **string** | Email subject line shown to recipients (max 200 chars). Required when creating an email campaign. | [optional]
**fallback_first_name** | **string** | Stand-in when {{first_name}} is blank (max 80). Empty string clears. Omit on update to keep. | [optional]
**html_body** | **string** | Full email HTML. Sanitized on save. Use {{unsubscribe_url}}, {{first_name}}, {{last_name}} tokens. | [optional]
**sms_body** | **string** | Text message body for SMS campaigns (max 1600 chars). Required when creating a text campaign. | [optional]
**plain_body** | **string** | Optional plain-text alternative. Auto-derived from the HTML at send time if omitted. | [optional]
**mode** | **string** | \&quot;smtp\&quot; (default), \&quot;webhook\&quot;, or \&quot;mailgun\&quot; (HTTP API with domain rotation). Left unchanged on update unless passed. | [optional]
**mailgun_domain_ids** | **int[]** | When mode is mailgun: Ready tenant_mailgun_domains ids to round-robin. Required when setting mode&#x3D;mailgun. | [optional]
**audience_includes_customers** | **bool** | Whether to include the tenant&#39;s customers in the audience. Defaults to true on create. | [optional]
**contact_list_ids** | **int[]** | Manual or Smart List ids to attach. Pass [] to clear. Omit to leave unchanged on update. | [optional]
**mailgun_pace** | **string** | When mode is mailgun: \&quot;drip\&quot; (default) or \&quot;blast\&quot;. | [optional]
**saved_webhook_id** | **int** | When mode is webhook: saved TenantWebhook id. Prefer this over inline webhook_url. | [optional]
**webhook_url** | **string** | Inline webhook URL when mode&#x3D;webhook and no saved_webhook_id. | [optional]
**webhook_health_url** | **string** | Optional HTTPS health-check URL for webhook mode. | [optional]
**webhook_headers** | [**\ShadowSoftware\DabDash\Model\CampaignUpsertRequestWebhookHeadersInner[]**](CampaignUpsertRequestWebhookHeadersInner.md) | Custom headers for inline webhook mode (max 20). | [optional]
**throttle_per_minute** | **int** | Optional send throttle cap per minute. | [optional]
**throttle_per_hour** | **int** | Optional send throttle cap per hour. | [optional]
**send_interval_minutes** | **int** | Minutes between individual sends (1–30). | [optional]
**send_window_enabled** | **bool** | Restrict sends to a daily window. | [optional]
**send_window_from** | **int** | Send window start hour 0–23 (tenant timezone). | [optional]
**send_window_to** | **int** | Send window end hour 0–23 (tenant timezone). | [optional]
**send_window_days** | **int[]** | ISO weekdays 1–7 when send_window_enabled. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
