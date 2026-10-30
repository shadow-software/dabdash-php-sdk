# BundleRulePreviewRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**bundle_id** | **int** | Preview this bundle&#39;s currently stored rule. Omit when previewing an inline &#x60;rules&#x60; list instead. | [optional]
**rules** | **mixed[]** | Hypothetical rule to preview (not saved). Each item: {field, operator, value} — same shape as bundle_upsert&#39;s &#x60;rules&#x60;. Required when bundle_id is omitted. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
