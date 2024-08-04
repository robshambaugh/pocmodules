<?php

namespace Drupal\api_consumer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class APIConsumerConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['api_consumer.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'api_consumer_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('api_consumer.settings');

    $form['apis'] = [
      '#type' => 'textarea',
      '#title' => $this->t('API Sources'),
      '#description' => $this->t('Enter the API sources as JSON. Example: [{"name": "Customers API", "url": "https://myapisource.com", "endpoint": "customers"}, {"name": "Trips API", "url": "https://myapisource.com", "endpoint": "trips"}]'),
      '#default_value' => $config->get('apis') ?: '[]',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('api_consumer.settings')
      ->set('apis', $form_state->getValue('apis'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
