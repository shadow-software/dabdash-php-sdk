# ProductProfitabilityRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**date_from** | **string** | Start date YYYY-MM-DD (default: 90 days ago in tenant TZ). | [optional]
**date_to** | **string** | End date YYYY-MM-DD (default: today in tenant TZ). | [optional]
**sort** | **string** | \&quot;margin\&quot; (default), \&quot;revenue\&quot;, or \&quot;units\&quot;. | [optional]
**limit** | **int** | Max products to return (default 20, max 50). | [optional]
**min_margin_percent** | **float** | Threshold for promo_headroom_ok (default 25). | [optional]
**only_in_stock** | **bool** | When true, drop out-of-stock catalog products (default false). | [optional]
**require_cost** | **bool** | When true (default), only lines with cost_price &gt; 0 are ranked. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
