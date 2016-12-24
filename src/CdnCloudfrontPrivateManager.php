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

  /**
   * @return \Aws\CloudFront\CloudFrontClient
   */
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
   * Returns a default policy statement.
   *
   * @return array
   */
  public function getDefaultPolicyStatement() {
    return [
      'Resource' => 'https://*',
      'Condition' => [
        'DateLessThan' => [
          'AWS:EpochTime' => REQUEST_TIME + (60 * 60 * 5),
        ],
      ],
    ];
  }

  /**
   * Sign a URL, or generate signed cookies for the policy.
   *
   * @param $policy array The policy statement.
   * @param $method string Either cookie or url
   * @param $url string The URL (optional if signing a cookie.)
   * @param $secure bool Whether to mark the cookie as secure
   *
   * @return string
   */
  public function getSignedUrl($policy, $method, $url = NULL, $secure = TRUE) {
    if ($method == 'url' && is_null($url)) {
      throw new \Exception('Must specify a url if signing a URL.');
    }
    $client = $this->getClient();
    $config = $this->config_factory->get('cdn_cloudfront_private.config');
    $opts = [
      'private_key' => $config->get('private_key'),
      'key_pair_id' => $config->get('key_pair_id'),
      'url' => $url,
      'policy' => json_encode(['Statement' => [$policy]], JSON_UNESCAPED_SLASHES),
    ];
    if ($method == 'cookie') {
      if (empty($policy['Resource'])) {
        throw new \Exception('AWS signed cookies require a resource to be specified.');
      }
      $cookies = $client->getSignedCookie($opts);
      $domain = $config->get('domain');
      foreach ($cookies as $c => $d) {
        setrawcookie($c, $d, NULL, '/', $domain, $secure, TRUE);
      }
    }
    else {
      $url = $client->getSignedUrl($opts);
    }
    return $url;
  }

}
