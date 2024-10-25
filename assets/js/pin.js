function collectPinPart() {
    var pin = "", valid = true
    inputField = $('#pinblock').data('input')
    $('div.pinbox').each(function (idx, el) {
        value = $(el).html();
        if ($(el).hasClass('input')) {
            value = $(el).find('input').val();
            $(el).toggleClass('invalid', value.length === 0);
            if (value && valid) {
                pin += value;
            } else valid = false;
        } else
            pin += $(el).html()
    });
    $('input#' + inputField + '').val(valid ? pin : '');

    $('#pinblock').toggleClass('invalid', function () { return !valid });
    $('#pinblock').toggleClass('valid', valid);

    return valid;

}
$(function ($) {
    $('div.pinbox input').on('focus', function (el) {
        $(this).val("");
    })
        .on('keyup', function (el) {
            pattern = ($(this).data('type') == "N") ? /[^0-9]/g : /[^A-Z]/g
            $(this).val($(this).val().replace(pattern, ''));
            if ($(this).val().length) {
                $(this).next('div.pinbox input').focus();
                var nextIndex = $('div.pinbox input').index(this) + 1;
                if ($('div.pinbox input').length > nextIndex)
                    $('div.pinbox input').eq(nextIndex).focus()
                else
                    $('form').submit();
            }
            collectPinPart();
        });
    $('form').on('submit', function () {
        return collectPinPart();
    })
    $('div.pinbox input').first().focus();
});