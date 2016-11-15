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
   *
   */
  const DEFAULT_POLICY_STATEMENT = [
    'Condition' => [
      'DateLessThan' => [
        'AWS:EpochTime' => REQUEST_TIME + (60 * 60 * 5),
      ],
    ],
  ];

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config_factory;

  /**
   * The cloudfront client.
   *
   * @var CloudFrontClient
   */
  protected $client;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactory $config_factory) {
    $this->config_factory = $config_factory;
  }

  protected function getClient() {
    if ($this->client) {
      return $this->client;
    }
    $this->client = new CloudFrontClient([
      'region' => 'us-west-2', // Not effective, but required.
      'version' => '2016-09-29',
    ]);
    return $this->client;
  }

  /**
   * Get a signed URL.
   *
   * @param $url
   * @param $policy
   * @return string
   */
  public function getSignedUrl($url, $policy) {
    $client = $this->getClient();
    $config = $this->config_factory->get('cdn_cloudfront_private.config');
    return $client->getSignedUrl([
      'private_key' => $config->get('private_key'),
      'key_pair_id' => $config->get('key_pair_id'),
      'url' => $url,
      'policy' => json_encode(['Statement' => $policy]),
    ]);
  }

}
