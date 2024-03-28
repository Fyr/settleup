(function($){
    jQuery.fn.pfleetPopup = function(){
        var popup, button;

        var make = function(){
            var element = this;
            $(function(){
                $(element).find('.addSelectedItems').on('click', function(){
                    if (!$(this).attr('disabled')) {
                        button = $(this);
                        popup = $(this).closest('.popup_checkbox_modal');
                        addSelectedItems();
                    }
                });
                $('.popup_checkbox_tab a').click(function(e){
                    e.preventDefault();
                    $(this).tab('show');
                });
                $('.popup_checkbox_tab').each(function(){
                    $(this).find('a:first').tab('show');
                });
            });

        };

        var addSelectedItems = function(){
            var contractors = getSelectedContractors();
            var powerunits = getSelectedPowerunits();
            var setups = getSelectedSetups();
            var cycle = getSettlementCycleId();
            var invoiceDate = getInvoiceDate();

            if (!contractors || !setups || !cycle) {
                button.tooltip('show');
                return;
            }


            button.attr('disabled', 'disabled');
            post(
                button.data("target-url"),
                {
                    'selectedSetup': setups,
                    'selectedContractors': contractors,
                    'selectedPowerunits': JSON.stringify(powerunits),
                    'selectedCycle': cycle,
                    'fromPopup': 'true',
                    'invoiceDate': invoiceDate
                }
            );


//            $.ajax({
//                beforeSend: function(){
//                    button.attr('disabled', 'disabled');
//                },
//                url: button.data("target-url"),
//                data: {
//                    'selectedSetup': setups,
//                    'selectedContractors': contractors,
//                    'selectedCycle': cycle
//                },
//                type: 'POST',
//                success: function (grid) {
//                    window.location.reload();
//                }
//            });
        };

        var getSelectedContractors = function(){
            var data = [];
            var resultArray = [];
            if (popup.find('.tab-pane').length == 2) {
                data = popup.find('.tab-pane:last .checkboxField input:checkbox:checked');
                for (var index = 0; index < data.length; index++) {
                    resultArray.push($(data[index]).val());
                }
            } else {
                data = popup.data('contractor');
                resultArray.push(data);
            }
            return resultArray;
        };

        let getSelectedPowerunits = function(){
            let data;
            let resultArray = {};
            if (popup.find('.tab-pane').length == 2) {
                data = popup.find('.tab-pane:last .checkboxField input:checkbox:checked');
                data.each(function () {
                    resultArray[$(this).val()] = $(this).attr('data-contractor-id');
                });
            } else {
                data = popup.data('powerunit');
                resultArray.push(data);
            }
            return resultArray;
        };

        var getSelectedSetups = function(){
            var resultArray = [];
            if (popup.find('.tab-pane').length == 2) {
                var data = popup.find('.tab-pane:first .checkboxField input:checkbox:checked');
            } else {
                var data = popup.find('.checkboxField input:checkbox:checked');
            }
            for (var index = 0; index < data.length; index++) {
                resultArray.push($(data[index]).val());
            }

            return resultArray;
        };

        var getSettlementCycleId = function(){
            var cycle = popup.find('.settlement-cycle-data');
            var id = 0;
            if (cycle.length) {
                id = cycle.data('cycle-id');
            }
            return id;
        };

        var getInvoiceDate = function(){
            var date = popup.find('.invoice-date').val();
            if (date) {
                return date;
            } else {
                return '';
            }
        };

        return this.each(make)
    };
    function post(path, params, method){
        method = method || "post";

        var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", path);

        for (var key in params) {
            if (params.hasOwnProperty(key)) {
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
})(jQuery);

$(function(){
    $('.popup_checkbox_modal').pfleetPopup();
});
