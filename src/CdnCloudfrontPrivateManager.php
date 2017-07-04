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
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The cloudfront client.
   *
   * @var \Aws\CloudFront\CloudFrontClient
   */
  protected $client;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Get the Cloudfront client.
   *
   * @return \Aws\CloudFront\CloudFrontClient
   *   Client.
   */
  protected function getClient() {
    if ($this->client) {
      return $this->client;
    }
    $this->client = new CloudFrontClient([
    // Not effective, but required.
      'region' => 'us-west-2',
      'version' => '2016-09-29',
    ]);
    return $this->client;
  }

  /**
   * Returns a default policy statement.
   *
   * @return array
   *   A default policy statement.
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
   * @param array $policy
   *   The policy statement.
   * @param string $method
   *   Either cookie or url.
   * @param string $url
   *   The URL (optional if signing a cookie.)
   * @param bool $secure
   *   Whether to mark the cookie as secure.
   * @param string $path
   *   Path to apply cookie.
   *
   * @return string
   *   The URL, signed.
   */
  public function getSignedUrl(array $policy, $method, $url = NULL, $secure = TRUE, $path = '/') {
    if ($method == 'url' && is_null($url)) {
      throw new \Exception('Must specify a url if signing a URL.');
    }
    $client = $this->getClient();
    $config = $this->configFactory->get('cdn_cloudfront_private.config');
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
        setrawcookie($c, $d, NULL, $path, $domain, $secure, TRUE);
      }
    }
    else {
      $url = $client->getSignedUrl($opts);
    }
    return $url;
  }

}
