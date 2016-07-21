import validator from 'validator';
import R from 'ramda';
import crypto from "crypto";

(function($) {

  $(function() {

    /*

    Util: Get URL Parameters
    Returns: Object

    */
    const getUrlParams = function(url) {
      var urlParams = {};
      url.replace(
        new RegExp("([^?=&]+)(=([^&]*))?", "g"),
        function($0, $1, $2, $3) {
          urlParams[$1] = $3;
        }
      );

      return urlParams;
    };


    /*

    Get the stored Auth data
    Returns: Promise

    */
    const getStoredAuthData = function() {

      let options = {
        method: 'GET',
        url: '/wp-shopify/wp/wp-admin/admin-ajax.php',
        dataType: 'json',
        data: {
          action: 'wp_shopify_check_valid_nonce'
        }
      };

      return $.ajax(options);

    };


    /*

    Set the Auth data
    Returns: Promise

    */
    const saveAuthData = function(data) {

      let options = {
        method: 'POST',
        url: '/wp-shopify/wp/wp-admin/admin-ajax.php',
        dataType: 'json',
        data: {
          action: 'wp_shopify_save_auth_data',
          data: data
        }
      };

      return $.ajax(options);

    };


    /*

    Update the stored consumer entry with 'code'

    */
    const updateAuthDataWithCode = function() {

      var ok = getStoredAuthData();

      console.log("okok: ", ok);

      ok.then(function(authData) {

        console.log("authDataauthData: ", authData);

        var url = getUrlParams(location.search);
        var nonce = url.state;

        var data = JSON.parse(authData);

        console.log("url: ", url);
        console.log("data: ", data);


        // Finds the client which matches the nonce in the URL
        var nonceMatch = R.find(R.propEq('nonce', nonce))(data);

        console.log("nonceMatch: ", nonceMatch);


        if(nonceMatch.shop === url.shop) {
          // Verified

          nonceMatch.code = url.code;

          var newnew = nonceMatch.url + "&shop=" + nonceMatch.shop + "&auth=true";

          // window.location.href = newnew;

          nonceMatch.code = url.code;
          var finalRedirectURL = nonceMatch.url + "&shop=" + nonceMatch.shop + "&auth=true";

          nonceMatch = [nonceMatch];

          // Merging updated client with everything else
          var newFinalList = R.unionWith(R.eqProps('domain'), nonceMatch, data);

          console.log("newFinalList: ", newFinalList);
          console.log("nonceMatch", nonceMatch);
          console.log("nonce", nonce);

          // Saving client records to database
          saveAuthData(JSON.stringify(newFinalList)).then(function(resp) {

            // At this point we've updated the authenticated consumer with the code
            // value sent from Shopify. We can now query for this value from the
            // consumer side.
            console.log('Newly saved: ', resp);
            console.log("finalRedirectURL", finalRedirectURL);
            window.location = finalRedirectURL;

          });

        }

      });

    };


    /*

    Check if current nonce within the URL is valid. Checks
    against the stored nonce values in the database.

    */
    const isValidNonce = function() {

      var url = getUrlParams(location.search),
          nonce = url.state;

      return new Promise(function (resolve, reject) {

        getStoredAuthData().then(function(response) {

          response = JSON.parse(response);
          var nonceMatches = R.find(R.propEq('nonce', nonce))(response);

          if(nonceMatches) {
            resolve(response);

          } else {
            reject("Nonce not found, error111!");
          }

        });

      });

    };


    /*

    Check if hostname is valid

    */
    const isValidHostname = function() {
      return new Promise(function (resolve, reject) {
        var result = getUrlParams(location.search);

        console.log("result.shop: ", result.shop);

        if(validator.isURL(result.shop)) {
          resolve();

        } else {
          reject("Invalid Hostname");

        }
      });
    };


    /*

    Checks if HMAC is valid

    */
    const isValidHMAC = function() {

      return new Promise(function (resolve, reject) {
        var result = getUrlParams(location.search);
        var origHMAC = result.hmac;

        var newObj = {
          code: result.code,
          shop: result.shop,
          state: result.state,
          timestamp: result.timestamp
        };

        console.log("newObj: ", newObj);

        var message = $.param(newObj);
        var secret = 'd73e5e7fa67a54ac25a9af8ff8df3814';
        var finalDigest = crypto.createHmac('sha256', secret).update(message).digest('hex');

        console.log("Final val: ", finalDigest);
        console.log("Original hmac: ", origHMAC);

        if(finalDigest === origHMAC) {
          resolve("Valid HMAC");

        } else {
          reject("Invalid HMAC");
        }

      });

    };


    /*

    Control center

    */
    isValidHMAC()
    .then(function() {
      console.log('Finished hmac');
      return isValidHostname();

    })
    .then(function() {
      console.log('Finished hostname');
      return isValidNonce();

    })
    .then(function(response) {
      // var result = getUrlParams(location.search);
      // var origHMAC = result.state;
      // var nonceMatches = R.find(R.propEq('nonce', result.state))(response);
      // console.log('hihih', nonceMatches.url);
      console.log('Valid nonce', response);

      updateAuthDataWithCode();

    }).catch(function(err) {
      console.log('ERROR: ', err);
    });

  });

}(jQuery));
