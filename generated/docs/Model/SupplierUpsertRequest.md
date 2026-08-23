# SupplierUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**supplier_id** | **int** | Existing supplier id to update. Omit to create (or match by name). | [optional]
**name** | **string** | Supplier name. Required when creating. | [optional]
**email** | **string** | Supplier email for sending purchase orders. | [optional]
**phone** | **string** | Supplier phone. | [optional]
**license_number** | **string** | License or account number if you store one. | [optional]
**notes** | **string** | Internal notes about this supplier. | [optional]
**lead_time_days** | **int** | Typical lead time in days (0–365). Defaults to 0 on create. | [optional]
**is_active** | **bool** | Whether this supplier can be used on new purchase orders. Defaults to true on create. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
