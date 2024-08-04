<?php

namespace Drupal\custom_api_data\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Drupal\custom_api_data\Service\CustomApiDataService;
use Drupal\Core\Session\AccountProxyInterface;
use Psr\Log\LoggerInterface;

/**
 * Sets cookies on every page load.
 */
class SetCookiesSubscriber implements EventSubscriberInterface {

  protected $apiDataService;
  protected $currentUser;
  protected $logger;

  public function __construct(CustomApiDataService $apiDataService, AccountProxyInterface $currentUser, LoggerInterface $logger) {
    $this->apiDataService = $apiDataService;
    $this->currentUser = $currentUser;
    $this->logger = $logger;
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onKernelRequest', 0];
    return $events;
  }

  public function onKernelRequest(RequestEvent $event) {
    if ($event->isMasterRequest()) {
      // Temporarily disable the functionality to avoid site breakage.
      return;

      // The following code is commented out to prevent execution.
      // $this->logger->info('SetCookiesSubscriber triggered');
      // $user = \Drupal\user\Entity\User::load($this->currentUser->id());
      // if ($user) {
      //   $customer_id = $user->get('field_customer_id')->value;
      //   if ($customer_id) {
      //     $this->logger->info('Customer ID found: @customer_id', ['@customer_id' => $customer_id]);
      //     $customer_data = $this->apiDataService->getCustomers();
      //     $trips_data = $this->apiDataService->getCustomerTrips($customer_id);
      //     if (!empty($customer_data['data']) && !empty($trips_data['data'])) {
      //       $customer_first_name = $customer_data['data'][0]['attributes']['first_name'];
      //       $number_of_trips = count($trips_data['data']);
      //       // Sort trips by start date to find the most recent trip.
      //       usort($trips_data['data'], function($a, $b) {
      //         return strtotime($b['attributes']['start_date']) - strtotime($a['attributes']['start_date']);
      //       });
      //       $most_recent_trip = $trips_data['data'][0]['attributes'];
      //       $trip_name = $most_recent_trip['trip_name'];
      //       $trip_start_date = $most_recent_trip['start_date'];
      //       $trip_end_date = $most_recent_trip['end_date'];
      //       // Log the data being set.
      //       $this->logger->info('Setting cookies: @data', ['@data' => json_encode([
      //         'customerFirstName' => $customer_first_name,
      //         'numberOfTrips' => $number_of_trips,
      //         'tripName' => $trip_name,
      //         'tripStartDate' => $trip_start_date,
      //         'tripEndDate' => $trip_end_date,
      //       ])]);
      //       // Add data to cookies.
      //       setcookie('customerFirstName', $customer_first_name, time() + (86400 * 7), "/");
      //       setcookie('numberOfTrips', $number_of_trips, time() + (86400 * 7), "/");
      //       setcookie('tripName', $trip_name, time() + (86400 * 7), "/");
      //       setcookie('tripStartDate', $trip_start_date, time() + (86400 * 7), "/");
      //       setcookie('tripEndDate', $trip_end_date, time() + (86400 * 7), "/");
      //     } else {
      //       $this->logger->warning('No customer or trips data found for customer ID: @customer_id', ['@customer_id' => $customer_id]);
      //     }
      //   } else {
      //     $this->logger->warning('No customer ID found for user ID: @user_id', ['@user_id' => $this->currentUser->id()]);
      //   }
      // } else {
      //   $this->logger->warning('No user found for current session.');
      // }
    }
  }
}
