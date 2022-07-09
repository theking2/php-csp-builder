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
 * $Csp = (new CspBuilder())
 * 
 *  ->add(CspDirective::Default', CspSource::Self)
 *  ->add(CspDirective::Script, CspSource::Self)
 *  ->addNonce(CspSource::Script);
 * 
 * header('Content-Security-Policy: ' . $Csp);
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
   * @deprecated use the other add functions
   * @param  CspDirective $directive
   * @param  array $sources Array of string sources
   * @return CspBuilder for chaining
   */
  public function addCspPolicies(CspDirective $directive, array $sources): CspBuilder
  {
    $this->csp_options[ $directive-> value ] = $sources;
    return $this;
  }  
  /** @depricated use add instead. */
  public function addCspPolicy(): CspBuilder
  {
    return call_user_func_array([$this, 'add'], func_get_args());
  }
  /**
   * Add a single source to the CSP
   *
   * @param  CspDirective $directive
   * @param  CspSource $source
   * @return CspBuilder for chainning
   */
  public function add(CspDirective $directive, CspSource $source): CspBuilder
  {
    $this->csp_options[ $directive-> value ][] = $source-> value;
    return $this;
  }

  /** @depriecated use addUrl instead. */
  public function addCspPolicyUrl(): CspBuilder
  {
    return call_user_func_array([$this, 'addUrl'], func_get_args());
  }

   /**
   * Add a single url to the CSP
   *
   * @param  CspDirective $directive
   * @param  CspSource $source
   * @return CspBuilder for chainning
   */
  public function addUrl(CspDirective $directive, string $source): CspBuilder
  {
    $this->csp_options[ $directive-> value ][] = $source;
    return $this;
  }  
  /** @depricated use addNonce instead */
  public function addCspPolicyNonce() : CspBuilder
  {
    return call_user_func_array([$this, 'addNonce'], func_get_args());
  }

  /**
   * Add a nonce policy
   *
   * @param  CspDirective $directive
   * @return CspBuilder for chaining
   */
  public function addNonce(CspDirective $directive) : CspBuilder
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
   * @deprecated use cast to string instead
   * @return string
   */
  public function getCspHeader(): string {
    return call_user_func('__tostring', func_get_args());
  }

  /**
   * create a complete policy
   *
   * @return string
   */
  public function __toString(): string
  {
    $csp = "";
    foreach( $this->csp_options as $directive => $sources ) {
      $csp .= "$directive " . implode(' ', $sources) . "; ";
    }
    return $csp;
  }
 
 
  /**
   * setCspHeader
   * Side effect set the header in the current request
   *
   * @return CspBuilder
   */
  public function setCspHeader(): CspBuilder
  {
    header('Content-Security-Policy: ' . $this);

    return $this;
  }

}
