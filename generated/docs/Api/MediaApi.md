# ShadowSoftware\Sdk\Generated\MediaApi



All URIs are relative to https://.dabdash.com, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**mediaCompose()**](MediaApi.md#mediaCompose) | **POST** /api/v1/tools/media_compose | Build a finished campaign image ON THE SERVER: take a base picture, drop the vendor&#39;s logo on top, add a headline and subtitle, and save the result straight into the tenant&#39;s media library. Returns a media_id + public URL ready for campaign_set_image. Nothing has to be uploaded from your machine — pass IDs and URLs and the server does the artwork.  PROVIDE THE BASE (exactly one):   - base_media_id — an image already in the tenant&#39;s library (see media_list).   - base_url      — a public https image URL, e.g. the output of an image-generation tool.  PROVIDE THE LOGO (optional, at most one):   - logo_media_id / logo_url — same two options as the base.   THE LOGO IS NEVER ALTERED. It is scaled to fit (aspect ratio locked) and placed as-is —   its colours, strokes and transparency are left exactly as supplied. For legibility on busy   artwork use the &#x60;scrim&#x60; option, which darkens the area BEHIND the logo and text.  LAYOUT: logo on top, headline under it, subtitle under that — the block is centred horizontally and positioned with logo_position (top / center / bottom). Sizes are given as a fraction of the base image width so they scale with any canvas. Long text is word-wrapped automatically; the response&#39;s &#x60;wrapped&#x60; flag tells you when that happened so you can shorten it.  CONSTRAINTS: sources max 5 MB, JPEG/PNG/WebP/GIF, max 8000x8000. The finished image goes through the same pipeline as media_upload — re-encoded to WebP, resized to fit, de-duplicated by content — so identical inputs return the existing media_id instead of a duplicate.  Typical flow: (generate or pick a base image) -&gt; media_compose -&gt; campaign_set_image.  STOREFRONT SWAG MODE (category_id or widget_id): instead of the manual layout above, pass category_id or widget_id to run the platform&#39;s own storefront branding pipeline (SwagImagesService) against that exact category or widget — the same compositor used by the dev seeder and vendor admin: tenant logo eyebrow, Bebas Neue headline (shrunk to fit long names automatically), brand-colour accent rule, and a sub-label (the tenant&#39;s live domain for categories, the widget&#39;s own subtitle for widgets). Pass base_media_id or base_url to set/replace the row&#39;s base_image_path first; omit both to recompose from whatever base_image_path is already saved. Saves directly to image_path on that category or widget — this is an internal/admin tool, not exposed to vendors. |
| [**mediaList()**](MediaApi.md#mediaList) | **POST** /api/v1/tools/media_list | List the images in a tenant&#39;s media library — id, public URL, dimensions, filename, folder, and alt text. Use this to find the media_id of an image to place into a campaign with campaign_set_image, instead of guessing. Returns newest first.  Filter with &#x60;folder&#x60; (exact match) or &#x60;search&#x60; (filename substring). &#x60;visibility&#x60; defaults to \&quot;public\&quot; (the emailable assets); pass \&quot;all\&quot; or \&quot;private\&quot; to widen. Confirm the tenant_slug with tenant_list first. |
| [**mediaUpload()**](MediaApi.md#mediaUpload) | **POST** /api/v1/tools/media_upload | Upload an image into a tenant&#39;s media library (the same library the vendor admin uses). The image is ingested through the platform&#39;s shared media pipeline — re-encoded to WebP, resized to fit, EXIF- oriented, content-addressed for dedup — and a media_assets row is created. It ALWAYS lands in the library, and the tool returns the new media_id and public URL you can then place into a campaign with campaign_set_image.  PROVIDE THE IMAGE ONE OF THREE WAYS (exactly one):   - source_url    — fetch the image from a public http(s) URL.   - source_path   — read a local file path on the server/agent host.   - source_base64 — raw base64 of the image bytes (no data: prefix needed; a data: prefix is stripped).  CONSTRAINTS: max 5 MB; JPEG, PNG, WebP, or GIF. Identical bytes already in the library are de-duped (you get the existing media_id back). Uploads default to public visibility so the image is emailable.  Optional alt_text and folder help organise and caption the asset. Confirm the tenant_slug with tenant_list first. To see what&#39;s already in the library, use media_list. |


## `mediaCompose()`

```php
mediaCompose($media_compose_request): \ShadowSoftware\Sdk\Generated\Model\MediaCompose200Response
```

Build a finished campaign image ON THE SERVER: take a base picture, drop the vendor's logo on top, add a headline and subtitle, and save the result straight into the tenant's media library. Returns a media_id + public URL ready for campaign_set_image. Nothing has to be uploaded from your machine — pass IDs and URLs and the server does the artwork.  PROVIDE THE BASE (exactly one):   - base_media_id — an image already in the tenant's library (see media_list).   - base_url      — a public https image URL, e.g. the output of an image-generation tool.  PROVIDE THE LOGO (optional, at most one):   - logo_media_id / logo_url — same two options as the base.   THE LOGO IS NEVER ALTERED. It is scaled to fit (aspect ratio locked) and placed as-is —   its colours, strokes and transparency are left exactly as supplied. For legibility on busy   artwork use the `scrim` option, which darkens the area BEHIND the logo and text.  LAYOUT: logo on top, headline under it, subtitle under that — the block is centred horizontally and positioned with logo_position (top / center / bottom). Sizes are given as a fraction of the base image width so they scale with any canvas. Long text is word-wrapped automatically; the response's `wrapped` flag tells you when that happened so you can shorten it.  CONSTRAINTS: sources max 5 MB, JPEG/PNG/WebP/GIF, max 8000x8000. The finished image goes through the same pipeline as media_upload — re-encoded to WebP, resized to fit, de-duplicated by content — so identical inputs return the existing media_id instead of a duplicate.  Typical flow: (generate or pick a base image) -> media_compose -> campaign_set_image.  STOREFRONT SWAG MODE (category_id or widget_id): instead of the manual layout above, pass category_id or widget_id to run the platform's own storefront branding pipeline (SwagImagesService) against that exact category or widget — the same compositor used by the dev seeder and vendor admin: tenant logo eyebrow, Bebas Neue headline (shrunk to fit long names automatically), brand-colour accent rule, and a sub-label (the tenant's live domain for categories, the widget's own subtitle for widgets). Pass base_media_id or base_url to set/replace the row's base_image_path first; omit both to recompose from whatever base_image_path is already saved. Saves directly to image_path on that category or widget — this is an internal/admin tool, not exposed to vendors.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\MediaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$media_compose_request = new \ShadowSoftware\Sdk\Generated\Model\MediaComposeRequest(); // \ShadowSoftware\Sdk\Generated\Model\MediaComposeRequest

try {
    $result = $apiInstance->mediaCompose($media_compose_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MediaApi->mediaCompose: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **media_compose_request** | [**\ShadowSoftware\Sdk\Generated\Model\MediaComposeRequest**](../Model/MediaComposeRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\MediaCompose200Response**](../Model/MediaCompose200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `mediaList()`

```php
mediaList($media_list_request): \ShadowSoftware\Sdk\Generated\Model\MediaList200Response
```

List the images in a tenant's media library — id, public URL, dimensions, filename, folder, and alt text. Use this to find the media_id of an image to place into a campaign with campaign_set_image, instead of guessing. Returns newest first.  Filter with `folder` (exact match) or `search` (filename substring). `visibility` defaults to \"public\" (the emailable assets); pass \"all\" or \"private\" to widen. Confirm the tenant_slug with tenant_list first.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\MediaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$media_list_request = new \ShadowSoftware\Sdk\Generated\Model\MediaListRequest(); // \ShadowSoftware\Sdk\Generated\Model\MediaListRequest

try {
    $result = $apiInstance->mediaList($media_list_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MediaApi->mediaList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **media_list_request** | [**\ShadowSoftware\Sdk\Generated\Model\MediaListRequest**](../Model/MediaListRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\MediaList200Response**](../Model/MediaList200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `mediaUpload()`

```php
mediaUpload($media_upload_request): \ShadowSoftware\Sdk\Generated\Model\MediaUpload200Response
```

Upload an image into a tenant's media library (the same library the vendor admin uses). The image is ingested through the platform's shared media pipeline — re-encoded to WebP, resized to fit, EXIF- oriented, content-addressed for dedup — and a media_assets row is created. It ALWAYS lands in the library, and the tool returns the new media_id and public URL you can then place into a campaign with campaign_set_image.  PROVIDE THE IMAGE ONE OF THREE WAYS (exactly one):   - source_url    — fetch the image from a public http(s) URL.   - source_path   — read a local file path on the server/agent host.   - source_base64 — raw base64 of the image bytes (no data: prefix needed; a data: prefix is stripped).  CONSTRAINTS: max 5 MB; JPEG, PNG, WebP, or GIF. Identical bytes already in the library are de-duped (you get the existing media_id back). Uploads default to public visibility so the image is emailable.  Optional alt_text and folder help organise and caption the asset. Confirm the tenant_slug with tenant_list first. To see what's already in the library, use media_list.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure OAuth2 access token for authorization: tenantOAuth
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure Bearer authorization: tenantApiKey
$config = ShadowSoftware\Sdk\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new ShadowSoftware\Sdk\Generated\Api\MediaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$media_upload_request = new \ShadowSoftware\Sdk\Generated\Model\MediaUploadRequest(); // \ShadowSoftware\Sdk\Generated\Model\MediaUploadRequest

try {
    $result = $apiInstance->mediaUpload($media_upload_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MediaApi->mediaUpload: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **media_upload_request** | [**\ShadowSoftware\Sdk\Generated\Model\MediaUploadRequest**](../Model/MediaUploadRequest.md)|  | [optional] |

### Return type

[**\ShadowSoftware\Sdk\Generated\Model\MediaUpload200Response**](../Model/MediaUpload200Response.md)

### Authorization

[tenantOAuth](../../README.md#tenantOAuth), [tenantApiKey](../../README.md#tenantApiKey)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
