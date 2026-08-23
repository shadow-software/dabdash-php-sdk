# ProductImageStrainAssignRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**assignments** | [**\ShadowSoftware\DabDash\Model\ProductImageStrainAssignRequestAssignmentsInner[]**](ProductImageStrainAssignRequestAssignmentsInner.md) | Explicit product_id/strain_id pairs to assign. Mutually exclusive with auto_match. | [optional]
**auto_match** | **bool** | When true, find and assign matches automatically instead of using assignments. | [optional]
**search** | **string** | auto_match only: filter candidate products by name. | [optional]
**limit** | **int** | auto_match only: max products to consider. Default 25, max 100. | [optional]
**dry_run** | **bool** | Preview without writing. Defaults true. | [optional]
**overwrite** | **bool** | Replace an existing featured_image. Defaults false. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
