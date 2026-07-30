# CustomerUpdateRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**customer_id** | **int** | The customer to update |
**name** | **string** | New display name | [optional]
**email** | **string** | New email address (must be unique within the tenant) | [optional]
**phone** | **string** | New phone number, digits or formatted | [optional]
**email_opt_out** | **bool** | Set true to suppress marketing email. Cannot be set to false — un-suppression happens in DabDash only. | [optional]
**sms_marketing_opt_out** | **bool** | Set true to suppress marketing texts. Cannot be set to false. | [optional]
**sms_notifications_muted** | **bool** | Set true to mute transactional/order SMS notifications. Cannot be set to false. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
