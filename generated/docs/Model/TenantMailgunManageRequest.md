# TenantMailgunManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;show\&quot; (default), \&quot;connect\&quot;, \&quot;disconnect\&quot;, \&quot;sync\&quot;, \&quot;add_domain\&quot;, \&quot;verify_domain\&quot;, \&quot;autoconfigure\&quot;, or \&quot;bootstrap_smtp\&quot;. | [optional]
**api_key** | **string** | Mailgun private API key (connect only; write-only). | [optional]
**region** | **string** | Mailgun region: us or eu. Defaults to us. | [optional]
**domain** | **string** | Sending name FQDN for add_domain / verify_domain / bootstrap_smtp. | [optional]
**domain_id** | **int** | Local tenant_mailgun_domains id for verify_domain / bootstrap_smtp. | [optional]
**force_smtp** | **bool** | When true, bootstrap_smtp overwrites complete non-Mailgun SMTP. | [optional]
**dry_run** | **bool** | Default true for mutating actions. | [optional]
**confirm** | **bool** | Must be true when applying connect, disconnect, autoconfigure, or bootstrap_smtp with force_smtp. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
