<?php

namespace Drupal\external_api_integration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\external_api_integration\Service\ExternalApiService;

class ExternalApiController extends ControllerBase {

  protected $externalApiService;

  public function __construct(ExternalApiService $external_api_service) {
    $this->externalApiService = $external_api_service;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('external_api_integration.external_api_service')
    );
  }

  public function getData($id) {
    $data = $this->externalApiService->getData($id);
    return new JsonResponse($data);
  }
}
