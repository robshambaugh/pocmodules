(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.customApiData = {
    attach: function (context, settings) {
      var customerData = drupalSettings.customApiData;
      if (customerData) {
        document.cookie = "customerData=" + JSON.stringify(customerData) + ";path=/;max-age=" + (86400 * 30); // 86400 = 1 day, cookie expires in 30 days
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
