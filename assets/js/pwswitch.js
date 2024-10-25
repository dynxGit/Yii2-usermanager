/** -----------------------------------------------------------------------------------------------------
 * validatepassword.js
 * 
 * @copyright Copyright &copy; Szincsák András
 * @author Szincsák András <andras@szincsak.hu>
 * @version 1.0
 * 
 * ------------------------------------------------------------------------------------------------------ */
let pwValidatorMessages = {
    'title': "Password must containts the following:",
    'length': "Minimum <b>{num}</b> character",
    'upper': "Minimum <b>{num}</b> uppercase [A-Z]",
    'number': "Minimum <b>{num}</b> number [1-9]",
    'symbol': "Minimum <b>{num}</b> special character [!%+-?@]",
}

$(function () {
    $('.form-control.pwswitched').before($("<div class='pwBlock'></div>"))
    .appendTo($('.pwBlock'))
    .after("  <div class=\"pwswitch\"><i class=\"fa fa-eye\"></i></div> ")
    .closest('.pwBlock').next().appendTo($('.pwBlock'))
    $(".pwswitch").on('click', passwordSwitch);

    if ($(".pwvalidate").length) {
        var $password = $(".pwvalidate");
        $password.on('keyup', validatePassword);
        $password.closest("form").on('submit', function () {
            return $password.hasClass('is-valid');
        });
        generateValidatorMessages($password)
        validatePassword($password);
    }

});


function generateValidatorMessages(field) {

    var validators = $(field).data('validator'),
        items = "";
    for (const [key, value] of Object.entries(validators)) {
        if (value)
            items += "<li class='pw" + key + "'   data-length=" + (value - 1) + ">" + pwValidatorMessages[key].replace('{num}', value) + "</li>"
    }

    if (items.length) {
        items = '<div class="pwValidator"><b>' + pwValidatorMessages.title + '</b><ul>' + items + '</ul>'
        $(field).closest('.pwBlock').after($(items));
    }
}


function validatePassword(field) {
    var elem = (field.target) ? $(field.target) : $(field);
    var validators = $(elem).data('validator'),
        val = elem.val(),
        validatorItems = 0;
    minLength = validators.length;

    $('.pwValidator').toggleClass('validated', val.length > 0);
    var valid = false;
    elem.removeClass('is-valid').removeClass('is-invalid');
    if (val) {
        var minchar = Object.values(validators).reduce((pv, cv) => {
            return pv + (parseFloat(cv) || 0);
        }, 0);
          $('.pwlength').toggleClass('valid', val.length > minLength);
        if ('number' in validators && validators.number > 0) {
            validatorItems++;
            re = new RegExp("(\\d.*){" + validators.number + ",}", "gm");
            $('.pwnumber').toggleClass('valid', re.test(val));
        }
        if ('upper' in validators && validators.upper > 0) {
            validatorItems++;
            re = new RegExp("(\[A-Z\].*){" + validators.upper + ",}", "gm");
            $('.pwupper').toggleClass('valid', re.test(val));
        }
        if ('symbol' in validators && validators.symbol > 0) {
            validatorItems++;
            re = new RegExp("(\[\\!\\%\\+\\-\\?\\@\].*){" + validators.symbol + ",}", "gm");
            console.log(re);
 
      //      re = /[\!\%\+\-\?\@]/;
            $('.pwsymbol').toggleClass('valid', re.test(val));
        }
        valid = ($(".pwValidator li.valid").length >= (validatorItems));
        elem.toggleClass('is-valid', valid);
        elem.toggleClass('is-invalid', !valid);
        elem.closest("form").toggleClass('is-valid', valid);
    }
    elem.closest("form").find('button[type=submit]').attr('disabled', !valid);
    $('.pwconfirm').val("");
}

function passwordSwitch() {
    var item = $(this).parent('div').find('input.pwswitched').get(0);
    $(item).prop('type', ($(item).prop('type') == 'password' ? 'text' : 'password'));
    $(this).html('<i class="fas ' + ($(item).prop('type') == 'password' ? 'fa-eye' : 'fa-eye-slash') + '"></i>');
}