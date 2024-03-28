$(function(){
    $('.print-report').click(function(){
        $('.report-body').printElement({
            pageTitle:'Pfleet report',
            overrideElementCSS:[
                {
                    href:'../css/bootstrap.css',
                    media:'print'
                },
                {
                    href:'../css/pdf.css',
                    media:'print'
                }
            ]
        });
    });
    $('#type').change(function(e){
        var targetFilters = additionalFilter[$(this).val()];
        $('.additional-filter').hide();
        for (var i = 0; i < targetFilters.length; i++) {
            $('.additional-filter[data-additional-filter-type="' + targetFilters[i] + '"]').show();
            if (targetFilters[i] == 'carrier_vendor') {
                if ($(this).val() == 11 || $(this).val() == 13 || $(this).val() == 17) {
                    $('#select_carrier_vendor option[value=1]').prop('disabled', true);
                    $('#select_carrier_vendor').val(2).change();
                    $('#carrier_vendor_id_title, #carrier_vendor_id').val('');
                    $('#carrier_vendor_id_modal :checkbox').prop('checked', false);
                    $('#carrier_vendor_id_modal .checkAll').prop('disabled', true);
                } else {
                    $('#select_carrier_vendor option[value=1]').prop('disabled', false);
                    $('#carrier_vendor_id_modal .checkAll').prop('disabled', false);
                }
            }
            if (targetFilters[i] == 'reserve_account_contractor' || targetFilters[i] == 'reserve_account') {
                if ($(this).val() == 8 || $(this).val() == 14) {
                    $('option[value=1]', '#select_reserve_account, #select_reserve_account_contractor').prop('disabled', true);
                    $('#select_reserve_account, #select_reserve_account_contractor').val(2).change();
                    if ($(this).val() != 8 ) {
                        $('option[value=1]', '#select_contractor').prop('disabled', true);
                        $('#select_contractor').val(2).change();
                    }

                    $('#reserve_account_id, #reserve_account_contractor_id, #contractor_id, #reserve_account_id_title, #reserve_account_contractor_id_title, #contractor_id_title').val('');
                    $(':checkbox', '#reserve_account_contractor_id_modal, #contractor_id_modal').prop('checked', false);
                    $('.checkAll', '#reserve_account_contractor_id_modal, #contractor_id_modal').prop('disabled', true);
                    if ($(this).val() == 8 && $('#select_contractor').val() == 1) {
                        $(':checkbox', '#reserve_account_id_modal').prop('checked', false);
                        $('.checkAll', '#reserve_account_id_modal').prop('disabled', true);
                    }
                } else {
                    $('option[value=1]', '#select_reserve_account, #select_reserve_account_contractor, #select_contractor').prop('disabled', false);
                    $('.checkAll', '#reserve_account_id_modal, #reserve_account_contractor_id_modal, #contractor_id_modal').prop('disabled', false);
                }
            }
        }
        configureDateFilter($(this).val());
        configurePeriodFilter($(this).val());
        configureFileType($(this).val());
        configureContractorFilter($(this).val());
        configureViewButton($(this).val());

        $('#date_filter_type').trigger('change');
    });
    $('#date_filter_type').change(function(e){
        var targetFields = $('.date-filter[data-type="' + $(this).val() + '"]');
        if (targetFields.css('display') == 'none') {
            targetFields.show();
            targetFields.siblings('.date-filter').hide();
        }
    });
    $('#select_contractor').change(function(){
        if ($('#type').val() == 8) {
            $(':checkbox', '#reserve_account_id_modal').prop('checked', false);
            $('#reserve_account_id, #reserve_account_id_title').val('');
            if ($('#select_contractor').val() == 1) {
                $('.checkAll', '#reserve_account_id_modal').prop('disabled', true);
            } else {
                $('.checkAll', '#reserve_account_id_modal').prop('disabled', false);
            }
        }
    });
    function configureDateFilter(type) {
        var targetDateFilters = dateFilter[type];
        var $dateFilterType = $('#date_filter_type');
        var dateFilterTypeValue = $dateFilterType.val();
        $dateFilterType.empty();
        if (['5','6','15'].indexOf(type) === -1) {
            $('#date-filters input[type=text]').val('');
        }
        if (!targetDateFilters.length) {
            $('#date-filters:visible').hide();
        } else {
            $('#date-filters:not(:visible)').show();
            $('.date-filter :input:disabled, #date_filter_type:disabled').prop('disabled', false);
            $.each(targetDateFilters, function (key, value) {
                $dateFilterType.append($('<option>').attr('value', value).text(dateTypeOptions[value]));
                if (value == dateFilterTypeValue) {
                    $dateFilterType.find('option:last-child').attr('selected', 'selected');
                }
            });

            if ($.inArray(parseInt($dateFilterType.val()), targetDateFilters) < 0) {
                $('#date_filter_type option[disabled!="disabled"]').first().attr('selected', 'selected');
            }
        }
        if (type == '8' || type == '14') {
            $('#date-filters>.control-group').hide();
        } else {
            $('#date-filters>.control-group').show();
        }

    }
    configureDateFilter($('#type').val());

    function configureFileType(type) {
        var $fileType = $('#file_type').empty();
        $.each(fileTypes[type], function (value, name) {
            $fileType.append($('<option>').text(name).attr('value', value));
        });
    }
    configureFileType($('#type').val());

    function configurePeriodFilter(type) {
        var showAll = periodFilter[type];
        if (showAll === true) {
            $('#period option.not-closed-cycle').removeAttr('disabled');
        } else {
            $('#period option.not-closed-cycle').attr('disabled', 'disabled');
        }
        if ($('#period option[value="' + $('#period').val() + '"]').prop('disabled')) {
            $('#period option[value="' + $('#period').val() + '"]').removeAttr('selected');
            var firstPeriod = $('#period option:not(.not-closed-cycle)').first();
            $('#period').val(firstPeriod.attr('value'));
        }
    }

    function configureContractorFilter(type) {

        var $selectContractor = $('#select_contractor').empty();
        $selectContractor.append($('<option>').text('All Contractors').attr('value', 1));
        if (type == 3) {
            $selectContractor.append($('<option>').text('Contractors Mail').attr('value', 3));
            $selectContractor.append($('<option>').text('Contractors Distribute').attr('value', 4));
        }
        $selectContractor.append($('<option>').text('Select Contractor').attr('value', 2));

        $selectContractor.find('option:not(:disabled)').first().prop('selected', true);
        $selectContractor.change();
    }

    function configureViewButton(type) {
        if (["11", "12", "17"].indexOf(type) == -1) {
            $('input#view').removeClass('hide');
        } else {
            $('input#view').addClass('hide');
        }

    }

    $('.additional-filter select').change(function(e){
        if ($(this).val() == 2) {
            $(this).closest('.additional-filter').find('.additional-filter-popup').show();
        } else {
            $(this).closest('.additional-filter').find('.additional-filter-popup').hide();
        }
        if ($(this).find('option[value="1"]').prop('disabled')) {
            if ($(this).closest('.additional-filter').find('.additional-filter-popup').length) {
                $(this).closest('.control-group:visible').hide();
            }
        } else {
            $(this).closest('.control-group:not(:visible)').show();
        }
    });
    $('#year').change(function(e){
        var wrapper = $(this).closest('.date-filter');
        var options = wrapper.find('.period-options .year-options[data-year="' + $(this).val() + '"]').html();
        $('#period').html(options);
        $('#period').val($('#period').find('option').first().val());
        configurePeriodFilter($('#type').val());
    });

    $('#starting_year').change(function(e){
        var options = $('.period-options .year-options[data-year="' + $(this).val() + '"]').html();
        $('#starting_period').html(options);
        $('#starting_period').val($('#starting_period').find('option').first().val());
        configurePeriodFilter($('#type').val());
    });

    $('#ending_year').change(function(e){
        var options = $('.period-options .year-options[data-year="' + $(this).val() + '"]').html();
        $('#ending_period').html(options);
        $('#ending_period').val($('#ending_period').find('option').first().val());
        configurePeriodFilter($('#type').val());
    });

    $('form[name="reporting"] #view, form[name="reporting"] #download').click(function(e){
        $(this).closest('form').removeAttr('target');
        $('#action').val($(this).data('action'));
        if ($(this).data('action') == 1) {
            var $this = $(this);
            $.ajax({
                url: '/reporting_index/validate',
                data: $('form[name=reporting]').serialize(),
                type: 'POST',
                context: $this,
                success: function (response) {
                    var valid = response.valid;
                    var $form = $(this).closest('form');
                    if (valid) {
                        $form.attr('target', '_blank');
                        $form.find('.report-custom-errors').remove();
                    }
                    $form.submit();
                }

            });
            e.preventDefault();
            e.stopPropagation();
        }
    });
    $('#year').change();
    var modals = $('#carrier_vendor_id_modal, #reserve_account_id_modal, #reserve_account_contractor_id_modal, #contractor_id_modal');
    modals.on('item-check.after', function(e, checkbox){
        if (
            ($(this).attr('id') == 'carrier_vendor_id_modal' && ($('#type').val() == 11 || $('#type').val() == 13 || $('#type').val() == 17))
            || ($(this).attr('id') == 'reserve_account_id_modal' && $('#type').val() == 8 && $('#select_contractor').val() == 1)
            || ($(this).attr('id') == 'reserve_account_contractor_id_modal' && $('#type').val() == 14)
            || ($(this).attr('id') == 'contractor_id_modal' && ($('#type').val() == 8 || $('#type').val() == 14))
        ) {
            $(this).find('tbody :checkbox:checked').not(checkbox).prop('checked', false);
        }
    });
    multiselect.init();
});