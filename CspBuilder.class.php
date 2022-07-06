<?php declare(strict_types=1);

enum  CspDirective {
  case Default;
  case Image;
  case Font;
  case Script;
  case Style;
}
enum CspSource {
  case Self;
  case UnsafeInline;
  case UnsafeEval;
  case Data;
  case Blob;
  case Media;
  case Frame;
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
  /**
   * Turn a CspSoruce in to a string
   *
   * @param  CspSource $source
   * @return string
   */
  private static function CspSourceString(CspSource $source) {
    return match($source) {
      CspSource::Self => "'self'",
      CspSource::UnsafeInline => "'unsafe-inline'",
      CspSource::UnsafeEval => "'unsafe-eval'",
      CspSource::Data => "data:",
      CspSource::Blob => "blob:",
      CspSource::Media => "media:",
      CspSource::Frame => "frame:"
    };
  }
  
  /**
   * Turn a CspDirective in a string
   *
   * @param  CspDirective $directive
   * @return string
   */
  private static function CspDirectiveString(CspDirective $directive) {
    return match($directive) {
      CspDirective::Default => "default-src",
      CspDirective::Image => "img-src",
      CspDirective::Font => "font-src",
      CspDirective::Script => "script-src",
      CspDirective::Style => "style-src",
    };
  }
  
  /** string $nonce nonce is caculated at each construct
  private string $nonce;
  
  /** map of directives => array of strings sources
  private array $csp_options = [];
  
  public function __construct(?bool $defaultSelf=false)
  {
    $strong = false;
    $this->nonce = base64_encode(openssl_random_pseudo_bytes( 46, $strong ));
    if( !$strong ) {
      error_log("weak random for nonce");
    }
    if( $defaultSelf ) {
      foreach(CspDirective::cases() as $directive ) {
        $this-> addCspPolicy( $directive, CspSource::Self );
     }
     
    } else
      $this->csp_options = [];
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
    $this->csp_options[ self::CspDirectiveString($directive) ] = $sources;
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
    $this->csp_options[ self::CspDirectiveString($directive) ][] = self::CspSourceString($source);
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
    $this->csp_options[ self::CspDirectiveString($directive) ][] = $source;
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
    $this->csp_options[ self::CspDirectiveString($directive) ][] = "'nonce-$this->nonce'";
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
      $result .= $directive . implode(' ', $sources) . '; ';
    
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
