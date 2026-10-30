# LoyaltyManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;show\&quot; (default) or \&quot;set\&quot;. | [optional]
**enabled** | **bool** | Turn the loyalty program on or off. | [optional]
**points_per_dollar** | **float** | Points earned per dollar spent (0.01–1000). | [optional]
**redeem_points_per_dollar** | **int** | Points needed to redeem one dollar (1–100000). | [optional]
**skip_on_coupon** | **bool** | When true, orders using a coupon earn no points. | [optional]
**tiers_enabled** | **bool** | Enable the configured lifetime tier ladder. | [optional]
**tiers** | [**\ShadowSoftware\DabDash\Model\LoyaltyManageRequestTiersInner[]**](LoyaltyManageRequestTiersInner.md) | Lifetime tier definitions, sorted automatically by lifetime_points. | [optional]
**customer_id** | **int** | Customer ID for set_customer_lifetime. | [optional]
**lifetime_points** | **int** | Explicit lifetime points for set_customer_lifetime. | [optional]
**dry_run** | **bool** | Default true for set. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
