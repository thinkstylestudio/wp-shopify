import $ from 'jquery';
// import slick from 'slick-carousel';

const Vendor = (function() {

  let $body = $('body');

  let logBody = function() {
    console.log($body);
  };

  return {
    logBody: logBody
  };

})();

export default Vendor;
