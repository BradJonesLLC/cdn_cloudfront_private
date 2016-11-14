<?php

namespace Drupal\cdn_cloudfront_private;

use Aws\CloudFront\CloudFrontClient;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class CdnCloudfrontPrivateManager.
 *
 * @package Drupal\cdn_cloudfront_private
 */
class CdnCloudfrontPrivateManager {

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config_factory;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactory $config_factory) {
    $this->config_factory = $config_factory;
    $config = $this->config_factory->get('cdn_cloudfront_private.config');
    $this->client = new CloudFrontClient([
      'private_key' => $config->get('private_key'),
      'key_pair_id' => $config->get('key_pair_id'),
    ]);
  }

  /**
   * Get a signed URL.
   *
   * @param $url
   * @param $policy
   * @return string
   */
  public function getSignedUrl($url, $policy) {
    return $this->client->getSignedUrl([
      'url' => $url,
      'policy' => json_encode($policy),
    ]);
  }

}
