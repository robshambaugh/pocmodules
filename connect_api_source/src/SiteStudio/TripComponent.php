<?php

namespace Drupal\\connect_api_source\\SiteStudio;

use Drupal\\cohesion_elements\\ElementBase;
use Symfony\\Component\\DependencyInjection\\ContainerInterface;
use Drupal\\connect_api_source\\Service\\ApiService;

/**
 * Provides a 'Trip Component' for Site Studio.
 */
class TripComponent extends ElementBase {

  protected $apiService;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ApiService $api_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->apiService = $api_service;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('connect_api_source.api_service')
    );
  }

  public function build(array $properties) {
    $trip_id = $properties['trip_id'];
    $trips_data = $this->apiService->fetchTripsData();
    $trip_data = array_filter($trips_data['data'], function($trip) use ($trip_id) {
      return $trip['attributes']['field_trip_id'] == $trip_id;
    });

    $trip = reset($trip_data);
    return [
      '#theme' => 'trip_component',
      '#title' => $trip['attributes']['field_trip_name'],
      '#description' => $trip['attributes']['field_trip_description'],
      '#start_date' => $trip['attributes']['field_trip_start_date'],
      '#end_date' => $trip['attributes']['field_trip_end_date'],
      '#price' => $trip['attributes']['field_price'],
    ];
  }

  public function defaultConfiguration() {
    return [
      'trip_id' => '',
    ];
  }

  public function buildConfigurationForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form['trip_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Trip ID'),
      '#default_value' => $this->configuration['trip_id'],
    ];
    return $form;
  }

  public function submitConfigurationForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $this->configuration['trip_id'] = $form_state->getValue('trip_id');
  }
}
