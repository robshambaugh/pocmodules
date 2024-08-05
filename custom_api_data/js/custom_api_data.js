(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.customApiData = {
    attach: function (context, settings) {
      console.log("Drupal settings:", drupalSettings); // Log all drupalSettings
      var customerData = drupalSettings.customApiData;
      if (customerData) {
        document.cookie = "customerData=" + JSON.stringify(customerData) + ";path=/;max-age=" + (86400 * 30); // 86400 = 1 day, cookie expires in 30 days
        console.log("Customer Data cookie set:", customerData);
      } else {
        console.log("Customer Data not found in drupalSettings.");
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
