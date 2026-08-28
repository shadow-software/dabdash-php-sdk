# CampaignGenerateFlyerRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**theme** | **string** | What the flyer is for, in plain words. E.g. \&quot;September restock — cosy autumn mood, warm light\&quot;. |
**product_ids** | **mixed[]** | Product IDs to feature. Their photos anchor the artwork. Omit to auto-pick the newest featured products that have images. | [optional]
**campaign_id** | **int** | DRAFT campaign to place the artwork into. Omit to only add it to the media library. | [optional]
**slot_index** | **int** | 1-based image slot to fill. Defaults to 1, the hero slot. | [optional]
**aspect_ratio** | **string** | Shape of the artwork: \&quot;4:3\&quot; (default, email banner), \&quot;1:1\&quot;, \&quot;16:9\&quot;, \&quot;3:4\&quot;. Anything else falls back to the default. | [optional]
**alt_text** | **string** | Alt text for the placed image. Defaults to the theme. | [optional]
**reference_media_ids** | **mixed[]** | Media library ids of images to use as one-off style/composition references for THIS generation (e.g. a past flyer the vendor just attached to this chat — its media_id is noted right after the attachment). Max 3. Not persisted; use campaign_flyer_style_pack for a look that should repeat. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
