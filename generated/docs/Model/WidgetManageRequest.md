# WidgetManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;list\&quot; (default), \&quot;create\&quot;, \&quot;update\&quot;, or \&quot;delete\&quot;. | [optional]
**widget_id** | **int** | ID of an existing widget. Required for update and delete. | [optional]
**title** | **string** | Widget headline. Required when creating. | [optional]
**subtitle** | **string** | Widget sub-label shown under the headline. | [optional]
**cta_text** | **string** | Call-to-action button text (e.g. \&quot;Shop Now\&quot;). | [optional]
**link_type** | **string** | \&quot;product\&quot;, \&quot;category\&quot;, \&quot;mix_match\&quot;, or \&quot;featured\&quot;. See LINK_TYPE in the tool description. | [optional]
**product_id** | **int** | Target product id when link_type&#x3D;product. | [optional]
**category_id** | **int** | Target category id when link_type&#x3D;category. | [optional]
**mix_match_tag** | **string** | Target mix &amp; match tag when link_type&#x3D;mix_match. | [optional]
**sort_order** | **int** | Display order (lower sorts first). | [optional]
**is_active** | **bool** | Whether the widget is visible on the storefront. | [optional]
**media_id** | **int** | Media library asset id to set as image_path (the branded, customer-facing image). | [optional]
**base_media_id** | **int** | Media library asset id to set as base_image_path (the unbranded source canvas). | [optional]
**confirm** | **bool** | Required (true) for action&#x3D;delete. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
