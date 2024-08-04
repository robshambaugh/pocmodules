(function() {
  function setCookie(name, value, days) {
    var expires = "";
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }

  // Fetch customer data from drupalSettings
  var customerData = drupalSettings.custom_api_data.customerData;
  console.log('Customer Data:', customerData); // Debugging log

  if (customerData) {
    setCookie('customerFirstName', customerData.customerFirstName, 7);
    setCookie('numberOfTrips', customerData.numberOfTrips, 7);
    setCookie('tripName', customerData.tripName, 7);
    setCookie('tripStartDate', customerData.tripStartDate, 7);
    setCookie('tripEndDate', customerData.tripEndDate, 7);
  } else {
    console.log('No customer data found in drupalSettings');
  }
})();
