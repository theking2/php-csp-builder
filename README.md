# PHP CspBuilder
Ultimate Content-Security-Policy builder in PHP. The CspSource and CspDirective enum utilize the string backed enums to protect you project from typos. Garantued to work on all browsers. Add security to your PHP based site now.

# Interface
 * `public function addCspPolicy(CspDIrective $directive, CspSource $source): CspBuilder`
 * `public function addCspPolicyUrl(CspDIrective $directive, string $source): CspBuilder`
 * `public function addCspPolicyNonce(CspDirective $directive) : CspBuilder`
 * `public function getNonce(): string`
 * `public function getCspHeader(): string`
 * `public function setCspHeader(): void`

Most function return $this therefore allowing for chaining.

## Sources
The following source identifiers are included:
 * CspSource::Self - self
 * CspSource::UnsafeInine - unsafe-inline
 * CspSource::UnsafeEval - unsafe-eval
 * CspSource::Data - data:
 * CspSource::Blob - blob:
 * CspSource::Media - media:
 * CspSource::Frame - frame:
 
"" Directives
The following directives are defined
 * CspDirective::Default - default-src
 * CspDirective::Image - img-src
 * CspDirective::Font - font-src
 * CspDirective::Script - srcipt-src
 * CspDirective::Style - style-src
 
These can be of course be shortened with this
```
use \CspDirective as CspD;
use \CspSource as CspS;
```

## addCspPolicies
Deprecated, use one of the other add functions instead. Adds an array of policies to a directive, while overwrite existing policies. 

## addCspPolicy
Adds one policy to the list for a directive. Returns a CspBuilder so can be chained.

## addCspPolicyUrl
Adds a URL to the list for a directive. Returns a CspBuilder so can be chained.

## addCspPolicyNonce
Adds the nonce to a directive. The nonce is calculated at constuction. Returns a CspBuilder so can be chained.

## getNonce
Return the current nonce as string

## getCspHeader
Return a complete Content-Security-Policy string

## setCspHeader
Adds the Content-Security-Policy to the header; call this before writing content, side-effect!

# Example usage:
```php
$Csp = (new CspBuilder(true))

	->addCspPolicy(CspDirective::Style, CspSource::UnsafeInline)
	->addCspPolicyUrl(CspDirective::Style, "http://fonts.googleapis.com")

	->addCspPolicy(CspDirective::Image, CspSource::Data)

	->addCspPolicyUrl(CspDirective::Font, "http://fonts.gstatic.com")

	->addCspPolicyNonce(CspDirective::Script)
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
