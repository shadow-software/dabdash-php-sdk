# OrderDashboardRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**order_number** | **string** | Exact order number (optional) | [optional]
**customer_email** | **string** | Exact customer email (optional) | [optional]
**customer_phone** | **string** | Customer phone number, digits or formatted (optional) | [optional]
**status** | **string** | Filter by order status: pending, confirmed, preparing, out_for_delivery, delivered, cancelled | [optional]
**payment_status** | **string** | Filter by payment status: pending, paid, failed, refunded | [optional]
**date_from** | **string** | Start date filter (YYYY-MM-DD format) | [optional]
**date_to** | **string** | End date filter (YYYY-MM-DD format) | [optional]
**min_total** | **int** | Minimum order total in cents | [optional]
**limit** | **int** | Number of orders to return (default 25, max 100) | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
