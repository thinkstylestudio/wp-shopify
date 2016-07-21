import validator from 'validator';
// import CryptoJS from "crypto-js";
import crypto from "crypto";
// import ASQ from "asynquence";


var grabURL = function() {
  var URL = window.location.href;

  // console.log('Referrer URL: ', document.referrer);
  // console.log('Current URL: ', URL);
  // $('body').fadeOut('slow');
};

grabURL();


var parseQueryString = function(url) {
  var urlParams = {};
  url.replace(
    new RegExp("([^?=&]+)(=([^&]*))?", "g"),
    function($0, $1, $2, $3) {
      urlParams[$1] = $3;
    }
  );

  return urlParams;
};


// Test valid nonce

var isValidNonce = function() {
  return new Promise(function (resolve, reject) {
    var result = parseQueryString(location.search);

    if(result) {
      resolve();

    } else {
      reject("Invalid Nonce");
    }
  });
};


// Test valid hostname
var isValidHostname = function() {
  return new Promise(function (resolve, reject) {
    var result = parseQueryString(location.search);

    if(validator.isURL(result.shop)) {
      resolve();

    } else {
      reject("Invalid Hostname");

    }
  });
};




// Test valid hmac
var isValidHMAC = function() {

  return new Promise(function (resolve, reject) {
    var result = parseQueryString(location.search);
    var origHMAC = result.hmac;

    var newObj = {
      code: result.code,
      shop: result.shop,
      state: result.state,
      timestamp: result.timestamp
    };

    var message = $.param(newObj);
    var digest = crypto.createHash('sha256');
    var secret = 'd73e5e7fa67a54ac25a9af8ff8df3814';
    var finalDigest = crypto.createHmac('sha256', secret).update(message).digest('hex');

    // console.log("Final val: ", finalDigest);
    // console.log("Original hmac: ", result.hmac);

    if(finalDigest === origHMAC) {
      resolve();

    } else {
      reject("Invalid HMAC");
    }

  });

};


isValidHMAC()
.then(function() {
  console.log('Finished hmac');
  return isValidHostname();
})
.then(function() {
  console.log('Finished hostname');
  return isValidNonce();
})
.then(function() {
  console.log('Finished nonce');
  console.log('All done');
}).catch(console.log.bind(console));
