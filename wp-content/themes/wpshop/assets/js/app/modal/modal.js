/*

Modal trigger event
TODO: move to modal.js

*/
function initModal($) {
  $('.modal-trigger').click(function(e) {
    e.preventDefault();
    $('body').toggleClass('is-modal');

  });
}

export { initModal }
