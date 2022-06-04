# php-csp-builder
Simple Content-Security-Policy builder

# Interface
 * `public function addCspPolicies(string $resource, array $values): CspBuilder`
 * `public function addCspPolicy(string $resource, string $value): CspBuilder`
 * `public function addCspPolicyNonce(string $resource) : CspBuilder`
 * `public function getNonce(): string`
 * `public function getCspHeader(): string`
 * `public function setCspHeader(): void`

## addCspPolicies
Adds an array of policies to a resource, well overwrite existing policies

## addCspPolicy
Adds one policy to the list for a resource

## addCspPolicyNonce
Adds the nonce to a resource. The nonce is calculated at constuction

## getNonce
Return the current nonce as string

## getCspHeader
Return a complete Content-Security-Policy string

## setCspHeader
Adds a the Content-Security-Policy to the header

# Example usage:
```php
$Csp = (new CspBuilder())
	->addCspPolicies('default-src', [CspBuilder::SELF])

	->addCspPolicy('style-src', CspBuilder::SELF)
	->addCspPolicy('style-src', CspBuilder::UNSAFE_INLINE)
	->addCspPolicy('style-src', "http://fonts.googleapis.com")

	->addCspPolicy('img-src', CspBuilder::SELF)
	->addCspPolicy('img-src', CspBuilder::DATA)

	->addCspPolicy('font-src', "http://fonts.gstatic.com")

	->addCspPolicyNonce('script-src')
	->addCspPolicy('script-src', CspBuilder::SELF)
	->addCspPolicy('script-src', "http://code.highcharts.com")
	->addCspPolicy('script-src', "http://code.jquery.com")
;
```
