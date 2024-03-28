(function($){

    $.fn.hideButtons = function(){
        $(this).find(':input').each(function(){
            $(this).attr('savedValue', $(this).val());
        });

        $(this).change(function(){
            toggleFormButtons(this);
        }).keyup(function(){
            toggleFormButtons(this);
        });
    };

    function toggleFormButtons(form){
        var show = false;
        $(form).find(':input:not(#change_cycle_rule_fields)').each(function(){
            if ($(this).attr('savedValue') !== $(this).val()) {
                show = true;
            }
        });
        if (show) {
            $(form).find('.form-actions').show();
        } else {
            $(form).find('.form-actions').hide();
        }
    }

}(jQuery));

$(function(){
    $('form input[type=checkbox]:not([readonly=readonly])').change(function(){
        $(this).val($(this).prop('checked') ? 1 : 0);
    }).change();
    $('form.hide-buttons').hideButtons();
});