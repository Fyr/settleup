$(function() {
    $('form[name="user_info"] #role_id').change(function(e){
        var roleField = $('#entity_id_title').parents('.wrapper');
        $('#entity_id_title, #entity_id').val('');
        if ($(this).val() == 1 || $(this).val() == 2 || $(this).val() == 6) {
            roleField.hide();
            $('.contact').hide();
        } else {
            if (roleField.css('display') == 'none') {
                roleField.show();
            }
            $('.contact').show();
        }

        if ($(this).val() == '3' || $(this).val() == '4' || $(this).val() == '5') {
            $('.entity-subform-wrapper:hidden').show();
            $('.wrapper:visible').hide();

            $('.entity-subform-wrapper .entity-subform:visible input[name*="[id]"]').each(function(){
                var $subform = $(this).closest('.entity-subform');
                if ($(this).val()) {
                    $subform.find('.btn.delete').click();
                } else {
                    $subform.remove();
                }
            });
        } else {
            $('.entity-subform-wrapper:visible').hide();
            if ($(this).val() != '1' && $(this).val() != '2' && $(this).val() != '6') {
                $('.wrapper:hidden').show();
            } else {
                $('.wrapper:visible').hide();
            }

        }
    });
    $('#multiple_entity_id_modal tbody tr').click(function() {
        var $subform = $('#multiple_entity_id_modal').data('subform');
        $('input[name*="[entity_id]"]', $subform).val($(this).data('value'));
        $('input[name*="[entity_id_title]"]', $subform).val($(this).data('title'));
        $('input[name*="[carrier_id]"]', $subform).val($(this).data('carrier-id'));
        $('input[name*="[carrier_name]"]', $subform).val($(this).data('carrier-name'));
        $('#multiple_entity_id_modal').modal('hide');
        $('form[name="user_info"]').change();
    });
    $('#multiple_entity_id_modal .nav.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        $('#multiple_entity_id_modal .tab-content .tab-pane.active').removeClass('active');
        $('#multiple_entity_id_modal .tab-content ' + $(this).attr('href')).addClass('active');
    })
    $('#multiple_entity_id_modal .nav.nav-tabs li').first().toggleClass('active');
    $('#multiple_entity_id_modal .tab-content div').first().toggleClass('active');
});
