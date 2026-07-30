# CouponUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**coupon_id** | **int** | ID of an existing coupon to update. Omit to create a new coupon. | [optional]
**code** | **string** | Coupon code customers enter at checkout (case-insensitive, stored uppercase). Required when creating. | [optional]
**type** | **string** | \&quot;percentage\&quot;, \&quot;fixed\&quot;, or \&quot;free_delivery\&quot;. Required when creating. | [optional]
**value** | **float** | Percent 0-100 for \&quot;percentage\&quot;; dollars for \&quot;fixed\&quot;; ignored for \&quot;free_delivery\&quot;. Required when creating (pass 0 for free_delivery). | [optional]
**min_order** | **float** | Minimum order subtotal in dollars required to use the coupon. Omit or 0 for no minimum. | [optional]
**max_uses** | **int** | Total redemption cap across all customers. Omit/null for unlimited. | [optional]
**max_uses_per_customer** | **int** | Redemption cap per customer, matched via limit_match_by. Defaults to 1. | [optional]
**limit_match_by** | **string** | \&quot;email\&quot;, \&quot;phone\&quot;, or \&quot;both\&quot; — how max_uses_per_customer is enforced. \&quot;phone\&quot;/\&quot;both\&quot; require the tenant&#39;s phone-at-checkout setting to be on. | [optional]
**applies_to** | **string** | \&quot;all\&quot;, \&quot;products\&quot;, or \&quot;categories\&quot;. Only \&quot;all\&quot; is fully wired today — see tool description. | [optional]
**is_active** | **bool** | Whether the coupon is live. Defaults to true on create; left unchanged on update unless passed. | [optional]
**starts_at** | **string** | Optional start date (tenant timezone, stored as UTC start-of-day). Pass null to clear. | [optional]
**expires_at** | **string** | Optional expiry date (tenant timezone, stored as UTC end-of-day). Must be on/after starts_at. Pass null to clear. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
