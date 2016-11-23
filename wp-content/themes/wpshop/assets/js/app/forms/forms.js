/*

MC: Get list by ID
Returns promise

*/
function McSignUp($) {

  var emailVal = $("#mailinglist-email").val(),
      emailnonce = $("#mailinglist-form #_wpnonce").val();

  var options = {
    type: 'POST',
    url: '/wp/wp-admin/admin-ajax.php',
    dataType: 'json',
    data: {
      action: "mailinglist_signup",
      email: emailVal,
      nonce: emailnonce
    }
  };

  return $.ajax(options);

}


/*

On click

*/
function onInputFocus($) {

  $('.form-input').on('focusin', function() {
    $(this).closest('.form-control').addClass('is-focused');
  });

  $('.form-input').on('focusout', function() {
    if( !$(this).val() ) {
      $(this).closest('.form-control').removeClass('is-focused');
    }
  });

  $('.form-label').on('click', function() {
    $(this).next().focus();
  });

}


/*

On form submission

*/
function onFormSubmission($) {

  $("#mailinglist-form").validate({

    submitHandler: function(form, e) {

      e.preventDefault();
      $(form).addClass('is-submitting');
      $(form).find('input').addClass('is-disabled').prop("disabled", true);
      $(form).find('.spinner').addClass('is-visible');

      McSignUp($)
        .done(function(data) {

          console.log("data: ", data);

          if(data.code !== 200) {
            $(form).find('.form-error').addClass('is-visible');
            $(form).find('#mailinglist-email-error').append('<i class="fa fa-times-circle" aria-hidden="true"></i> Uh oh, we have an error! Looks like ' + data.message.title + '. Please try again');
            $(form).find('.spinner').removeClass('is-visible');
            $(form).find('input').removeClass('is-disabled');
            $(form).removeClass('is-submitting');

          } else {
            console.log('Success, time to hide stuff');

            $(form).removeClass('is-submitting');
            $(form).find('.spinner').removeClass('is-visible');
            $(form).find('input').removeClass('is-disabled');
            $(form).find('.form-success').addClass('is-visible');
            $(form).find('.form-success').append('<i class="fa fa-check-circle" aria-hidden="true"></i> Success! Please check your email to finish signing up.');
            $(form).addClass('is-submitted');

          }

        })
        .fail(function(jqXHR, textStatus) {
          $(form).find('.form-error').addClass('is-visible');
          $(form).find('#mailinglist-email-error').append('Error! ' + textStatus);
          $(form).find('.spinner').removeClass('is-visible');
          $(form).find('input').removeClass('is-disabled');
          $(form).removeClass('is-submitting');

        });

    },

    rules: {
      email: {
        required: true,
        email: true
      }
    },

    errorClass: 'error',
    validClass: 'succes',

    highlight: function (element, errorClass, validClass) {
      $('#mailinglist-email').parent().removeClass('form-valid');
      $('.form-error').addClass('is-visible');
      $('.form-success').removeClass('is-visible');

    },
    unhighlight: function (element, errorClass, validClass) {
      // $('.form-success').addClass('is-visible');
      $('.form-error').removeClass('is-visible');

    },
    success: function(label){
      $('#mailinglist-email').parent().addClass('form-valid');

    },
    errorPlacement: function(error, element) {
      error.appendTo($('.form-error'));
    }

  });

}


function initForms($) {
  onFormSubmission($);
  onInputFocus($);
}

export { initForms }
