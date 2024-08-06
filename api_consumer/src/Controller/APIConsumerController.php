<?php

namespace Drupal\api_consumer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp\Exception\RequestException;
use Drupal\api_consumer\Service\APIConsumerService;

class APIConsumerController extends ControllerBase {

  protected $apiConsumerService;

  public function __construct(APIConsumerService $api_consumer_service) {
    $this->apiConsumerService = $api_consumer_service;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('api_consumer.api_consumer_service')
    );
  }

  public function content() {
    $customer_id = \Drupal::currentUser()->id();
    
    try {
      $customer_data = $this->apiConsumerService->getCustomerData($customer_id);
      $trips_data = $this->apiConsumerService->getCustomerTrips($customer_id);

      return [
        '#theme' => 'item_list',
        '#items' => [
          'Customer Data' => $customer_data,
          'Trips Data' => $trips_data,
        ],
        '#title' => $this->t('API Consumer Data'),
      ];
    } catch (RequestException $e) {
      $this->logger('api_consumer')->error($e->getMessage());
      return new JsonResponse(['error' => $e->getMessage()], 500);
    }
  }
}
