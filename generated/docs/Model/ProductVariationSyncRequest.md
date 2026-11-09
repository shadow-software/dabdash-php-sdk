# ProductVariationSyncRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**product_id** | **int** | Exact product id | [optional]
**expected_variation_count** | **int** | Exact number of rows in variations; rejects partial payloads | [optional]
**dry_run** | **bool** | Default true; set false only after vendor approval | [optional]
**variations** | **mixed[]** | Complete rows: name, price, cost_price, stock_quantity, optional id, weight_value, type, compare_at_price, mix_match_tags | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
