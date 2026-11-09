# TenantBlogUpsertRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**post_id** | **int** | Existing tenant blog post id. Provide this or slug. | [optional]
**slug** | **string** | Existing tenant blog post slug. Provide this or post_id. | [optional]
**title** | **string** | Post title. | [optional]
**excerpt** | **string** | Short summary or teaser copy. | [optional]
**body** | **string** | Full HTML body for the post. | [optional]
**category** | **string** | Editorial category label, e.g. guides, news, tips, reviews. | [optional]
**featured_image** | **string** | Clear only: empty/null removes the featured image. http(s) URLs are refused — use media_id. Ignored when media_id is also given. | [optional]
**media_id** | **int** | Tenant media library asset id (from media_list / media_upload) to use as the featured image. Required to set a photo. | [optional]
**meta_title** | **string** | SEO title tag text. | [optional]
**meta_description** | **string** | SEO meta description text. | [optional]
**og_image** | **string** | Absolute URL for the Open Graph image. | [optional]
**is_published** | **bool** | Publish or unpublish the post. | [optional]
**dry_run** | **bool** | Preview without writing. Defaults true. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
