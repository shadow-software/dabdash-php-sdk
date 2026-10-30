# ZoneUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**zone_id** | **int** | Existing zone to update. | [optional]
**name** | **string** | Zone name, e.g. \&quot;Halifax\&quot;. Required to create. | [optional]
**polygon** | [**\ShadowSoftware\DabDash\Model\ZoneUpsertRequestPolygonInner[]**](ZoneUpsertRequestPolygonInner.md) | Boundary points. | [optional]
**center_lat** | **float** | Circle centre latitude (with center_lng and radius_km). | [optional]
**center_lng** | **float** | Circle centre longitude. | [optional]
**radius_km** | **float** | Circle radius in km (0.1–200). | [optional]
**delivery_fee** | **float** | Delivery fee in dollars. | [optional]
**service_fee** | **float** | Service fee in dollars. | [optional]
**min_order** | **float** | Minimum order in dollars. | [optional]
**color** | **string** | Map colour hex, e.g. #3B82F6. | [optional]
**is_active** | **bool** | Whether the zone accepts orders. | [optional]
**priority** | **int** | Overlap priority (lower number wins). | [optional]
**fulfillment_type** | **string** | delivery (default), pickup, or shipping. | [optional]
**dry_run** | **bool** | Default true. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
