<?php declare(strict_types=1);

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
  const SELF = "'self'";
  const UNSAFE_INLINE = "'unsafe-inline'";
  const UNSAFE_EVAL = "'unsafe-eval'";
  const DATA = "data:";
  const BLOB = "'blob:'";
  const MEDIA = "'media:'";
  const FRAME = "'frame:'";

  private string $nonce;
  private array $csp_options = [];
  
  public function __construct()
  {
    $strong = false;
    $this->nonce = base64_encode(openssl_random_pseudo_bytes(46,$strong));
    if( !$strong ) {
      error_log("weak random for nonce");
    }
    $this->csp_options = [];
  }  
  /**
   * Add a complete policy to the CSP
   *
   * @param  string $resource
   * @param  string $values
   * @return CspBuilder for chaining
   */
  public function addCspPolicies(string $resource, array $values): CspBuilder
  {
    $this->csp_options[$resource] = $values;
    return $this;
  }  
  /**
   * Add a single policy to the CSP
   *
   * @param  string $resource
   * @param  string $value
   * @return CspBuilder for chainning
   */
  public function addCspPolicy(string $resource, string $value): CspBuilder
  {
    $this->csp_options[$resource][] = $value;
    return $this;
  }  
  /**
   * Add a nonce policy
   *
   * @param  string $resource
   * @return CspBuilder for chaining
   */
  public function addCspPolicyNonce(string $resource) : CspBuilder
  {
    $this->csp_options[$resource][] = "'nonce-$this->nonce'";
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
    foreach ($this->csp_options as $directive => $resource) {
      $result .= "$directive " . implode(' ', $resource) . '; ';
    
    }
    return $result;
  }  
  /**
   * setCspHeader
   * Side effect set the header in the current request
   *
   * @return void
   */
  public function setCspHeader(): void
  {
    header('Content-Security-Policy: ' . $this->getCspHeader());
  }

}