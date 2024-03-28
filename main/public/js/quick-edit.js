var oldValue = false;
var moneyField = false;
var enterKeyPress = false;

function attachQuickEditHendler() {
    $('body').on('dblclick', '.quick-edit:not(.disabled)', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var quickEditInput = $('#quick-edit-input');
        if (!quickEditInput.length) {
            var value = $(this).text().trim();

            if ($(this).attr('field-type') == 'money') {
                moneyField = true;
                value = value.replace(/\$/g, '').replace(/,/g, '');
            }

            if (value) {
                oldValue = value;
            }
            else {
                oldValue = '―';
            }            


            if (value === '―') {
                value = '';
            }

            var input = '<input id="quick-edit-input" placeholder="&#x2015" value="' + value + '"  type="text">';
            var width = $(this).width();
            $(this).html(input);
            quickEditInput = $('#quick-edit-input');
            $(this).css('width', width + 'px');
            quickEditInput.trigger("focus").trigger("select");
            $(window).on('click', function (event) {
                saveChanges(event)
            });
            $(window).on('keypress', function (event) {
                if (event.target.id == 'quick-edit-input') {
                    var code = (event.key ? event.key : event.which);
                    if (code == "Enter") { //Enter keycode
                        enterKeyPress = true;
                        saveChanges(event);
                    } else {
                        if (code == "Escape") { //escape keycode
                            cancelEditing(oldValue);
                        }
                    }
                }
            });
        }
    });
}


function saveChanges(event) {
    if (event.target.id != 'quick-edit-input' || enterKeyPress) {
        if (oldValue) {
            var input = $('#quick-edit-input');
            var recordId = input.parent().attr('record-id');
            var value = input.val();
            var field = input.parent().attr('field-name');
            var row = input.closest('tr').attr('data-value');
            var fieldType = input.parent().attr('field-type');
            if (!input.closest('.quick-edit').hasClass('nullable')) {
                switch (fieldType) {
                    case 'num':
                        if (isNaN(value)) {
                            value = oldValue;
                        }
                        else {
                            value = parseFloat(value).toFixed(0);
                        }
                        break;
                    case 'money':
                        if (isNaN(value)) {
                            value = oldValue;
                        }
                        else {
                        value = parseFloat(value.replace(/,/g, '')).toFixed(2);
                        }
                        break;
                    case 'text':
                        value = value;
                }


                if (input.parent().is('[max-value]')) {
                    var maxValue = input.parent().attr('max-value');
                    if (parseFloat(value) > parseFloat(maxValue)) {
                        value = maxValue;
                    }
                }
            }

            var grid = input.closest('.datatable');
            var id = grid.data('grid-id');
            var context = this;
            if ((value != oldValue && oldValue !== '―') || (oldValue === '―' && value !== '')) {
                $.ajax({
                    url: '/grid/quickedit',
                    data: {
                        'id': id,
                        'recordId': recordId,
                        'field': field,
                        'value': value
                    },
                    type: 'POST',
                    context: context,
                    beforeSend: function(){
                        grid.find('.quick-edit').addClass('disabled');
                    },
                    complete: function(){
                        grid.find('.quick-edit').removeClass('disabled');
                    },
                    success: function (response) {
                        if (typeof response.value != 'undefined') {
                            value = response.value;
                            cancelEditing(value);
                            // var record = input.closest('tr');
                            if (field == 'rate' || field == 'quantity') {                                
                                countAmount(row);
                            }
                        } else {
                            cancelEditing('');
                        }
                    }
                });
            } else {
                cancelEditing(oldValue);
            }
        }
    }
}

function cancelEditing(value) {
    var input = $('#quick-edit-input');
    if (moneyField) {
        input.parent().addClass('num');
        if (value !== '―') {           
                value = parseFloat(value).toFixed(2);
                if (isNaN(value)) {
                    value = 0;
                }
            value = '$' + addCommas(parseFloat(value).toFixed(2));           
        }
        moneyField = false;
    }
    oldValue = false;
    enterKeyPress = false;
    input.parent().html('<div>' + value + '</div>');
    input.parent().css('width', 'auto');
}

function createInput(value) {
    return input;
}

function countAmount(row) {
    var rate = $("td[record-id=" + row + "][field-name='rate']").text().trim().replace(/\$/g, '').replace(/,/g, '');

    if (Number.isNaN(rate)) {
        rate = 0;
    }
    rate = parseFloat(rate).toFixed(2);

    var qty = $("td[record-id=" + row + "][field-name='quantity']").text().trim();
    if (isNaN(qty)) {
        qty = 0;
    }
    qty = parseFloat(qty.replace(/,/g, '')).toFixed(2);
    var amount = parseFloat(rate * qty).toFixed(2);
    var total = 0;
    $("tr[data-value=" + row + "] td[amount-field]").html('<div>$' + addCommas(amount) + '</div>');
    $('td[amount-field]').each(function () {
        total = total + parseFloat($(this).text().trim().replace(/\$/g, '').replace(/,/g, ''));

    });
    $("tr.totals td.num").html('<div>$' + addCommas(parseFloat(total).toFixed(2)) + '</div>');

}
function addCommas(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1].substr(0, 2) : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
$(function () {
    attachQuickEditHendler();
});

// Amount mask
$(function(){
    $('.mask-money').on("keyup", function () {
        var value = $(this).val().replace(/,/g,'');
        $(this).val(addCommas(value));
    });
});
