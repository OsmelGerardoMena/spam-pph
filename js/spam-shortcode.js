$('.multi-field-wrapper').each(function() {
    var $wrapper = $('.multi-fields', this);
    $(".add-field", $(this)).click(function(e) {
        $('.multi-field:first-child', $wrapper).clone(true).appendTo($wrapper).find('input').val('').focus();
    });
    $('.multi-field .remove-field', $wrapper).click(function() {
        if ($('.multi-field', $wrapper).length > 1)
            $(this).parent('.multi-field').remove();
    });
});

$('.multi-field-wrapperr').each(function() {
    var $wrapper = $('.multi-fieldss', this);
    $(".add-fieldd", $(this)).click(function(e) {
        $('.multi-fieldd:first-child', $wrapper).clone(true).appendTo($wrapper).find('input').val('').focus();
    });
    $('.multi-fieldd .remove-fieldd', $wrapper).click(function() {
        if ($('.multi-fieldd', $wrapper).length > 1)
            $(this).parent('.multi-fieldd').remove();
    });
});