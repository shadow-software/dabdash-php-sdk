# TenantSmtpManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;show\&quot; (default) or \&quot;set\&quot;. | [optional]
**smtp_host** | **string** | SMTP hostname. | [optional]
**smtp_port** | **int** | SMTP port; 25 is not allowed. | [optional]
**smtp_username** | **string** | SMTP authentication username. | [optional]
**smtp_password** | **string** | SMTP password; write-only and never returned. | [optional]
**smtp_encryption** | **string** | SMTP encryption: tls or ssl. | [optional]
**smtp_from_address** | **string** | RFC-valid sender address authorized by the provider. | [optional]
**smtp_from_name** | **string** | Displayed sender name. | [optional]
**subscription_upsell_enabled** | **bool** | Optional. When false, hides Subscribe &amp; Save upsell emails/UI for this tenant. | [optional]
**dry_run** | **bool** | Default true for set. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
