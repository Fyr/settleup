$(function() {
    $('form[name="user_info"] #role_id').change(function(e){
        var roleField = $('#entity_id_title').parents('.wrapper');
        $('#entity_id_title, #entity_id').val('');
        if ($(this).val() == 1 || $(this).val() == 5 || $(this).val() == 6) {
            roleField.hide();
            $('.contact').hide();
        } else {
            if (roleField.css('display') == 'none') {
                roleField.show();
                $('.contact').show();
            }
        }


        if ($(this).val() == '3' || $(this).val() == '4') {
            $('.entity-subform-wrapper:hidden').show();
            $('.wrapper:visible').hide();

            $('.entity-subform-wrapper .entity-subform:visible input[name*="[id]"]').each(function(){
                var $subform = $(this).closest('.entity-subform');
                if ($('.entity-subform-wrapper .entity-subform:visible').length === 1) {
                    $subform.find('.btn.add').click();
                }
                if ($(this).val()) {
                    $subform.find('.btn.delete').click();
                } else {
                    $subform.remove();
                }
            });
            updateMultipleEntityPopup($(this).val());
        } else {
            $('.entity-subform-wrapper:visible').hide();
            if ($(this).val() != '5' && $(this).val() != '1' && $(this).val() != '6') {
                $('.wrapper:hidden').show();
            } else {
                $('.wrapper:visible').hide();
            }

        }
    });
    $('body').on('click', '#multiple_entity_id_modal tbody tr', function(){
    //$('#multiple_entity_id_modal tbody tr').click(function() {
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
    function updateMultipleEntityPopup(role) {
        if (role == '3') {
            $('#multiple_entity_id_modal .nav.nav-tabs a[href="#Vendor"]').hide();
            $('#multiple_entity_id_modal .nav.nav-tabs a[href="#Contractor"]').show();
            $('#multiple_entity_id_modal .nav.nav-tabs a[href="#Contractor"]').click();
        } else if (role == '4') {
            $('#multiple_entity_id_modal .nav.nav-tabs a[href="#Contractor"]').hide();
            $('#multiple_entity_id_modal .nav.nav-tabs a[href="#Vendor"]').show();
            $('#multiple_entity_id_modal .nav.nav-tabs a[href="#Vendor"]').click();
        }
    }
    updateMultipleEntityPopup($('form[name="user_info"] #role_id').val());
});
