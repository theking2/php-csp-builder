<?php declare(strict_types=1);

enum CspDirective: String {
  case Default = "default-src";
  case Image = "img-src";
  case Font = "font-src";
  case Script = "script-src";
  case Style = "style-src";
}
enum CspSource: String {
  case Self = "'self'";
  case UnsafeInline = "'unsafe-inline'";
  case UnsafeEval = "'unsafe-eval'";
  case Data = "data:";
  case Blob = "blob:";
  case Media = "media:";
  case Frame = "frame:";
}
/**
 * CspBuilder
 * Build a Content Security Policy (CSP) header
 * Example:
 * $Csp = new CspBuilder();
 * $Csp->addCspPolicies('default-src', [CspBuilder::SELF]);
 *  ->addCspPolicy('script-src', CspBuilder::SELF);
 *  ->addCspPolicyNonce('script-src');
 */
class CspBuilder
{  
  private string $nonce;
  private array $csp_options = [];
  
  public function __construct(?bool $defaultSelf=false)
  {
    $strong = false;
    $this->nonce = base64_encode(openssl_random_pseudo_bytes( 46, $strong ));
    if( !$strong ) {
      error_log("weak random for nonce");
    }
    if( $defaultSelf ) {
      foreach( CspDirective::cases() as $directive ) {
        $this->csp_options[ $directive->value ][] = CspSource::Self-> value;
      }
    } else {
  	  $this->csp_options = [];
    }
  }  
  /**
   * Add a complete source list to the CSP
   *
   * @param  CspDirective $directive
   * @param  array $sources Array of string sources
   * @return CspBuilder for chaining
   */
  public function addCspPolicies(CspDirective $directive, array $sources): CspBuilder
  {
    $this->csp_options[ $directive-> value ] = $sources;
    return $this;
  }  
  /**
   * Add a single source to the CSP
   *
   * @param  CspDirective $directive
   * @param  CspSource $source
   * @return CspBuilder for chainning
   */
  public function addCspPolicy(CspDirective $directive, CspSource $source): CspBuilder
  {
    $this->csp_options[ $directive-> value ][] = $source-> value;
    return $this;
  }
    /**
   * Add a single url to the CSP
   *
   * @param  CspDirective $directive
   * @param  CspSource $source
   * @return CspBuilder for chainning
   */
  public function addCspPolicyUrl(CspDirective $directive, string $source): CspBuilder
  {
    $this->csp_options[ $directive-> value ][] = $source;
    return $this;
  }  
  /**
   * Add a nonce policy
   *
   * @param  CspDirective $directive
   * @return CspBuilder for chaining
   */
  public function addCspPolicyNonce(CspDirective $directive) : CspBuilder
  {
    $this->csp_options[ $directive-> value ][] = "'nonce-$this->nonce'";
    return $this;
  }
  /**
   * return the current nonce
   * @return string
   */
  public function getNonce(): string
  {
    return $this->nonce;
  }    
  /**
   * create a complete policy
   *
   * @return string
   */
  public function getCspHeader(): string
  {
    $result = '';
    foreach ($this->csp_options as $directive => $sources) {
      $result .= $directive . ' ' . implode(' ', $sources) . '; ';
    
    }
    return $result;
  }  
  /**
   * setCspHeader
   * Side effect set the header in the current request
   *
   * @return CspBuilder
   */
  public function setCspHeader(): CspBuilder
  {
    header('Content-Security-Policy: ' . $this->getCspHeader());

    return $this;
  }

}
