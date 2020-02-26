jQuery(document).ready(function() {
  $.get("https://ipinfo.io", function(response) {
      console.log(response.city, response.country);
  }, "jsonp");
});