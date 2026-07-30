# MailboxInspectRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**platform** | **bool** | Inspect a platform-owned mailbox (tenant_id IS NULL). When true, tenant_slug is ignored and email_account_id is required. | [optional]
**email_account_id** | **int** | Specific EmailAccount id (required for platform&#x3D;true; optional for tenant_slug — defaults to the tenant&#39;s single mailbox). | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
