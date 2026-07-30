# MediaComposeRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**category_id** | **int** | Storefront swag mode: composite the platform branding onto this category via SwagImagesService instead of the manual layout below. At most one of category_id or widget_id. | [optional]
**widget_id** | **int** | Storefront swag mode: composite the platform branding onto this widget via SwagImagesService instead of the manual layout below. At most one of category_id or widget_id. | [optional]
**base_media_id** | **int** | ID of a library image to use as the background. Provide exactly one of base_media_id or base_url. | [optional]
**base_url** | **string** | Public http(s) URL of the background image. Provide exactly one of base_media_id or base_url. | [optional]
**logo_media_id** | **int** | ID of a library image to place on top, unaltered. At most one of logo_media_id or logo_url. | [optional]
**logo_url** | **string** | Public http(s) URL of the logo to place on top, unaltered. | [optional]
**headline** | **string** | Large text under the logo (max 120 chars). Wrapped to at most 2 lines. | [optional]
**subtitle** | **string** | Smaller text under the headline (max 200 chars). Wrapped to at most 3 lines. | [optional]
**font** | **string** | Typeface: anton, bebas, oswald, playfair, sans, serif. Defaults to \&quot;sans\&quot;. | [optional]
**logo_position** | **string** | Vertical placement of the whole block: \&quot;top\&quot;, \&quot;center\&quot; (default) or \&quot;bottom\&quot;. | [optional]
**logo_width_pct** | **float** | Logo width as a fraction of the base width, 0.05-0.90. Defaults to 0.30. | [optional]
**text_color** | **string** | Headline colour as #RRGGBB. Defaults to #FFFFFF. | [optional]
**subtitle_color** | **string** | Subtitle colour as #RRGGBB. Defaults to text_color. | [optional]
**headline_size_pct** | **float** | Headline size as a fraction of base width, 0.01-0.25. Defaults to 0.07. | [optional]
**subtitle_size_pct** | **float** | Subtitle size as a fraction of base width, 0.01-0.15. Defaults to 0.035. | [optional]
**scrim** | **string** | Darkening behind the logo/text for legibility: \&quot;none\&quot;, \&quot;soft\&quot; (default) or \&quot;strong\&quot;. | [optional]
**text_shadow** | **bool** | Draw a soft shadow under the text. Defaults to true. | [optional]
**filename** | **string** | Filename to record for the new asset (e.g. \&quot;natal-day-hero.png\&quot;). | [optional]
**alt_text** | **string** | Alt text for accessibility and captions (max 500 chars). | [optional]
**folder** | **string** | Library folder to group the asset under (max 100 chars). | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
