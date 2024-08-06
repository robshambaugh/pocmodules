<?php

namespace Drupal\\connect_api_source\\Controller;

use Drupal\\Core\\Controller\\ControllerBase;
use Symfony\\Component\\DependencyInjection\\ContainerInterface;
use Symfony\\Component\\HttpFoundation\\Request;
use Drupal\\connect_api_source\\Service\\ApiService;

class ApiController extends ControllerBase {

  protected $apiService;

  public function __construct(ApiService $api_service) {
    $this->apiService = $api_service;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('connect_api_source.api_service'));
  }

  public function updateCustomerCookie(Request $request) {
    $customer_id = $request->query->get('customer_id');
    if ($customer_id) {
      $customer_data = $this->apiService->fetchCustomerData($customer_id);
      if ($customer_data) {
        $this->apiService->setCustomerCookie($customer_data);
        return $this->redirect('<front>')->send();
      }
    }
    return $this->redirect('<front>')->send();
  }

  public function getTripsData() {
    $trips_data = $this->apiService->fetchTripsData();
    return [
      '#theme' => 'item_list',
      '#items' => array_map(function($trip) {
        return $trip['attributes']['title'];
      }, $trips_data['data']),
      '#title' => $this->t('Trips'),
    ];
  }
}
