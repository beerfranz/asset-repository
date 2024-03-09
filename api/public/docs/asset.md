# Assets

Asset define governance, what is expected.


## Reconciliation rules

`rules` property contains rules that are evaluated to reconciliate with the asset with its instances.

An instance is linked to an asset if:
* all of the asset rules are validated
* only one asset is matched

Rules matchers:
* equal: `foo == 'bar'`
* in: `in_array(foo, ["value1", "value2"])`
* lower than: `foo < 10`, `foo <= 10`
* greater than: `foo > 10`, `foo >= 10`
* between: `foo > 1 && foo < 10`

Instance properties that can be used:
* friendlyName: `friendlyName`
* kind identifier: `kind`

## Attributes constaints

Attributes defines constraints on instances attributes.

Constraints:
* equal: `desiredValue`
* one of: `in ["value1", "value2"]`
