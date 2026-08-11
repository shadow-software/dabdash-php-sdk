# ProductUnitPriceSearchRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**unit** | **string** | Unit to price per: \&quot;g\&quot;, \&quot;oz\&quot; (default, 28g), or \&quot;lb\&quot; (448g) | [optional]
**name_query** | **string** | Optional partial product name filter | [optional]
**min_price** | **float** | Minimum price per unit in dollars (optional) | [optional]
**max_price** | **float** | Maximum price per unit in dollars (optional) | [optional]
**sort** | **string** | \&quot;asc\&quot; (cheapest first, default) or \&quot;desc\&quot; (most expensive first) | [optional]
**only_in_stock** | **bool** | When true (default), exclude out-of-stock variations | [optional]
**limit** | **int** | Max results to return (default 25, max 100) | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
