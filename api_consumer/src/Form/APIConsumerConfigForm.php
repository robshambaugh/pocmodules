<?php

namespace Drupal\api_consumer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class APIConsumerConfigForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['api_consumer.settings'];
  }

  public function getFormId() {
    return 'api_consumer_config_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('api_consumer.settings');
    
    $form['selected_api'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Selected API'),
      '#default_value' => $config->get('selected_api'),
    ];

    $form['apis'] = [
      '#type' => 'textarea',
      '#title' => $this->t('APIs'),
      '#default_value' => $config->get('apis'),
      '#description' => $this->t('Enter the APIs in JSON format.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('api_consumer.settings')
      ->set('selected_api', $form_state->getValue('selected_api'))
      ->set('apis', $form_state->getValue('apis'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
