# TenantBrandingManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;show\&quot; (default) or \&quot;set\&quot;. | [optional]
**media_id** | **int** | Library asset to use as the store logo. Required to change the logo. | [optional]
**hero_media_id** | **int** | Library asset to use as the homepage hero (settings.hero_image_path). | [optional]
**clear_hero** | **bool** | When true, remove the homepage hero. Mutually exclusive with hero_media_id. | [optional]
**source_url** | **string** | Public http(s) URL of a BIMI SVG. Provide exactly one BIMI source. | [optional]
**source_path** | **string** | Local file path of a BIMI SVG. Provide exactly one BIMI source. | [optional]
**source_base64** | **string** | Base64-encoded BIMI SVG bytes. Provide exactly one BIMI source. | [optional]
**dry_run** | **bool** | If true (the default for set), report only and write nothing. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
