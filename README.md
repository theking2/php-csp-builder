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
$Csp = (new CspBuilder(true))

	->addCspPolicy(CspDirective::Style, CspSource::_Self)
	->addCspPolicy(CspDirective::Style, CspSource::UnsafeInline)
	->addCspPolicyUrl(CspDirective::Style, "http://fonts.googleapis.com")

	->addCspPolicy(CspDirective::Image, CspSource::_Self)
	->addCspPolicy(CspDirective::Image, CspSource::Data)

	->addCspPolicyUrl(CspDirective::Font, "http://fonts.gstatic.com")

	->addCspPolicyNonce(CspDirective::Script)
	->addCspPolicy(CspDirective::Script, CspSource::_Self)
	->addCspPolicyUrl(CspDirective::Script, "http://code.highcharts.com")
	->addCspPolicyUrl(CspDirective::Script, "http://code.jquery.com")
	
	// set the http header
	->setCspHeader();

// save the nonce
$nonce = $Csp->getNonce();
```

Use this for inline scripts as 
```js
<script nonce="<?=$nonce?>">
"use strict;
//some inline code
</script>
```
