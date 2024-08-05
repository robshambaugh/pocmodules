(function($, Drupal, drupalSettings) {
  $(document).ready(function() {
    function fetchTripData(tripId) {
      $.ajax({
        url: `/api-consumer?api=Trips%20API&trip_id=${tripId}`,
        method: 'GET',
        success: function(data) {
          if (data.trip) {
            var trip = data.trip;
            $('[data-cohesion-field="trip_name"]').val(trip.title);
            $('[data-cohesion-field="start_date"]').val(trip.start_date);
            $('[data-cohesion-field="end_date"]').val(trip.end_date);
            $('[data-cohesion-field="price"]').val(trip.price);
            $('[data-cohesion-field="cost_per_day"]').val(trip.cost_per_day);
            $('[data-cohesion-field="average_group_size"]').val(trip.average_group_size);
            $('[data-cohesion-field="video_urls"]').val(trip.video_urls.join(', '));
            $('[data-cohesion-field="map_image_url"]').val(trip.map_image_url);
          }
        },
        error: function() {
          console.log('Error fetching trip data.');
        }
      });
    }

    $('[data-cohesion-field="trip_id"]').on('change', function() {
      var tripId = $(this).val();
      if (tripId) {
        fetchTripData(tripId);
      }
    });

    var customerData = drupalSettings.customerData;

    if (customerData) {
      // Set the cookie with customer data
      document.cookie = "customerData=" + JSON.stringify(customerData) + "; path=/";

      // Extract data and add to the block if necessary
      $('#customer-first-name').text(customerData.firstName);
      $('#customer-last-name').text(customerData.lastName);
      $('#trips-booked').text(customerData.tripsBooked);
      $('#trip-details').text(customerData.tripDetails);

      // Set Site Studio variables
      if (typeof window.siteStudio !== 'undefined') {
        window.siteStudio.variables.setVariableValue('customer_first_name', customerData.firstName);
        window.siteStudio.variables.setVariableValue('customer_last_name', customerData.lastName);
        window.siteStudio.variables.setVariableValue('trips_booked', customerData.tripsBooked);
        window.siteStudio.variables.setVariableValue('trip_details', customerData.tripDetails);
      }
    }
  });
})(jQuery, Drupal, drupalSettings);
