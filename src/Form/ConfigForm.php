<?php

namespace Drupal\cdn_cloudfront_private\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyRepository;
use Drupal\key\KeyRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ConfigForm.
 *
 * @package Drupal\cdn_cloudfront_private\Form
 */
class ConfigForm extends ConfigFormBase {

  /**
   * The key repository.
   *
   * @var \Drupal\key\KeyRepositoryInterface
   */
  protected $keyRepository;

  /**
   * ConfigForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   */
  public function __construct(ConfigFactoryInterface $config_factory, KeyRepositoryInterface $keyRepository) {
    parent::__construct($config_factory);
    $this->keyRepository = $keyRepository;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('key.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'cdn_cloudfront_private.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cdn_cloudfront_private_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('cdn_cloudfront_private.config');
    $form['domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain to use for setting secure cookies'),
      '#default_value' => $config->get('domain'),
      '#description' => $this->t('A valid domain string for setrawcookie()'),
    ];
    $form['key'] = [
      '#type' => 'select',
      '#options' => $this->keyRepository->getKeyNamesAsOptions(['type' => 'authentication_multivalue']),
      '#required' => TRUE,
      '#title' => $this->t('Key containing Cloudfront <code>key_pair_id</code> and <code>private_key</code>.'),
      '#default_value' => $config->get('key'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $key = $form_state->getValue('key');
    $cloudFrontCredentials = $this->keyRepository->getKey($key)->getKeyValues();
    if (empty($cloudFrontCredentials['private_key']) || empty($cloudFrontCredentials['key_pair_id'])) {
      $form_state->setErrorByName('key', $this->t('Key must contain both required parameters.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('cdn_cloudfront_private.config')
      ->set('domain', $form_state->getValue('domain'))
      ->set('key', $form_state->getValue('key'))
      ->save();
  }

}
