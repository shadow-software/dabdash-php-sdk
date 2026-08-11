# CouponUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**coupon_id** | **int** | ID of an existing coupon to update. Omit to create a new coupon. | [optional]
**code** | **string** | Coupon code customers enter at checkout (case-insensitive, stored uppercase). Required when creating. | [optional]
**type** | **string** | \&quot;percentage\&quot;, \&quot;fixed\&quot;, or \&quot;free_delivery\&quot;. Required when creating. | [optional]
**value** | **float** | Percent 0-100 for \&quot;percentage\&quot;; dollars for \&quot;fixed\&quot;; ignored for \&quot;free_delivery\&quot;. Required when creating (pass 0 for free_delivery). | [optional]
**min_order** | **float** | Minimum order subtotal in dollars required to use the coupon. Omit or 0 for no minimum. | [optional]
**freebie_id** | **int** | ID of a Freebie (from freebie_list) to link, making this a code-triggered freebie. Pass null to unlink. | [optional]
**max_uses** | **int** | Total redemption cap across all customers. Omit/null for unlimited. | [optional]
**max_uses_per_customer** | **int** | Redemption cap per customer, matched via limit_match_by. Defaults to 1. | [optional]
**limit_match_by** | **string** | \&quot;email\&quot;, \&quot;phone\&quot;, or \&quot;both\&quot; — how max_uses_per_customer is enforced. \&quot;phone\&quot;/\&quot;both\&quot; require the tenant&#39;s phone-at-checkout setting to be on. | [optional]
**applies_to** | **string** | \&quot;all\&quot;, \&quot;products\&quot;, or \&quot;categories\&quot;. See tool description for applies_to_ids. | [optional]
**applies_to_ids** | **int[]** | Product ids (applies_to&#x3D;\&quot;products\&quot;) or category ids (applies_to&#x3D;\&quot;categories\&quot;) this coupon is scoped to. All must belong to this tenant. Pass [] to clear. | [optional]
**requires_subscribed_customer** | **bool** | Newsletter-subscriber-exclusive code: when true, only a registered customer who has NOT opted out of marketing email (email_opt_out&#x3D;false) can redeem it. Gates to store customers only — never platform leads. Guests are always rejected. Defaults to false. | [optional]
**subscription_ladder_id** | **int** | ID of an existing SubscriptionDiscountLadder to link. Pass null to unlink. See tool description. | [optional]
**ladder_start_percent** | **int** | Mounting ladder: percent off for orders before start_order_index. 0-100. See tool description. | [optional]
**ladder_step_percent** | **int** | Mounting ladder: percent the discount climbs each order once past start_order_index. 0-100. See tool description. | [optional]
**ladder_cap_percent** | **int** | Mounting ladder: ceiling percent the discount pegs at once reached. 0-100. See tool description. | [optional]
**ladder_start_order_index** | **int** | Mounting ladder: 1-based order number the climb begins at. See tool description. | [optional]
**is_active** | **bool** | Whether the coupon is live. Defaults to true on create; left unchanged on update unless passed. | [optional]
**starts_at** | **string** | Optional start date (tenant timezone, stored as UTC start-of-day). Pass null to clear. | [optional]
**expires_at** | **string** | Optional expiry date (tenant timezone, stored as UTC end-of-day). Must be on/after starts_at. Pass null to clear. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
