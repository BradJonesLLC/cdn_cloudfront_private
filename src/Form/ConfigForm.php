<?php

namespace Drupal\cdn_cloudfront_private\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigForm.
 *
 * @package Drupal\cdn_cloudfront_private\Form
 */
class ConfigForm extends ConfigFormBase {

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
    // @todo - Still legacy D7 code below.
    /*
    $form['security'] = array(
      '#title' => t('Security configuration'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => variable_get('cdn_cloudfront_private_key_id') && variable_get('cdn_cloudfront_private_keyfile'),
    );
    $form['security']['cdn_cloudfront_private_key_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Key ID'),
      '#default_value' => variable_get('cdn_cloudfront_private_key_id', ''),
      '#required' => TRUE,
    );
    $form['security']['cdn_cloudfront_private_keyfile'] = array(
      '#type' => 'textfield',
      '#title' => t('Path to PEM file'),
      '#default_value' => variable_get('cdn_cloudfront_private_keyfile', ''),
      '#description' => t('Absolute system path to PEM keyfile'),
      '#required' => TRUE,
    );
    **/
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('cdn_cloudfront_private.config')
      ->save();
  }

}
