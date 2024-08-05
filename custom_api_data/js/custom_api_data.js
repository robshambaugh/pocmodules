(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.customApiData = {
    attach: function (context, settings) {
      console.log("Custom API Data JavaScript loaded");
    }
  };
})(jQuery, Drupal, drupalSettings);
