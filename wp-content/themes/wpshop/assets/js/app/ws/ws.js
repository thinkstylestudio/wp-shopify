import $ from "jquery";

/*

Get the stored Auth data
Returns: Promise

*/
function getStoredAuthData() {

  let options = {
    method: 'GET',
    url: '/wp/wp-admin/admin-ajax.php',
    dataType: 'json',
    data: {
      action: 'wps_check_valid_nonce'
    }
  };

  return $.ajax(options);

};


/*

Set the Auth data
Returns: Promise

*/
function saveAuthData(data) {

  let options = {
    method: 'POST',
    url: '/wp/wp-admin/admin-ajax.php',
    dataType: 'json',
    data: {
      action: 'wps_save_auth_data',
      data: data
    }
  };

  return $.ajax(options);

};

export { getStoredAuthData, saveAuthData }
