# PHP CspBuilder
Simple Content-Security-Policy builder in PHP. Enums protect for typos, but are currently incomplete

# Interface
 * `public function addCspPolicy(CspDIrective $directive, CspSource $source): CspBuilder`
 * `public function addCspPolicyUrl(CspDIrective $directive, string $source): CspBuilder`
 * `public function addCspPolicyNonce(CspDirective $directive) : CspBuilder`
 * `public function getNonce(): string`
 * `public function getCspHeader(): string`
 * `public function setCspHeader(): void`

Most function return $this therefore allowing for chaining.

## addCspPolicies
Deprecated, use one of the other add functions instead. Adds an array of policies to a directive, well overwrite existing policies. 

## addCspPolicy
Adds one policy to the list for a directive

## addCspPolicyUrl
Adds a URL to the list for a directive

## addCspPolicyNonce
Adds the nonce to a directive. The nonce is calculated at constuction

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
