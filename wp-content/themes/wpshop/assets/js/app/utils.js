/*

Util: Get URL Parameters
Returns: Object

*/
function getUrlParams(url) {
  console.log('hi');
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

Show Loader

*/
function showLoader($form) {
  $form.find('.spinner').addClass('is-visible');
}


/*

Hide Loader

*/
function hideLoader($form) {
  $form.find('.spinner').removeClass('is-visible');
}


/*

Disable Form

*/
function disableForm($form) {
  console.log(1);
  $form.find('input').addClass('is-disabled');
  console.log(2);
}


/*

Enable Form

*/
function enableForm($form) {
  console.log(3);
  $form.find('input').removeClass('is-disabled');
  console.log(4);
}








export { getUrlParams };
