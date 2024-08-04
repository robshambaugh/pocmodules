<?php

namespace Drupal\custom_api_data\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_api_data\Service\CustomApiDataService;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Sets cookies on every page load.
 */
class SetCookiesSubscriber implements EventSubscriberInterface {

  protected $apiDataService;
  protected $currentUser;

  public function __construct(CustomApiDataService $apiDataService, AccountProxyInterface $currentUser) {
    $this->apiDataService = $apiDataService;
    $this->currentUser = $currentUser;
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onKernelRequest', 0];
    $events[KernelEvents::RESPONSE][] = ['onKernelResponse', 0];
    return $events;
  }

  public function onKernelRequest(RequestEvent $event) {
    // Only proceed if this is the master request
    if ($event->isMasterRequest()) {
      $user = \Drupal\user\Entity\User::load($this->currentUser->id());
      $customer_id = $user->get('field_customer_id')->value;

      if ($customer_id) {
        $customer_data = $this->apiDataService->getCustomers();
        $trips_data = $this->apiDataService->getCustomerTrips($customer_id);

        if ($customer_data && $trips_data) {
          $customer_first_name = $customer_data['data'][0]['attributes']['first_name'];
          $number_of_trips = count($trips_data['data']);

          // Sort trips by start date to find the most recent trip
          usort($trips_data['data'], function($a, $b) {
            return strtotime($b['attributes']['start_date']) - strtotime($a['attributes']['start_date']);
          });

          $most_recent_trip = $trips_data['data'][0]['attributes'];

          $trip_name = $most_recent_trip['trip_name'];
          $trip_start_date = $most_recent_trip['start_date'];
          $trip_end_date = $most_recent_trip['end_date'];

          $this->setCookies([
            'customerFirstName' => $customer_first_name,
            'numberOfTrips' => $number_of_trips,
            'tripName' => $trip_name,
            'tripStartDate' => $trip_start_date,
            'tripEndDate' => $trip_end_date,
          ]);
        }
      }
    }
  }

  public function onKernelResponse(ResponseEvent $event) {
    // Ensure the cookies are set in the response
    $response = $event->getResponse();
    $cookies = $this->getCookies();
    foreach ($cookies as $name => $value) {
      $response->headers->setCookie(new Cookie($name, $value, 0, '/', NULL, FALSE, FALSE));
    }
  }

  protected function setCookies(array $data) {
    foreach ($data as $name => $value) {
      setcookie($name, $value, 0, '/');
    }
  }

  protected function getCookies() {
    $cookies = [];
    foreach ($_COOKIE as $name => $value) {
      $cookies[$name] = $value;
    }
    return $cookies;
  }
}
