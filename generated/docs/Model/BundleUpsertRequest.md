# BundleUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**bundle_id** | **int** | ID of an existing bundle to update. Omit to create a new bundle. | [optional]
**name** | **string** | Bundle name shown to vendors and customers (e.g. \&quot;Any 4 for $77\&quot;). Required when creating. | [optional]
**quantity** | **int** | Trigger threshold — number of cart units that activates the deal. Min 2. Required when creating. | [optional]
**discount_type** | **string** | \&quot;percent\&quot;, \&quot;fixed\&quot; (per-unit dollars), or \&quot;fixed_total\&quot; (dollars for the whole set). Required when creating. | [optional]
**discount_value** | **float** | Percent 0–100 for \&quot;percent\&quot;; dollars for \&quot;fixed\&quot; and \&quot;fixed_total\&quot;. Required when creating. | [optional]
**is_active** | **bool** | Whether the bundle is live. Defaults to true on create; left unchanged on update unless passed. | [optional]
**starts_at** | **string** | Optional start datetime (tenant timezone, stored as UTC). Pass null to clear. | [optional]
**ends_at** | **string** | Optional end datetime (tenant timezone, stored as UTC). Must be on/after starts_at. Pass null to clear. | [optional]
**variation_ids** | **int[]** | Product variation IDs to apply the bundle to. Omit to leave membership unchanged. | [optional]
**variation_mode** | **string** | How to apply variation_ids: \&quot;replace\&quot; (default), \&quot;add\&quot;, or \&quot;detach\&quot;. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
