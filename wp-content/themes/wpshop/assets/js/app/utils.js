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
  $form.addClass('is-submitting');
  console.log(2);
}


/*

Enable Form

*/
function enableForm($form) {
  console.log(3);
  $form.find('input').removeClass('is-disabled');
  $form.removeClass('is-submitting');
  console.log(4);
}

function doScrolling(element, duration) {
	var startingY = window.pageYOffset
  var elementY = getElementY(element)
  // If element is close to page's bottom then window will scroll only to some position above the element.
  var targetY = document.body.scrollHeight - elementY < window.innerHeight ? document.body.scrollHeight - window.innerHeight : elementY
	var diff = targetY - startingY
  // Easing function: easeInOutCubic
  // From: https://gist.github.com/gre/1650294
  var easing = function (t) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1 }
  var start

  if (!diff) return

	// Bootstrap our animation - it will get called right before next frame shall be rendered.
	window.requestAnimationFrame(function step(timestamp) {
    if (!start) start = timestamp
    // Elapsed miliseconds since start of scrolling.
    var time = timestamp - start
		// Get percent of completion in range [0, 1].
    var percent = Math.min(time / duration, 1)
    // Apply the easing.
    // It can cause bad-looking slow frames in browser performance tool, so be careful.
    percent = easing(percent)

    window.scrollTo(0, startingY + diff * percent)

		// Proceed with animation as long as we wanted it to.
    if (time < duration) {
      window.requestAnimationFrame(step)
    }
  })
}


/*

Learn more

*/
function learnMoreLink($) {


  $('.btn-hero').on('click', function(e) {

    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 600);
        return false;
      }
    }

  });


}




export { getUrlParams, learnMoreLink };
