# CampaignApplyTemplateRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**campaign_id** | **int** | ID of the DRAFT campaign to apply the template to. |
**template_id** | **string** | System template id (e.g. \&quot;summer-new-arrivals\&quot;). Omit with use_month_default&#x3D;true to apply this month&#39;s calendar default. | [optional]
**use_month_default** | **bool** | When true and template_id is omitted, applies the month_calendar default for month (or the current month in the tenant timezone). | [optional]
**month** | **int** | Calendar month 1-12 for month_calendar lookup when use_month_default is true. Defaults to the current month. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
