<?php

/**
 * @file
 * Admin callbacks for CDN CloudFront private files integration
 */

/**
 * Form constructor for the cdn_cloudfront_private settings form.
 */
function cdn_cloudfront_private_admin_settings() {
  $form = array();
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
  return system_settings_form($form);
}