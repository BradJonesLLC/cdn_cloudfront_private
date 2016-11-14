<?php

namespace Drupal\cdn_cloudfront_private\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class CdnCloudfrontPrivateEvent
 *
 * An event for determining the cloudfront protection status of a uri.
 */
class CdnCloudfrontPrivateEvent extends Event {

  /**
   * The uri to examine.
   *
   * @var string
   */
  protected $uri;

  /**
   * Whether to protect and sign the uri.
   *
   * @var bool
   */
  protected $protected = FALSE;

  /**
   * A policy statement to apply to the signature.
   *
   * @var array
   */
  protected $policyStatement;

  /**
   * Whether the page should be cacheable after altering the uri.
   *
   * @var bool
   */
  protected $pageCacheable = FALSE;

  /**
   * @inheritDoc
   */
  public function __construct($uri) {
    $this->uri = $uri;
    $this->policyStatement = [
      'Condition' => [
        'DateLessThan' => [
          'AWS:EpochTime' => REQUEST_TIME + (60 * 60 * 5),
        ],
      ],
    ];
  }

  /**
   * Get the page cacheable status.
   *
   * @return boolean
   */
  public function isPageCacheable() {
    return $this->pageCacheable;
  }

  /**
   * Set the page cacheable status.
   *
   * @param boolean $pageCacheable
   */
  public function setPageCacheable($pageCacheable) {
    if (!is_bool($pageCacheable)) {
      throw new \Exception('Cacheable value must be a boolean.');
    }
    $this->pageCacheable = $pageCacheable;
  }

  /**
   * Get the uri to be tested.
   *
   * @return string
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * Return whether the uri is marked as protected.
   *
   * @return bool
   */
  public function isProtected() {
    return $this->protected;
  }

  /**
   * Set the protection status.
   *
   * @param bool $protected
   * @throws \Exception
   */
  public function setProtected($protected = TRUE) {
    if (!is_bool($protected)) {
      throw new \Exception('Protected value must be a boolean.');
    }
    $this->protected = $protected;
  }

  /**
   * Get the current policy statement.
   *
   * @return array
   */
  public function getPolicyStatement() {
    return $this->policyStatement;
  }

  /**
   * Set the policy statement.
   *
   * @param $statement
   * @throws \Exception
   */
  public function setPolicyStatement($statement) {
    if (!is_array($statement)) {
      throw new \Exception('Policy statement must be an array.');
    }
  }

}
