import Cookies from "js-cookie";

$(function() {
    /* sorting */
    $('.datatable').on('click', '.sorting', function() {
        sortIt($(this));
    });

    $('.datatable').on('click', '.checkAll', function () {
        var grid = $(this).closest(".datatable");
        grid.find('input:checkbox').attr('checked', $(this).is(':checked')).trigger('change');
    });


    $('.datatable').data('checkboxes', {})
        .on('click','input.checkbox', function() {
        var grid = $(this).closest('.datatable');
        if ($(this).prop('checked')) {
            grid.data('checkboxes')[$(this).val()] = 1;
        } else {
            delete grid.data('checkboxes')[$(this).val()];
        }
    });

    $('.addSelectedItems').attr('disabled', 'disabled');
    $('.datatable').on('change','.popup_checkbox_modal .checkbox', function () {
        var disabled = false;
        $(this).closest('.popup_checkbox_modal').find('.datatable').each(function () {
            if (!$(this).find('.checkbox:checked').length) {
                disabled = true;
            }
        });
        var button = $(this).closest('.popup_checkbox_modal').find('.addSelectedItems');
        if (disabled) {
            button.attr('disabled', 'disabled');
        } else {
            button.removeAttr('disabled');
        }
    });

    var cycle = $("#settlement_cycle_id_filter");
    if (cycle.length) {
        $(".settlement-cycle-data").val(cycle.find('option:selected').text()).attr('data-cycle-id', cycle.val());
        cycle.on('change', function () {
            $(".settlement-cycle-data").val(cycle.find('option:selected').text()).attr('data-cycle-id', cycle.val());
        });
    }

    $('.datatable:not(.settlement-grid) .rowlimit_wrapper_select').change(function(event) {
        if (typeof grid == 'undefined') {
            var grid = $(".datatable").first();
        }

        var toSend = {};
        toSend.id = grid.data('grid-id');
        toSend.filter = getFilters(grid.data('grid-id'));
        toSend.limit = $(this).val();
        $.ajax({
            url: '/grid/limitgridrow',
            data: toSend,
            type: 'POST',
            success: function(response){
                var data = jQuery.parseJSON(response);
                grid.find("table").not(".additional-cycle-grid").find("tbody").html(data.text);
                grid.find("table").not(".additional-cycle-grid").find("tr.header").html(data.header);
                $('#pager_wrapper_id').html(data.pager);
                if (grid.find('table.fixable-head').length) {
                    var headerCells = grid.find('table.fixable-head thead tr:first-child th');
                    var bodyCells = grid.find('.datagrid:not(.additional-cycle-grid) tbody tr:first-child td:not(.placeholder)');
                    for (var i = 0; i <= bodyCells.length; i++) {
                        $(bodyCells[i]).css('width', $(headerCells[i]).width() + 'px');
                    }
                }
                if (grid.find('.dragrow').length) {
                    grid.data('beforeList', getListOfElts());
                }
            }
        });
    });

    $('#settlement_cycle_id_filter').change(function () {
        var selectedCycle = $(this).find('option:selected').val();
        if (selectedCycle > 0 && selectedCycle != Cookies.get("settlement_cycle_id")) {
            Cookies.remove("settlement_cycle_id");
            Cookies.set("settlement_cycle_id", selectedCycle, {path: '/'});
        }
        if ($(this).hasClass('reload')) {
            location.reload();
        } else {
            sendReq();
        }

    });

    if ($('#settlement_cycle_filter_type').val() == 4) {
        $("#Application_Model_Entity_Deductions_Deduction .table-controll .row.right").show();
        $("#Application_Model_Entity_Payments_Payment .table-controll .row.right").show();
    }


    $('#settlement_cycle_filter_type, #settlement_cycle_filter_year').change(function () {
        changeCyclePeriods();
        var cycle = $('#settlement_cycle_id_filter');
        Cookies.remove("settlement_cycle_id");
        Cookies.set("settlement_cycle_id", cycle.val(), {path: '/'});
        if (cycle.val() >= 0) {
            if (cycle.hasClass('reload')) {
                location.reload();
            } else {
                sendReq();
            }
        }
    });


    $('.btnFilter').click(function () {
        var grid = $(this).closest('.datatable');
        sendReq(grid);
    });


    $('.btnClearFilter').click(function () {
        var grid = $(this).closest('.datatable');
        grid.find('.datagrid:not(.additional-cycle-grid) tr.filter .filterInput:not(.contractor-status-selector)').each(function () {
            this.value = '';
        });
        sendReq(grid);
        $(grid).find('tbody.dragrow').sortable('enable');
    });

    $('.datatable:not(.additional-cycle-grid) .filterInput').keypress(function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) { //Enter keycode
            var grid = $(this).closest('.datatable');
            sendReq(grid);
        }
    });

    $('.checkboxField input:checkbox').on('change', function () {
        if (!$(this).attr('checked')) $(this).parents('.datatable').find('.checkAll').attr('checked', false);
    });

    $('.datatable').on('click', '.btn-multiaction', function (e) {
        e.preventDefault();
        var grid = $(this).parents('.datatable');
        $('#action-type').val($(this).attr('action-type'));
        var values = getSelectedItems(grid);
        if (values.length == 0) {
            return;
        }
        $('#ids').val(values);
        $('#multiaction').attr('action', $(this).attr('href'));
        if (typeof $(this).attr('confirm-type') != 'undefined') {
            var confirmType = $(this).attr('confirm-type');
            if (confirmType == 'Deletion') {
                confirmType = 'Deleting';
            }
            $('#confirm-modal .confirm-type').html($(this).attr('confirm-type'));
            $('#confirm-modal .modal-body .confirm-type').html(confirmType);
            if ($(this).data('confirm-description-title')) {
                $('#confirm-modal .modal-body .confirm-type').html($(this).data('confirm-description-title'));
            }
            $('#btn-confirm').attr('href', '');
            if (hasSelectedRecurringItems(grid)) {
                if ($('#confirm-modal #btn-confirm-all').length) {
                    $('#confirm-modal #btn-confirm-all').remove();
                }
                $('#confirm-modal #btn-confirm').before('<a href="" class="btn btn-danger" id="btn-confirm-all">Yes for all</a>')
            } else {
                $('#confirm-modal #btn-confirm-all').remove();
            }
            $('#btn-confirm-all').bind('click', function (e) {
                e.preventDefault();
                var action = $('#multiaction').attr('action');
                if (action.search('/all/true') == -1) {
                    if (action.indexOf("?") > -1) {
                        action = action.replace("?", "/all/true?");
                    } else {
                        action += "/all/true";
                    }
                    $('#multiaction').attr('action', action);
                }
                $('#multiaction').submit();
            });
            $('#btn-confirm').bind('click', function (e) {
                e.preventDefault();
                var action = $('#multiaction').attr('action');
                if (action.search('/all/true') > -1) {
                    action.replace('/all/true', '');
                }
                $('#multiaction').submit();
            });
            $('#confirm-modal').modal('show');
        } else {
            $('#multiaction').submit();
        }

    });

    $('.addSelectedItems').tooltip({
        "title": "Please choose at least one value on each tab and settlement cycle",
        "placement": "left",
        "trigger": "manual"
    });
    $('.modal-footer').mouseleave(function () {
        $('.addSelectedItems').tooltip('hide');
    });

    //pagination
    $('#pager_wrapper_id').on('click', 'a', function (e) {
        e.preventDefault();
        sendPager($(this));
    });

    $('.popup_checkbox_tab a').click(function(){
        var tabs = ['#contractors-payment-setup', '#contractors-deduction-setup', '#contractors-contribution', '#contractors-withdrawal'];
        if (tabs.indexOf($(this).attr('href')) > -1) {
            $(this).closest('.popup_checkbox_modal').find('.modal-footer .contractor-status').closest('.span').show();
        } else {
            $(this).closest('.popup_checkbox_modal').find('.modal-footer .contractor-status').closest('.span').hide();
        }
    });

    $('.popup_checkbox_modal .modal-footer select.contractor-status').change(function(){
        var grid = $(this).closest('.popup_checkbox_modal').find('#updatethis-Application_Model_Entity_Entity_Contractor').closest('.datatable');
        var statusFilter = $(grid).find('#status');
        if (!statusFilter.length) {
            grid.append('<input type="text" class="filterInput hidden" value="1" name="status" id="status">');
            statusFilter = $(grid).find('#status');
        }
//        var config = Grid.getInstance(grid.data('grid-id'));
        if (parseInt($(this).val())) {
//            var allTypeIndex = config.customFilters.indexOf('addFilterByCarrierContractor');
//            if (allTypeIndex > -1) {
//                config.customFilters.push('addFilterByActiveCarrierContractor');
//                config.customFilters.splice(allTypeIndex, 1);
//                sendReq(grid);
//            }
            if (parseInt(statusFilter.val()) != 1) {
                statusFilter.val(1).attr('value', 1);
                sendReq(grid);
            }
        } else {
//            var activeTypeIndex = config.customFilters.indexOf('addFilterByActiveCarrierContractor');
//            if (activeTypeIndex > -1) {
//                config.customFilters.push('addFilterByCarrierContractor');
//                config.customFilters.splice(activeTypeIndex, 1);
//                sendReq(grid);
//            }
            if (parseInt(statusFilter.val()) == 1) {
                statusFilter.val('').attr('value', '');
                sendReq(grid);
            }
        }
    });


    //end pagination

    if ($('.content table.fixable-head thead').length) {
        var thead = $('.content table.fixable-head thead');
        var tbody = $('.content table.fixable-head tbody');
        var table = $('.content table.fixable-head');
        var breakPoint = thead.height();
        thead.css('width', thead.width() - 2);
        table.css('width', table.width());
        tbody.find('tr:first-child td').each(function(){
            $(this).css('width', $(this).width());
        });
        thead.find('tr:first-child th').each(function(){
            $(this).css('width', $(this).width());
        });
        $(window).scroll(function(){
            if (thead.hasClass('fixed')) {
                if (tbody[0].getBoundingClientRect().top  >= breakPoint) {
                    thead.removeClass('fixed');
                    table.css('margin-top', 'auto');
                }
                thead.css({
                    'left': $(this).scrollLeft() * -1 + 40
                });
            } else {
                if (tbody[0].getBoundingClientRect().top <= breakPoint) {
                    thead.addClass('fixed');
                    thead.css({
                        'left': 40
                    });
                    table.css('margin-top', breakPoint + 'px');
                }
            }
        });
    }

    $('body').on('change', '.datatable .filter-wrapper .additional-filters select.filterInput.contractor-status-selector', function(){
        var e = $.Event('keypress');
        e.which = 13;
        $(this).trigger(e);
    });



    // #popup

    $('.modal.simple.multigrid .nav-tabs li:first-child, .modal.simple.multigrid .tab-content .tab-pane:first-child').toggleClass('active');
    $('body').on('click', '.modal.simple.multigrid .nav-tabs a', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    $('body').on('click', '.modal.simple tbody tr', function(e){
        var modal = $(this).closest('.modal');
        $('#' + modal.data('dest-field') + '_title').val($(this).data('title'));
        $('#' + modal.data('dest-field')).val($(this).data('value')).change();
        modal.modal('hide');
    });


// #contractor_status_selector
    var contractorStatus = $('.popup_checkbox_modal .modal-footer select.contractor-status');
    contractorStatus.each(function () {
        if ($(this).val() == 1) {
            var grid = $(this).closest('.popup_checkbox_modal').find('#updatethis-Application_Model_Entity_Entity_Contractor').closest('.datatable');
            grid.append('<input type="text" class="filterInput hidden" value="1" name="status" id="status">');
        }
    });

});



function sendReq(grid) {

    if (typeof grid == 'undefined') {
        var grid = $(".datatable").first();
    }
    $(grid).find('tbody.dragrow').sortable('disable');

    var toSend = {};

    toSend.filter = getFilters(grid.data('grid-id'));
    toSend.id = grid.data('grid-id');
    toSend.limit = $('.rowlimit_wrapper_select', grid).val();

    if (grid.hasClass('settlement-grid')) {
        window.location = '/settlement_index/';
    } else {
        $.ajax({
            context: grid,
            url: '/grid/filter',
            data: toSend,
            type: 'POST',
            success: function (response) {
                var data = jQuery.parseJSON(response);
                var body = $(data.text);
                if ($(this).closest('.setup').length) {
                    var checkboxes = $(this).data('checkboxes');
                    for (var v in checkboxes) {
                        body.find('.checkbox[value=' + v + ']').prop('checked', true);
                    }
                }

                $(this).find('.datagrid:not(.additional-cycle-grid) tbody').html(body);
                if ($(this).find('table.fixable-head').length) {
                    var headerCells = $(this).find('table.fixable-head thead tr:first-child th');
                    var bodyCells = $(this).find('.datagrid:not(.additional-cycle-grid) tbody tr:first-child td:not(.placeholder)');
                    for (var i = 0; i <= bodyCells.length; i++) {
                        $(bodyCells[i]).css('width', $(headerCells[i]).width() + 'px');
                    }
                }
                $('#pager_wrapper_id').html(data.pager);
                var buttons = $(this).find('.table-controll > div.row.right');
                if ('buttons' in data) buttons.html(data.buttons);
                if ($(this).find('.additional-cycle-grid').length && typeof data.cycle != 'undefined') {
                    $(this).find('.additional-cycle-grid').replaceWith(data.cycle);
                }
                //$(this).closest('.popup_checkbox_modal').find('.add-selected-items').attr('disabled','disabled');
                $(this).find('input[type=checkbox].checkAll').removeAttr('checked');
                if (!$(this).closest('.modal-body').find(':checkbox:checked').length) {
                    $(this).closest('.popup_checkbox_modal').find('.addSelectedItems').prop('disabled');
                    $(this).closest('.popup_checkbox_modal').find('.add-selected-items').addClass('disabled');
                }
            }
        });
    }
}

function getFilters(gridId) {
    var objectData = {};
    if (gridId) {
        var filterSelector = '.datatable[data-grid-id="' + gridId + '"] .filterInput[value!=""]';
    } else {
        var filterSelector = '.filterInput';
    }

    $(filterSelector).each(function (index) {

        var value;
        if (this.value != null) {
            if ($(this).hasClass('hasDatepicker')) {
                var dateArray = this.value.split("/");
                value = dateArray[2] + "-" + dateArray[0] + "-" + dateArray[1];
            } else if (this.id == 'taxable') {
                value = (this.value.toLowerCase() == 'yes') ? 1 : 0;
            } else {
                value = this.value;
            }
        } else {
            value = '';
        }





        if (objectData[this.id] != null) {
            if (!objectData[this.id].push) {
                objectData[this.id] = [objectData[this.id]];
            }

            objectData[this.id].push(value);
        } else {
            objectData[this.id] = value;
        }

    });

    return objectData;
}

function sortIt(coll) {

    var nameToSort = $(coll).attr('name');
    var grid = $(coll).closest('.datatable');
    var limit = $('.rowlimit_wrapper_select', grid).val();
    var toSend = {};

    $.extend(toSend, {sortCol: nameToSort, limit: limit});
    toSend.filter = getFilters(grid.data('grid-id'));
    toSend.id = grid.data('grid-id');

    $.ajax({
        context: grid,
        url: '/grid/sortgridcoll',
        data: toSend,
        type: 'POST',
        success: function (response) {
            var data = jQuery.parseJSON(response);
            $(this).find('.datagrid:not(.additional-cycle-grid) tbody').html(data.text);
            if ($(this).find('table.fixable-head').length) {
                var headerCells = $(this).find('table.fixable-head thead tr:first-child th');
                var bodyCells = $(this).find('.datagrid:not(.additional-cycle-grid) tbody tr:first-child td:not(.placeholder)');
                for (var i = 0; i <= bodyCells.length; i++) {
                    $(bodyCells[i]).css('width', $(headerCells[i]).width() + 'px');
                }
            }
            $(this).find('.header th.sorting').each(function () {
                if ($(this).attr('name') == data.sort.sort) {
                    $(this).attr('class', 'sorting ' + data.sort.order);
                } else {
                    $(this).attr('class', 'sorting ');
                }
            });
            if ($(this).find('.additional-cycle-grid').length && typeof data.cycle != 'undefined') {
                $(this).find('.additional-cycle-grid').replaceWith(data.cycle);
            }
            $('#pager_wrapper_id').html(data.pager);
        }
    });
}

function getSelectedItems(grid) {
    var selectedItemsId = [];
    var selectedItems = grid.find('.datagrid:not(.additional-cycle-grid) tbody input:checkbox:checked');
    for (var index = 0; index < selectedItems.length; index++) {
        selectedItemsId.push((selectedItems[index].value));
    }

    return selectedItemsId;
}
function hasSelectedRecurringItems(grid) {
    return (grid.find('.datagrid:not(.additional-cycle-grid) tbody input.recurring:checkbox:checked').length > 0);
}
function changeCyclePeriods() {
    var selectedType = $('#settlement_cycle_filter_type').val();
    var selectedPeriods = $('#cycle-filter').find('.filter-type-' + selectedType);
    if (selectedType == 1) {
        var selectedYear = $('#settlement_cycle_filter_year').val();
        selectedPeriods = selectedPeriods.find('.periods-by-year[data-year="' + selectedYear + '"]');
        Cookies.set("settlement_cycle_filter_year", selectedYear, {path: '/'});
        $('#settlement_cycle_filter_year_wrapper').css('display', 'inline');
    } else {
        $('#settlement_cycle_filter_year_wrapper').hide();
    }
    $('#settlement_cycle_id_filter').html(selectedPeriods.html());
    if (selectedType == 4) {
        $('#settlement_cycle_id_filter').val($('#settlement_cycle_id_filter option').last().attr('value'));
    }
    Cookies.set("settlement_cycle_filter_type", selectedType, {path: '/'})
}

function sendPager(a) {

    var page = a.data('page');
    var grid = a.closest('.datatable');
    var limit = $('.rowlimit_wrapper_select', grid).val();
    var toSend = {};
    $.extend(toSend, {pager: page, limit: limit});
    toSend.id = grid.data('grid-id');
    toSend.filter = getFilters(grid.data('grid-id'));

    $.ajax({
        context: grid,
        url: '/grid/pager',
        data: toSend,
        type: 'POST',
        success: function (response) {
            var data = jQuery.parseJSON(response);
            $(this).find('.datagrid:not(.additional-cycle-grid) tbody').html(data.text);
            if ($(this).find('table.fixable-head').length) {
                var headerCells = $(this).find('table.fixable-head thead tr:first-child th');
                var bodyCells = $(this).find('.datagrid:not(.additional-cycle-grid) tbody tr:first-child td:not(.placeholder)');
                for (var i = 0; i <= bodyCells.length; i++) {
                    $(bodyCells[i]).css('width', $(headerCells[i]).width() + 'px');
                }
            }
            $('#pager_wrapper_id').html(data.pager);
            if ($(this).find('.additional-cycle-grid').length && typeof data.cycle != 'undefined') {
                $(this).find('.additional-cycle-grid').replaceWith(data.cycle);
            }
        }
    });
}

$(function () {
    var sortable = $('tbody.dragrow');
    var buttons = sortable.closest('.datatable').find('.priority-buttons');
    buttons.find('.priority-save').click(function () {
        sendUpdated($(this).closest('.datatable').find('tbody.dragrow'));
    });
    sortable.each(function () {
        $(this).data('beforeList',getListOfElts(this));
    });

    sortable.sortable({
        placeholder: "highlight",
        cursor: 'move',
        revert: true,
        helper: fixHelper,
        axis: 'y',
        items: "tr:not(.not-sortable-item, .totals)",
        start: function (event, element) {
            var filter = $(element.item[0]).data('filter');
            if (typeof filter != 'undefined') {
                $(this).find('tr:not([data-filter=' + filter + '])').addClass('not-sortable-item');
            }
            $(this).sortable('refresh');
            element.placeholder.html("<td colspan='" + $('tbody tr td').length + "'></td>");
            return event;
        },
        stop: function (event, element) {
            var filter = $(element.item[0]).data('filter');
            $(this).find('tr:not([data-filter=' + filter + '])').removeClass('not-sortable-item');
            $(this).sortable('refresh');
            if (objectEqual($(this).data('beforeList'), getListOfElts(this))) {
                $(this).closest('.datatable').find('.priority-buttons').css('display', 'none');
            } else {
                $(this).closest('.datatable').find('.priority-buttons').css('display', 'inline-block');
            }
            //sendUpdated(this, filter);
        }
    });
});

function getListOfElts(sortable, filter) {

    var resultList = {};

    if (typeof filter == 'undefined') {
        filter = '';
    } else {
        filter = '[data-filter="' + filter + '"]';
    }
    $(sortable).find('tr[data-value]' + filter).each(function (priority, element) {
        if ($(element).data('filter')) {
            if (typeof resultList[$(element).data('filter')] == 'undefined') {
                resultList[$(element).data('filter')] = [];
            }
            resultList[$(element).data('filter')].push($(element).data('value'));
        } else {
            if (typeof resultList[0] == 'undefined') {
                resultList[0] = [];
            }
            resultList[0].push($(element).data('value'));
        }
    });
    return resultList;
}

var fixHelper = function (event, element) {
    var index = 0;
    var theadThArr = $('.sortable-title thead tr th');
    element.children().each(function () {
        $(theadThArr[index++]).width($(this).width());
        $(this).width($(this).width());
    });
    return element;
};

function sendUpdated(sortable, element) {
    var grid = $(sortable).closest('.datatable');
    var toSend = {};
    toSend.filter = getFilters(grid.data('grid-id'));
    toSend.id = grid.data('grid-id');
    var resultList = getListOfElts(sortable);
    var beforeList = $(sortable).data('beforeList');

    for (var filter in beforeList) {
        if (beforeList.hasOwnProperty(filter)) {
            if (arraysEqual(beforeList[filter], resultList[filter])) {
                delete beforeList[filter];
                delete resultList[filter];
            }
        }
    }
    $(sortable).data('beforeList', beforeList);

    $.extend(toSend, {
        limit: $('.rowlimit_wrapper_select').val(),
        beforeList: beforeList,
        currentPage: $('#pager_wrapper_id .active a').text().replace(/[^0-9]/g, ''),
        resultList: resultList
    });

    $.ajax({
        context: grid,
        url: '/grid/changepriority',
        data: toSend,
        type: 'POST',
        success: function (response) {
            $(this).find('.priority-buttons').css('display', 'none');
            $(this).find('tbody.dragrow').data('beforeList', getListOfElts(sortable));
            $(this).find('tbody').html(response);
            if ($(this).find('table.fixable-head').length) {
                var headerCells = $(this).find('table.fixable-head thead tr:first-child th');
                var bodyCells = $(this).find('.datagrid:not(.additional-cycle-grid) tbody tr:first-child td:not(.placeholder)');
                for (var i = 0; i <= bodyCells.length; i++) {
                    $(bodyCells[i]).css('width', $(headerCells[i]).width() + 'px');
                }
            }
            $(this).find('tbody.dragrow').each(function(){
                $(this).find('tr:not(.totals)').each(function(index, element){
                    $(element).attr('data-order', index);
                })
            });

        }
    })
}


function objectEqual(a, b) {
    return JSON.stringify(a) === JSON.stringify(b);
}

function arraysEqual(a, b) {
    if (a === b) return true;
    if (a == null || b == null) return false;
    if (a.length != b.length) return false;

    for (var i = 0; i < a.length; ++i) {
        if (a[i] !== b[i]) return false;
    }
    return true;
}

$(function(){
    $('.datatable .dragrow').each(function(){
        $(this).find('tr:not(.totals)').each(function(index, element){
            $(element).attr('data-order', index);
        })
    });
    $('.priority-cancel').click(function(){
        var $list = $(this).closest('.datatable').find('.dragrow'),
            $items = $list.children('tr:not(.totals)');
        $items.sort(function(a,b){
            var an = parseInt(a.getAttribute('data-order')),
                bn = parseInt(b.getAttribute('data-order'));
            if(an > bn) return 1;
            if(an < bn) return -1;
            return 0;
        });
        $items.detach().appendTo($list);
        $(this).closest('.datatable').find('tr.totals').detach().appendTo($list);
        $(this).parent().hide();
    })
});


