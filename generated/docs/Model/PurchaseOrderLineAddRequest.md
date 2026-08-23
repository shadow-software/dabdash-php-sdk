# PurchaseOrderLineAddRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**purchase_order_id** | **int** | Draft purchase order id from purchase_order_draft_create. | [optional]
**product_id** | **int** | Catalog product id to order. | [optional]
**variation_id** | **int** | Size/variation id when the product uses variation inventory. | [optional]
**qty** | **float** | Quantity to order (grams or units, matching the product). | [optional]
**unit_cost** | **float** | Unit cost in dollars. Omit to use the last cost for this supplier. | [optional]
**match_confidence** | **float** | How confident the catalog match is, from 0 to 1. Pass when importing from OCR. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
