# CategoryManageRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**action** | **string** | \&quot;list\&quot; (default), \&quot;create\&quot;, \&quot;update\&quot;, or \&quot;delete\&quot;. | [optional]
**category_id** | **int** | ID of an existing category. Required for update and delete. | [optional]
**name** | **string** | Category name shown to customers. Required when creating. | [optional]
**slug** | **string** | URL slug, unique per tenant. Auto-generated from name when omitted on create. | [optional]
**description** | **string** | Optional category description. | [optional]
**parent_id** | **int** | Parent category id, or null for a top-level category. | [optional]
**sort_order** | **int** | Display order (lower sorts first). | [optional]
**is_active** | **bool** | Whether the category is visible on the storefront. | [optional]
**is_featured** | **bool** | Whether the category appears in the homepage featured grid. | [optional]
**media_id** | **int** | Media library asset id to set as image_path (the branded, customer-facing image). | [optional]
**base_media_id** | **int** | Media library asset id to set as base_image_path (the unbranded source canvas). | [optional]
**confirm** | **bool** | Required (true) for action&#x3D;delete. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
