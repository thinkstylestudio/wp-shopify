import $ from "jquery";
// import Shopify from "./shopify";

// Shopify.init();



function delay(num) {
  return new Promise(function(resolve, reject) {
    setTimeout(resolve, num);
  });
}

delay(2000)
  .then(function() {
    console.log(2000);
    return delay(500);
  })
  .then(function() {
    console.log(500);
    return delay(2000);
  })
  .then(function() {
    console.log(2000);
    console.log('All done!');
  });
