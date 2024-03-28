import Cookies from "js-cookie";
//import $ from "jquery"

const percentageLimitType = 1;


/** TAGS:
 *  #recurring
 *  #popup
 *  #permissions_checkbox
 *  #contractor_status_selector
 *  #compensations
  */
$(function () {
    $('#importing-cycle-type').change(function(){
        var options = $('#cycle-options').find('.options[data-status="' + $(this).val() + '"]').html();
        $('#importing-cycle').html(options);
        if ($(this).val() == 3) {
            $('#importing-cycle-type-info:not(:visible)').show();
        } else {
            $('#importing-cycle-type-info:visible').hide();
        }
    });
    $('#importing-cycle-type').closest('body').find('#approve').click(function(e){
        if ($('#importing-cycle').val() == '0') {
            e.preventDefault();
            e.stopPropagation();
            $(this).popover('show');
        } else {
            window.location.href = $(this).attr('href') + '/cycle/' + $('#importing-cycle').val()
            $(this).addClass('disabled');
        }
    });
    $('#importing-cycle-type').closest('body').find('#approve').popover({
        title: "Warning!",
        content: "Settlement cycle isn't selected!",
        placement: 'left',
        trigger: 'manual'
    });
    $('#importing-cycle-type').closest('body').find('#approve').mouseleave(function(){
        $(this).popover('hide');
    });

    $(".btn[disabled='disabled']").click(function (e) {
        e.preventDefault();
    });


    function post(path, params, method, target) {
        method = method || "post";

        var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", path);
        if (typeof target != 'undefined') {
            form.setAttribute("target", target);
        }

        for(var key in params) {
            if(params.hasOwnProperty(key)) {
                var hiddenField;
                if (typeof params[key] == 'object') {
                    for (var index in params[key]) {
                        hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key + '[]');
                        hiddenField.setAttribute("value", params[key][index]);
                        form.appendChild(hiddenField);
                    }

                } else {
                    hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", key);
                    hiddenField.setAttribute("value", params[key]);
                    form.appendChild(hiddenField);
                }
            }
        }        

        document.body.appendChild(form);
        form.submit();
    }

    $('body').on('click', '.contractor-settlement-statement-report', function(e){
        e.preventDefault();
        var data = {
            action:1,
            type:3,
            date_filter_type:1,
            year:'',//'2014',
            period:$(this).data('cycle-id'),
            range_start_date:"",
            range_end_date:"",
            select_contractor:2,
            contractor_id:'[' + $(this).data('contractor-id') + ']',
            contractor_id_title:$(this).data('company-name'),//'Serezha5',
            select_reserve_account:1,
            reserve_account_id:'[]',
            reserve_account_id_title:"",
            select_carrier_vendor:1,
            carrier_vendor_id:'[]',
            carrier_vendor_id_title:""
        };
        post($(this).attr('href'), data, 'post', '_blank');
    });

    function getClosestFriday(date) {        
        if (date.getDay() === 5) {
            date.setDate(date.getDate() + 7);
        } else {
            date.setDate(date.getDate() + ((5 + 7 - date.getDay()) % 7));
        }
        return date.toLocaleDateString('en-US');
    }

    $('input:checkbox[readonly], select[readonly]').click(function(e){e.preventDefault();e.stopPropagation();});

    addMask();

    $('input.date').datepicker();

    $('input.date-picker').datepicker({
            range: 'period',
            numberOfMonths: 1,
            dateFormat: 'mm/dd/yy',
        });

    $('#cycle_period_id, #cycle_start_date', 'form[name="settlement_cycle"]').change(function(){
        if (!$('form[name="settlement_cycle"] input[name="id"]').val()) {
            $('#cycle_close_date').val('');
        }
    });

    $('#cycle_close_date').on('change', function () {        
        var timestamp = Date.parse($('#cycle_close_date').val());
        var dateObject = new Date(timestamp);
        $("#disbursement_date").val(getClosestFriday(dateObject));
    })

    // #payments
    if ($('form[name="payments"]').length) {
        $("#invoice_date:not(:disabled,[readonly])").datepicker({dateFormat: 'mm/dd/yy', changeYear: true});
        $("#shipment_complete_date:not(:disabled,[readonly])").datepicker({dateFormat: 'mm/dd/yy', changeYear: true});
        $("#invoice_due_date:not(:disabled,[readonly])").datepicker({dateFormat: 'mm/dd/yy', changeYear: true});
        $("#disbursement_date:not(:disabled,[readonly])").datepicker({dateFormat: 'mm/dd/yy', changeYear: true});
        $("#cycle_close_date:not(:disabled,[readonly])").datepicker({dateFormat: 'mm/dd/yy', changeYear: true});
        if ($('#recurring').attr('checked') == null) {
            $('#billing_cycle_id').parent().parent().css('display', 'none');
            $('#first_start_day').parent().parent().css('display', 'none');
            $('#second_start_day').parent().parent().css('display', 'none');
        }
    }


    if ($('form[name="payment_setup"], form[name="deductionsetup"]').length) {
        $("#biweekly_start_day:not(:disabled,[readonly])").datepicker({
            changeYear: true,
            changeMonth: true,
            onSelect: function() {
                var dateSelect = $(this).datepicker('getDate');
                var dayOfWeek = $.datepicker.formatDate('DD', dateSelect);
                $(this).change();
                $('#start_date').val(dayOfWeek);
            }
        });
    }

    // #recurring


    $('form[name="deductionsetup"] #billing_cycle_id, form[name="payment_setup"] #billing_cycle_id, form[name="settlement_cycle_rule"] #cycle_period_id, form[name="settlement_cycle"] #cycle_period_id').change(function () {
        switch(parseInt($(this).val())) {

            case 1:
                $('#semi-monthly-fields:visible').hide();
                $('#week_offset_wrapper:visible').hide();
                $('#second_week_day_wrapper:visible').hide();
                $('#second_week_day_wrapper:not(:visible)').hide();
                //$('#week_day_wrapper label[for="week_day"]').text('Select Day of Week');
                if (!$('#cycle_period_id').length) {
                    $('#week_day_wrapper:not(:visible)').show();
                } else {
                    $('#week_day_wrapper:visible').hide();
                }
                $('#week_day option:last-child').prop('disabled', false);

                break;
            case 2:
                $('#semi-monthly-fields:visible').hide();
                $('#second_week_day_wrapper:visible').hide();
                $('#week_day_wrapper:visible').hide();
                //$('#week_day_wrapper label[for="week_day"]').text('Select Day of Week');
                if (!$('#cycle_period_id').length) {
                    $('#week_offset_wrapper:not(:visible)').show();
                }
                break;
            case 3:
                $('#week_day_wrapper:visible').hide();
                $('#week_offset_wrapper:visible').hide();
                if ($(this).attr('id') != 'cycle_period_id') {
                    $('#semi-monthly-fields:not(:visible)').show();
                    $('#second_start_day_wrapper:visible').hide();
                    //$('#semi-monthly-fields label[for="first_start_day"]').text('Select Day of Month');
                    $('#first_start_day option').prop('disabled', false);
                } else {
                    $('#semi-monthly-fields:visible').hide();
                }
                break;
            case 4:
            case 5:
                $('#week_day_wrapper:visible').hide();
                $('#week_offset_wrapper:visible').hide();
                $('#semi-monthly-fields:not(:visible)').show();
                $('#second_start_day_wrapper:not(:visible)').show();
                //$('#semi-monthly-fields label[for="first_start_day"]').text('Select Days of Month');
                $('#first_start_day option:gt(28)').prop('disabled', true);
                $('#first_start_day').change();
                break;
            case 6:
                $('#semi-monthly-fields:visible').hide();
                $('#week_offset_wrapper:visible').hide();
                $('#week_day_wrapper:not(:visible)').show();
                $('#second_week_day_wrapper:not(:visible)').show();
                $('#week_day option:last-child').prop('disabled', true);
                $('#second_week_day option:first-child').prop('disabled', true);
                //$('#week_day_wrapper label[for="week_day"]').text('Select Days of Week');
                break;
        }
    });
    $(function(){
        $('form[name="deductionsetup"] #billing_cycle_id, form[name="payment_setup"] #billing_cycle_id').trigger('change');
    });
    $('#first_start_day', 'form[name="deductionsetup"], form[name="payment_setup"], form[name="settlement_cycle_rule"], form[name="settlement_cycle"]').change(function(){
        var currentValue = parseInt($(this).val());
        if (parseInt($('#second_start_day').val()) <= currentValue) {
            $('#second_start_day').val(currentValue + 1);
        }
        $('#second_start_day option').prop('disabled', false);
        $('#second_start_day option:lt(' + (currentValue) + ')').prop('disabled', true);
    });
    $('#second_start_day', 'form[name="deductionsetup"], form[name="payment_setup"], form[name="settlement_cycle_rule"], form[name="settlement_cycle"]').change(function(){
        var currentValue = parseInt($(this).val());
        if (parseInt($('#first_start_day').val()) >= currentValue) {
            $('#first_start_day').val(currentValue - 1);
        }
//        $('#first_start_day option:gt(' + (currentValue - 2) + ')').prop('disabled', true);
    });
    $('#week_day', 'form[name="deductionsetup"], form[name="payment_setup"], form[name="settlement_cycle_rule"], form[name="settlement_cycle"]').change(function(){
        var currentValue = parseInt($(this).val());
        if (parseInt($('#second_week_day').val()) <= currentValue) {
            $('#second_week_day').val(currentValue + 1);
        }
        $('#second_week_day option').prop('disabled', false);
        $('#second_week_day option:lt(' + (currentValue + 1) + ')').prop('disabled', true);
    });
    $('#second_week_day', 'form[name="deductionsetup"], form[name="payment_setup"], form[name="settlement_cycle_rule"], form[name="settlement_cycle"]').change(function(){
        var currentValue = parseInt($(this).val());
        if (parseInt($('#week_day').val()) >= currentValue) {
            $('#week_day').val(currentValue - 1);
        }
//        $('#first_start_day option:gt(' + (currentValue - 2) + ')').prop('disabled', true);
    });
    $('#week_day').change();
    $('form[name="settlement_cycle_rule"] #change-cycle-rule, form[name="deductionsetup"] #recurring, form[name="payment_setup"] #recurring').click(function () {
        var defaultData = {};
        if (!$('#change_cycle_rule_fields').val()) {
            $('#change_cycle_rule_fields').val(JSON.stringify({
                first_start_date: $('#first_start_date').val(),
                second_start_date: $('#second_start_date').val(),
                cycle_start_date: $('#cycle_start_date').val(),
                cycle_period_id: $('#cycle_period_id').val(),
                billing_cycle_id: $('#cycle_period_id').val(),
                week_day: $('#week_day').val(),
                second_week_day: $('#second_week_day').val()
            }));

        }
        if ($('#change-cycle-rule-fields').css('display') == 'none') {
            defaultData = {
                first_start_date: $('#first_start_date').val(),
                second_start_date: $('#second_start_date').val(),
                cycle_start_date: $('#cycle_start_date').val(),
                cycle_period_id: $('#cycle_period_id').val(),
                billing_cycle_id: $('#cycle_period_id').val(),
                week_day: $('#week_day').val(),
                second_week_day: $('#second_week_day').val()
            }
            $('#cycle_period_id').removeAttr('readonly', 'readonly');
            $('#cycle_period_id option').removeAttr('disabled');
            $('form[name="deductions"] #billing_cycle_id, form[name="payments"] #billing_cycle_id, form[name="settlement_cycle_rule"] #cycle_period_id, form[name="settlement_cycle"] #cycle_period_id').change();
            $('#change-cycle-rule-fields').show();
            $('#change_cycle_rule_fields').val(JSON.stringify(defaultData));
            $('#billing_cycle_id, #cycle_period_id').change();
        } else {
            $('#cycle_period_id').attr('readonly', 'readonly');
            defaultData = JSON.parse($('#change_cycle_rule_fields').val());
            for (var field in defaultData) {
                $('#' + field).val(defaultData[field]);
            }
            $('#cycle_period_id option:not([value="' + defaultData.cycle_period_id + '"])').attr('disabled', 'disabled');
            $('#billing_cycle_id, #cycle_period_id').change();
            $('#change-cycle-rule-fields').hide();
        }
    });

    $('select[readonly="readonly"] option:not(:selected)').attr('disabled',true);



    if (document.referrer.indexOf(window.location.hostname) != -1) {
        var cancel = $("a.cancel");
        if (!cancel.attr('href') || cancel.attr('href') == '#') {
            cancel.attr('href', document.referrer);
        }
    }

    $('body').on('click', '.confirm', function (e) {
        e.preventDefault();

        var targetUrl = $(this).attr('target-url');
        var message = $(this).attr('confirm-message');
        var deleteSecondMessage = $(this).attr('delete-second-message');
        if (targetUrl == undefined) {
            targetUrl = $(this).attr('href');
        }
        var confirmType = $(this).attr('confirm-type');
        if (confirmType == 'Deletion') {
            confirmType = 'Deleting';
        }

        $('#confirm-modal .confirm-type').html($(this).attr('confirm-type'));
        $('#confirm-modal .modal-body .confirm-type').html(confirmType);
        if (!$(this).data('confirm-description')) {
            if ($(this).data('confirm-description-title')) {
                $('#confirm-modal .modal-body .confirm-type').html($(this).data('confirm-description-title'));
            }
        } else {
            $('#confirm-modal .modal-body .confirm-description').html($(this).data('confirm-description'));
        }

        if ($(this).data('confirm-title')) {
            $('#confirm-modal .modal-header>h3').html($(this).data('confirm-title'));
        }


        $('#confirm-modal #btn-confirm').attr('href', targetUrl);
        if ($(this).hasClass('recurring')) {
            if ($('#confirm-modal #btn-confirm-all').length) {
                $('#confirm-modal #btn-confirm-all').remove();
            }
            var n = targetUrl.indexOf('?');
            var newTargetUrl;
            if (n > 0) {
                newTargetUrl = [targetUrl.slice(0, n), '/all/true', targetUrl.slice(n)].join('');
            } else {
                newTargetUrl = targetUrl + '/all/true';
            }

            $('#confirm-modal #btn-confirm').before('<a href="' + newTargetUrl + '" class="btn btn-danger" id="btn-confirm-all">Yes for all</a>')
        } else {
            $('#confirm-modal #btn-confirm-all').remove();
        }
        if (message != undefined) {
            $('#confirm-modal .modal-body p:first').html(message);
        }
        if (deleteSecondMessage != undefined) {
            $('#confirm-modal .modal-body p:last').html('');
        }

        $('#confirm-modal').modal('show');
    });

//<----------------[ Contractors Edit start]----------------]
    // delete vendor of the contractor
    $('body').on('click', '.contractors-del-vendor', function(e){
        e.preventDefault();
        if($(this).hasClass('disabled')) return;
        var elem = $(this).closest('.vendors_list');
        elToDelete = {vendor:elem.find('.element_status').attr('name').replace(/^\D+/g, '')};
        $.ajax({
            url: '/contractors_index/deletevendor',
            data: elToDelete,
            type: 'POST',
            success: function(){
                elem.remove();
            }
        });
    });

    // add new vendor for the contractor
    $('body').on('click', '.contractors-add-vendor', function(e){
        e.preventDefault();
        if(typeof counter == 'undefined') {
            counter = 1000;
        }

        var newVendor = $(".hide > .new-contractor-vendor").clone();
        newVendor.appendTo($(this).closest('.vendors_list'));
        newVendor.find('[name]').each(function(){
          $(this).attr('name', $(this).attr('name').split(/\d+/)[0] + counter);
          $(this).attr('id', $(this).attr('id').split(/\d+/)[0] + counter);
        });

        $('.contractor-vendors').each(function(){
          $(this).find('.controls .btn-danger').removeClass('disabled');
        });
        counter++;
    });
    //<----------------[ Contractors Edit end]----------------]

    //<----------------[ FreezeState start ]----------------]

  // restore state
  $('body').on('click', 'ul.list-db-states a', function(e){
    e.preventDefault();
    $('.preloader').fadeIn();
    processState({state: $(this).text()}, '/freeze/restore');
  });
    // save current state
    $('body').on('click', '.freeze-save-new a.save-state', function(e){
        e.preventDefault();
        $('.preloader').fadeIn();
        var stateDesc = $('input.new-state-desc').val();
        processState({desc:stateDesc},'/freeze/save')
    });

  function processState(data, url) {
      $.ajax({
          url: url,
          data: data,
          type: 'POST',
          success: function (response) {
              $('.preloader').fadeOut();
              $('#freeze-notification .modal-body').html(response);
              $('#freeze-notification').modal('show');
          }
      });
  }

  // close modal window
    $('#freeze-notification').on('hide', function() {
      window.location = '/freeze';
  });

//<----------------[ FreezeState end]----------------]



    $('#expires, .date-filter input, #start_date,#dob, #termination_date, #rehire_date, #shipment_complete_date').datepicker({dateFormat: 'mm/dd/yy', changeYear: true, yearRange: '-100:+100'});
    $('.modal-footer input.invoice-date').datepicker({
        dateFormat: 'mm/dd/yy',
        maxDate: $('.modal-footer input.invoice-date').data('maxdate'),
        changeYear: true,
        yearRange: '-100:+100',
        beforeShow: function(input) {
            var datepickerTop = input.getBoundingClientRect().top - 5;
            setTimeout(function(){
                datepickerTop -= $('#ui-datepicker-div').height();
                $('#ui-datepicker-div').css('top',datepickerTop + 'px');
            },1);
        }
    })
});

function URLToArray(url) {
    var request = [];
    var pairs = url.substring(url.indexOf('?') + 1).split('&');
    for (var i = 0; i < pairs.length; i++) {
        var pair = pairs[i].split('=');
        request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
    }
    return request;
}

/*--------------RA Contractor/Deduction Setup-------------------------*/
$(document).ready(function() {
    $('form[name="reserve_account_contractor"]').parents('body').on('click','#entity_id_modal.modal tbody tr:not(.placeholder)', function(event) {
        updateCarrierVendor($(this).find('.idField').text());
        $('form[name="reserve_account_contractor"]').change();
    });
    $('form[name="reserve_account_contractor"], form[name="deductionsetup"]').parents('body').on('click','#vendor_id_modal.modal tbody tr:not(.placeholder), #provider_id_modal.modal tbody tr:not(.placeholder)', function(event) {
        getRA($(this).find('.idField').html());
        $('form[name="reserve_account_contractor"], form[name="deductionsetup"]').change();
    });
    $('form[name="reserve_account_contractor"]').parents('body').on('click', '#reserve_account_vendor_id_modal.modal tbody tr:not(.placeholder)', function() {
        getRASetup($(this).find('.idField').html());
        $('form[name="reserve_account_contractor"]').change();
    });
    $('form[name="reserve_account_contractor"]').on('click', '#vendor_id_clear', function(){
        $('#reserve_account_vendor_id_clear').click();
        $('form[name="reserve_account_contractor"]').change();
    });
    $('form[name="reserve_account_contractor"]').on('click', '#reserve_account_vendor_id_clear', function(){
        clearSetup();
        $('form[name="reserve_account_contractor"]').change();
    });

    function updateCarrierVendor(entityId) {
        $.ajax({
            url:updateCarrierVendorUrl,
            data:{'contractor-id': entityId},
            type: 'POST',
            beforeSend: function() {
                $('#vendor_id_title').addClass('input-preloader').val('').attr('readonly','readonly');
            },
            success: function(data){

                $('#vendor_id_modal').next('script').remove();
                $('#vendor_id_clear').remove();
                $('#vendor_id_modal').next('script').remove();
                $('#vendor_id_modal').remove();
                $('#vendor_id_title').val('');
                $('#vendor_id').val('');
                data = JSON.parse(data);

                $('.content').append(data.popup);
                $('#vendor_id_clear, #reserve_account_vendor_id_clear').click();
                $('#current_contractor_name').text(data.contractorTitle);
            },
            complete: function() {
                $('#vendor_id_title').removeClass('input-preloader').removeAttr('readonly');
            }
        });
    }
    function changeContractor(entityId, entityTitle) {
        $.ajax({
            url:changeCurrentContractorUrl + entityId,
            type: 'GET'
        });
        $('#current_contractor_name').html(entityTitle);
    }
    function getRA(entityId) {
        $.ajax({
            url:getRAUrl,
            data:{'vendorEntityId': entityId},
            type: 'POST',
            beforeSend: function() {
                $('#reserve_account_vendor_id_title, #reserve_account_receiver_title').addClass('input-preloader').attr('readonly','readonly');
            },
            success: function(popup){
                $('#reserve_account_vendor_id_modal, #reserve_account_receiver_modal').next('script').remove();
                $('#reserve_account_vendor_id_clear, #reserve_account_receiver_clear').remove();
                $('#reserve_account_vendor_id_modal, #reserve_account_receiver_modal').next('script').remove();
                $('#reserve_account_vendor_id_modal, #reserve_account_receiver_modal').remove();
                $('#reserve_account_vendor_title, #reserve_account_receiver_title').val('');
                $('#reserve_account_vendor_id, #reserve_account_receiver_id').val('');
                $('#vendor_reserve_code').val('');
                $('.content').append(popup);
                $('#reserve_account_vendor_id_clear, #reserve_account_receiver_clear').click();
            },
            complete: function() {
                $('#reserve_account_vendor_id_title, #reserve_account_receiver_title').removeClass('input-preloader').removeAttr('readonly');
            }
        });
    }
    function getRASetup(raId) {
        $.ajax({
            url:getRASetupUrl,
            data:{'raId': raId},
            type: 'POST',
            beforeSend: function() {
                $('#vendor_reserve_code').addClass('input-preloader');
                $('#vendor_reserve_code').val('');
            },
            success: function(setup){
                for (var field in setup) {
                    $('#' + field).val(setup[field]);
                }
            },
            complete: function() {
                $('#vendor_reserve_code').removeClass('input-preloader');
            }
        });
    }
    function clearSetup() {
        var setupFieldsArray = JSON.parse(setupFields);
        for (var i = 0; i <= setupFieldsArray.length; i++) {
            if ($('#' + setupFieldsArray[i]).hasClass('mask-money')) {
                $('#' + setupFieldsArray[i]).val('0.00');
            } else {
                $('#' + setupFieldsArray[i]).val('');
            }
        }
    }

    //disbursement

    $(document).ready(function() {
        $('form[name="transactions_disbursement"]').parents('body').on('click','#entity_id_modal.modal tbody tr', function(event) {
            if ($(this).find('td:not(.placeholder)').length) {
                $('form[name="transactions_disbursement"]').change();
            }

        });
        $('form[name="transactions_disbursement_reissue"] #disbursement_date').datepicker({dateFormat: 'mm/dd/yy', changeYear: true});

        $('form[name="transactions_disbursement"]').on('click', '#entity_id_clear', function(){
            $('form[name="transactions_disbursement"]').change();
        });
    });

    //Contractor Reserve Transaction
    $(document).ready(function() {
        $('form[name="reserve_account_transaction"]').parents('body').on('click','#reserve_account_contractor_modal.modal tbody tr', function(event) {
            if ($(this).find('td:not(.placeholder)').length) {
                $.ajax({
                    url:getRASetupUrl,
                    data:{'raId': $(this).find('.idField').html()},
                    type: 'POST',
                    beforeSend: function() {
                        $('#vendor_name, #reserve_code, #reserve_account_vendor, #current_balance, #description').val('').addClass('input-preloader');
                    },
                    success: function(setup){
                        for (var field in setup) {
                            $('#' + field).val(setup[field]);
                        }
                    },
                    complete: function() {
                        $('#vendor_name, #reserve_code, #reserve_account_vendor, #current_balance, #description').removeClass('input-preloader');
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        $('form[name="deductionsetup"]').parents('body').on('click','#provider_id_modal.modal tbody tr', function(event) {
            if ($(this).find('td:not(.placeholder)').length) {
                $.ajax({
                    url:getProviderSetupUrl,
                    data:{'providerId': $(this).find('.idField').html()},
                    type: 'POST',
                    beforeSend: function() {
                        $('#terms').val('').addClass('input-preloader');
                    },
                    success: function(setup){
                        for (var field in setup) {
                            $('#' + field).val(setup[field]);
                        }
                    },
                    complete: function() {
                        $('#terms').removeClass('input-preloader');
                    }
                });
            }
        });
    });
    if ($('#deduction_amount').val()) {
        $('#deduction_amount').val("-" + $('#deduction_amount').val());
    }
    $('form[name="deductions"]').on('submit', function () {
        if ($('#deduction_amount').val() && $('#deduction_amount').val().charAt(0) == "-") {
            $('#deduction_amount').val($('#deduction_amount').val().substr(1));
        }
    });


    // #grid

    $('.btnFilterContractors').on("click", function (event) {
        event.preventDefault();
        filterContractors();
    });
    $('#rec_per_page.settlement-page').on("change", function (event) {
        filterContractors();
    });
    $('#settlement_division.settlement-page').on("change", function (event) {
        filterContractors();
    });
    function filterContractors() {
        var filterParams = '';
        $('input.settle').each(function(){
            if($(this).val()) {
                var paramName = $(this).attr('id');
                var paramValue = $(this).val();
                filterParams += '&' + paramName + '=' + paramValue;
            }
        });
        if ($('#settlement_division').val() == '0') {
            filterParams += '&limit=' + $('#rec_per_page').val() + '&filter=update';
        } else {
            filterParams += '&limit=' + $('#rec_per_page').val() + '&filter=update' + '&division=' + $('#settlement_division').val();
        }
        if ($('.header .sorting.asc, .header .sorting.desc').length) {
            filterParams += '&sort=' + $('.header .sorting.asc, .header .sorting.desc').closest('th').data('sort') + '&order=' + (($('.header .sorting.asc, .header .sorting.desc').hasClass('asc'))? 'asc' : 'desc');
        }

        if(filterParams.length) {
            window.location.href = '/settlement_index?' + filterParams.substr(1);
        }
    }

    $('.btnClearFilterContractors').click(function (event) {
        event.preventDefault();
        window.location.href = '/settlement_index?filter=update&limit=' + $('#rec_per_page').val();
    });

    $('.filterInput.settle').keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) { //Enter keycode
            filterContractors();
        }
    });
});

//carrier, vendor, contractor and user contact fields
$(function(){
    var subformCount = 0;
    $('form[name="carrier"], form[name="vendor"], form[name="user_info"], form[name="contractor"]').find('.subform').each(function(){
        ++subformCount;
    });
    $('form[name="carrier"], form[name="vendor"], form[name="user_info"], form[name="contractor"]').on('click', '.delete:not(.disabled)', function(e){
        e.preventDefault();
        var subform = $(this).parents('.subform');
        var contactType = subform.data('contact-type');
        if (contactType == 'vendor') {
            var oldValue = subform.find('.select[name*="vendor_id"]').val();
        }
        if (contactType == 'vendor' && !subform.siblings(':not(.nondisplay, #attached-carrier)').length) {
            subform.find('select[name*="status"]').closest('.vendor-status-wrapper').addClass('nondisplay');
            subform.find('select[name*="vendor_id"] option[value!="0"]').attr('disabled', 'disabled');
            subform.find('select[name*="vendor_id"]').attr('readonly', 'readonly').val(0);
            subform.find('.delete').addClass('disabled');
        } else {
            var siblingSubforms = subform.siblings(':not(.nondisplay)[data-contact-type=' + contactType +']');
            if (siblingSubforms.length == 1 && contactType != 'vendor') {
                siblingSubforms.find('.delete').addClass('disabled');
            }
            subform.addClass('nondisplay');
        }
        if (subform.find('input[name*="id"]').val() || (contactType == 'vendor' && !subform.siblings(':not(.nondisplay, #attached-carrier)').length)) {
            subform.find('input[name*="deleted"]').val(1);
            subform.find('input[name*="vendor_acct"]').val('');
        } else {
            subform.remove();
        }
        if (contactType == 'vendor') {
            fixVendorOptions(oldValue);
        }
        $('form[name="carrier"], form[name="vendor"], form[name="user_info"], form[name="contractor"]').change();
    });
    $('form[name="carrier"], form[name="vendor"], form[name="user_info"], form[name="contractor"]').on('click', '.add:not(.disabled)', function(e){
        e.preventDefault();
        var subform = $(this).parents('.subform');
        var contactType = subform.data('contact-type');
        if (contactType == 'vendor' && !subform.siblings(':not(.nondisplay, #attached-carrier)').length && subform.find('select[name*="vendor_id"]').val() == 0 && subform.find('select[name*="status"]').closest('.vendor-status-wrapper').hasClass('nondisplay')) {
            subform.find('select[name*="status"]').closest('.vendor-status-wrapper').removeClass('nondisplay');
            subform.find('select[name*="vendor_id"] option').removeAttr('disabled');
            subform.find('select[name*="vendor_id"]').removeAttr('readonly');
            subform.find('input[name*="deleted"]').val(0);
        } else {
            var count = 0;
            do {
                count = 1000 + (++subformCount);
            } while ($('.subform input[name="contacts[' + count + '][id]"]').length);

            var newSubform = subform.clone();
            subform.after(newSubform);
            newSubform.find('.control-group').removeClass('error');
            newSubform.find('.error').remove();
            newSubform.find('input[name*="id"], input[name*="value"], .address-fields input').val('');
            if (contactType == 'vendor') {
                newSubform.find('select, input').val(0).find('option').removeAttr('disabled');
                newSubform.find('input[type=text]').val('');
            }
            if (contactType == 'entity') {
                newSubform.find('input').val('');
            }

            newSubform.find('input, select').each(function(){
                $(this).attr('name', $(this).attr('name').replace(/\[\d+\]/,'[' + count + ']'));
            });

            if (contactType == 'vendor') {
                newSubform.find('select[name*="vendor_id"]').removeAttr('readonly');
            }
        }
        $('form .subform[data-contact-type="' + contactType + '"] .delete').removeClass('disabled');
        if (contactType == 'vendor') {
            fixVendorOptions(0);
        }
        addMask();
        $('form[name="carrier"], form[name="vendor"], form[name="user_info"], form[name="contractor"]').change();
    });
    $('form[name="carrier"], form[name="vendor"], form[name="user_info"], form[name="contractor"]').submit(function(e){
        var hasEmptyBlocks = false;
        $('.address-block').each(function(){
            var data = {};
            var isEmpty = true;
            $(this).find('.address-fields input').each(function(){
                data[$(this).attr('name')] = $(this).val();
                if ($(this).val()) {
                    isEmpty = false;
                }
            });
            $(this).find('.address-fields select').each(function(){
                data[$(this).attr('name')] = $(this).val();
                if ($(this).val()) {
                    isEmpty = false;
                }
            });
            var excludeName = $(this).closest('.subform').find('input[name*="deleted"]').attr('name');
            if (isEmpty && $(this).closest('form').find('subform[data-contact-type=1] input[name*="deleted"][value!="1"]:not([name="'+ excludeName + '"])').length) {
                $(this).siblings('dd').find('input[name*="deleted"]').val(1);
            }
            $(this).find('.address-data-holder input').val(JSON.stringify(data));
        });
    });
    $('form[name="contractor"]').on('focus', 'select[name*="vendor_id"][readonly!="readonly"]', function() {
        $(this).data('oldValue', $(this).val());
    });
    $('form[name="contractor"]').on('change', 'select[name*="vendor_id"]', function() {
        fixVendorOptions($(this).data('oldValue'));
    });
    function fixVendorOptions(oldValue) {
        var selectedValues = [];
        $('form[name="contractor"] .subform:not(.nondisplay) select[name*="vendor_id"]').each(function(){
            if (parseInt($(this).val())) {
                selectedValues.push($(this).val());
            }
        });
        $('form[name="contractor"] .subform:not(.nondisplay) select[name*="vendor_id"]').each(function(){
            if (!parseInt($(this).closest('.subform').find('input[name*="id"]').val())) {
                for (var i = 0; i < selectedValues.length; i++) {

                    if ($(this).val() != selectedValues[i]) {
                        var option = $(this).find('option[value="' + selectedValues[i] + '"]');
                        option.attr('disabled', 'disabled');
                    }
                }
                if (oldValue) {
                    $(this).find('option[value="' + oldValue + '"]').removeAttr('disabled');
                }
            }
        });
    }
    $('body').on('click', '.remove-attach', function(e){
        e.preventDefault();
        let attachmentId = $(this).attr('id');
        $('.preloader').fadeIn();
        $.ajax({
            url: '/contractors_index/delete-attachment',
            data: {attachmentId: attachmentId},
            type: 'POST',
            success: function(){
                $('.preloader').fadeOut();
                window.location.reload();
            }
        });
    });
    $('body').on('click', '.add-attach', function(e){
        e.preventDefault();
        e.target.blur();
        let counter= $('#file1-element input').length,
            newAttach = $(".attachment_list .hide").clone();
        newAttach.appendTo($(this).prev('.attachment_list').find('#file1-element'));
        newAttach.removeClass('hide');
        newAttach.attr('id', newAttach.attr("id").split(/\d+/)[0] + counter);
        newAttach.attr('name', newAttach.attr("name").split(/\d+/)[0] + counter);
    });
});

//user info
$(function(){
    $('form[name="user_info"] #change-password').click(function () {
        var defaultData = {}
        if ($('#change-password-fields').css('display') == 'none') {
            $('#change-password-fields').show();
        } else {
            $('#change-password-fields').hide();
            $('#change-password-fields input').val('');
            $('form[name="user_info"] #confirm_password').parents('.control-group').removeClass('error').removeClass('success');
            $('form[name="user_info"] #submit').removeClass('disabled');
        }
    });

    $('form[name="user_info"] #new_password').on('change keyup blur', function(){
        if ($('form[name="user_info"] #new_password').val()) {
            $('form[name="user_info"] #confirm_password').trigger('change');
        }
    });

    $('form[name="user_info"] #confirm_password').on('change keyup blur', function(){
        if ($(this).val() != $('form[name="user_info"] #new_password').val()) {
            $(this).parents('.control-group').addClass('error').removeClass('success');
            $('form[name="user_info"] #submit').addClass('disabled');
        } else {
            $(this).parents('.control-group').removeClass('error').addClass('success');
            $('form[name="user_info"] #submit').removeClass('disabled');
        }
    });

    $('form').submit(function(e){
        if ($(this).find('input.disabled[type="submit"]').length > 0) {
            e.preventDefault();
        }
    });
    $('form[name="user_info"]').on('click', '.entity-subform input[id*="entity_id_title"]', function(){
        $('#multiple_entity_id_modal').data('subform', $(this).closest('.entity-subform')).modal('show');
    });

});
//vendors on contractor form

//disbursement
$(function(){
    $('body').on('click', '.create-disbursement-transaction', function(e){
        e.preventDefault();
        window.location = $(this).attr('href') + '/cycle/' + $('#settlement_cycle_id_filter').val();
    });
});

$(function(){
    $('body').on('click', '.user-menu a[href*="/auth/logout"]', function(){
        clearCookies();
    });
});

var clearCookies = function () {
    Cookies.remove("settlement_cycle_id");
    Cookies.remove("settlement_cycle_filter_type");
    Cookies.remove("settlement_cycle_filter_year");
}
function addMask() {
    $('form input.phone').mask('(000) 000-0000', {placeholder: "(###) ###-####"});
    $('form input[name="tax_id"]').mask('00-0000000', {placeholder: "##-#######"});
    $('form input[name="holder_federal_tax_id"]').mask('00-0000000', {placeholder: "##-#######"});
    $('form input[name="social_security_id"]').mask('000-00-0000', {placeholder: "###-##-####"});
    $('form input[name="zip"]').mask('00000-0000', {placeholder: "#####-####"});
    $('form input[id="contractor_tax_id"]').mask('0000', {placeholder: "####"});
    $('form input[id="contractor_social_security_id"]').mask('0000', {placeholder: "####"});
};

$(function () {
    $('.additional-cycle-grid a[confirm-type="approve"]:not([disabled="disabled"])').click(function () {
        Cookies.set("settlement_cycle_filter_type", 3, {path: '/'})
    })
});

// #permissions_checkbox
$(function(){

    $('.permissions dl').each(function(){
        if (!$(this).find('dd :checkbox:not(:checked)').length) {
            $(this).find('dt :checkbox').prop('checked', true);
        }
        //attachCheckAllListener($(this).find('dt :checkbox'));
    });

    $('.permissions .check-all').change(function(){
        if ($(this).hasClass('initialized')) {
            if ($(this).prop('checked')) {
                $(this).closest('dt').siblings('dd').find(':checkbox').prop('checked', true).attr('value', '1');
            } else {
                $(this).closest('dt').siblings('dd').find(':checkbox').prop('checked', false).attr('value', '0');
            }
        } else {
            $(this).addClass('initialized');
        }

    });

    //function attachCheckAllListener(element) {
    //    $(element).change(function(){
    //        if ($(this).prop('checked')) {
    //            $(this).closest('dt').siblings('dd').find(':checkbox').prop('checked', true).attr('value', '1');
    //        } else {
    //            $(this).closest('dt').siblings('dd').find(':checkbox').prop('checked', false).attr('value', '0');
    //        }
    //    })
    //}

    $('.permissions dd :checkbox').change(function(){
        var checkAll = $(this).closest('dd').siblings('dt').find('.check-all');
        if ($(this).prop('checked')) {
            if ($(this).closest('dd').siblings('dd').find(':checkbox:not(:checked)').length) {
                checkAll.prop('checked', false).attr('value', '0');
            } else {
                checkAll.prop('checked', true).attr('value', '1');
            }
        } else {
            checkAll.prop('checked', false).attr('value', '0');
        }
    });
});

$(document).on('click', 'a.disabled', function(e){
    e.preventDefault();
    e.stopPropagation();
    return false;
});

$(document).on('click', '#submit.disabled', function(e){
    e.preventDefault();
    e.stopPropagation();
    return false;
});

$(function(){
    $('input[data-toggle=modal]').keydown(false);
});

$(function () {
    $('div.modal').on('show', function() {
        var id = $(this).attr('id'),
            relatedTarget = $('a[data-target=#' + id + ']'),
            okBtn = $(this).find('.btn-danger');
        okBtn.attr('href', relatedTarget.attr('href'));
    });
});

$(function(){
    $("form").not("[name=reporting]").preventDoubleSubmission();
});

$(function () {
    $('.label.show-errors').popover({
        placement: 'left',
        template: '<div class="popover" style="width:350px"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
    });
});

$(function () {
    $('.has-error.show-errors:nth-child(-n+2) div').popover({
        placement: 'right',
        template: '<div class="popover" style="width:350px;text-align:left"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
    });
});

$(function () {
    $('.has-error.show-errors div').popover({
        placement: 'top',
        template: '<div class="popover" style="width:350px"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
    });
});



$(function() {
    if ($('#current_carrier_name').html() == '') {
        $('#change_carrier_modal').modal('show');
    }
    $('#change_carrier_modal.modal tbody tr').on('click', function(event) {
        $('#change_carrier_modal').modal('hide');

        clearCookies();
        window.location.replace(
            '/index/changecurrentcarrier/currentController/' +
            $('#change_carrier_modal').data('controller') +
            "/selectedCarrierId/" + $.trim($(this).children('td.idField').html()) + "/entityId/" + $.trim($(this).children('td.entityField').html()));
    });
});

$(function() {
    if ($('#current_settlement_group_name').html() == '') {
        $('#change_settlement_group_modal').modal('show');
    }
    $('#change_settlement_group_modal.modal tbody tr').on('click', function(event) {
        $('#change_settlement_group_modal').modal('hide');

        clearCookies();
        window.location.replace(
            '/index/changecurrentsettlementgroup/currentController/' +
            $('#change_settlement_group_modal').data('controller') +
            "/selectedSettlementGroupId/" + $.trim($(this).children('td.idField').html()));
    });
});
