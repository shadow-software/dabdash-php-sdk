# ProductImageAssignRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**product_id** | **int** | Product id to update. | [optional]
**media_id** | **int** | Tenant media library asset id for featured_image. | [optional]
**gallery_media_ids** | **int[]** | Tenant media library asset ids that replace gallery_images (empty array clears). | [optional]
**clear_featured** | **bool** | When true, clear featured_image. Mutually exclusive with media_id. | [optional]
**dry_run** | **bool** | Preview without writing. Defaults true. | [optional]
**overwrite** | **bool** | Replace an existing featured_image. Defaults false. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
