# MediaUploadRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**source_url** | **string** | Public http(s) URL to fetch the image from. Provide exactly one source. | [optional]
**source_path** | **string** | Local file path to read the image from. Provide exactly one source. | [optional]
**source_base64** | **string** | Base64-encoded image bytes (data: prefix optional). Provide exactly one source. | [optional]
**filename** | **string** | Original filename to record (e.g. \&quot;summer-flyer.png\&quot;). Defaults to a derived name. | [optional]
**alt_text** | **string** | Alt text for accessibility and captions (max 500 chars). | [optional]
**folder** | **string** | Optional library folder to group the asset under (max 100 chars). | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
