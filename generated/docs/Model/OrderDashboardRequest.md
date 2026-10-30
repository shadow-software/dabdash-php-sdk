# OrderDashboardRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**order_number** | **string** | Exact order number (optional) | [optional]
**customer_email** | **string** | Exact customer email (optional) | [optional]
**customer_phone** | **string** | Customer phone number, digits or formatted (optional) | [optional]
**customer_name** | **string** | Partial customer name match (optional) | [optional]
**status** | **string** | Filter by order status | [optional]
**payment_status** | **string** | Filter by payment status | [optional]
**date_from** | **string** | Start created date filter (YYYY-MM-DD format) | [optional]
**date_to** | **string** | End created date filter (YYYY-MM-DD format) | [optional]
**updated_since** | **string** | ISO-8601 timestamp; only orders updated at or after this time (optional) | [optional]
**min_total** | **int** | Minimum order total in cents | [optional]
**limit** | **int** | Number of orders to return (default 25, max 100) | [optional]
**cursor** | **string** | Opaque next_cursor from the previous response; omit to start at the newest order | [optional]
**include_items** | **bool** | Include order line items for sync/export use (default false) | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
