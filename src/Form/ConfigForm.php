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
    $form['#tree'] = TRUE;
    $form['security'] = array(
      '#title' => t('Security configuration'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => $config->get('key_pair_id') && $config->get('private_key')
    );
    $form['security']['key_pair_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Key Pair ID'),
      '#default_value' => $config->get('key_pair_id'),
      '#required' => TRUE,
    );
    $form['security']['private_key'] = array(
      '#type' => 'textfield',
      '#title' => t('Path to PEM file'),
      '#default_value' => $config->get('private_key'),
      '#description' => t('Path to PEM keyfile'),
      '#required' => TRUE,
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $file = $form_state->get('private_key');
    if (!file_exists($form_state->get('security')['private_key'])) {
      $form_state->setErrorByName('private_key', $this->t('Private key file not found.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('cdn_cloudfront_private.config')
      ->set('private_key', $form_state->get('private_key'))
      ->set('key_pair_id', $form_state->get('key_pair_id'))
      ->save();
  }

}
