<?php

namespace Drupal\custom_user_fields\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomUserFormAlter {

  protected $customerApiService;

  public function __construct($customerApiService) {
    $this->customerApiService = $customerApiService;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('custom_user_fields.customer_api_service')
    );
  }

  public function alterForm(&$form, FormStateInterface $form_state, $form_id) {
    if (isset($form['field_customer_id']) && $form_id === 'user_form') {
      $customer_ids = $this->customerApiService->getCustomerIds();
      $form['field_customer_id']['widget'][0]['value'] = [
        '#type' => 'select',
        '#options' => $customer_ids,
        '#empty_option' => $this->t('- Select a Customer ID -'),
        '#title' => $this->t('Customer ID'),
      ];
    }
  }
}
