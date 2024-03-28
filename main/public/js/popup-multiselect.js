$(function(){
    if (typeof multiselect == 'undefined') {
        multiselect = {
            init: function(grid) {
                if (typeof grid != 'undefined') {
                    var grids = $(grid);
                } else {
                    var grids = $('.modal.multiselect');
                }

                grids.each(function(){
                    $(this).find('.nav.nav-tabs li').first().toggleClass('active');
                    $(this).find('.tab-content div').first().toggleClass('active');
                });
                this.bindAddItemsObserver();
                this.bindCheckboxObserver();
                grids.each(function(){
                    multiselect.appendClearButton($(this));
                });
                this.bindClearObserver();
                this.initTabs();
                grids.each(function() {
                    multiselect.defaultSetup($(this));
                });
            },
            bindCheckboxObserver: function() {
                $('body').on('change', 'tbody :checkbox', function(e) {
                    var tbody =  $(this).closest('tbody');
                    var modal = tbody.closest('.multiselect');
                    modal.trigger('item-check.before', [$(this)]);
                    var selectAll = modal.find('thead .select-all');
                    if (selectAll.prop('checked') && !$(this).prop('checked')) {
                        selectAll.prop('checked', false);
                    }
                    if (modal.find('tbody :checkbox:checked').length) {
                        modal.find('.add-selected-items').removeClass('disabled');
                    } else {
                        modal.find('.add-selected-items').addClass('disabled');
                    }
                    modal.trigger('item-check.after', [$(this)]);
                });
                $('body').on('change', ':checkbox.select-all', function(){
                    var modal = $(this).closest('.multiselect');
                    modal.trigger('all-item-check.before', [$(this)]);
                    if ($(this).prop('checked')) {
                        $(this).closest('table').find('tbody :checkbox').prop('checked', true);
                    } else {
                        $(this).closest('table').find('tbody :checkbox').prop('checked', false);
                    }
                    if (modal.find('tbody :checkbox:checked').length) {
                        modal.find('.add-selected-items').removeClass('disabled');
                    } else {
                        modal.find('.add-selected-items').addClass('disabled');
                    }
                    modal.trigger('all-item-check.after', [$(this)]);
                });
            },
            bindAddItemsObserver: function() {
                $('body').on('click', '.multiselect .add-selected-items:not(.disabled)', function(){
                    var grid = $(this).closest('.multiselect');
                    var destFieldName = grid.data('dest-field');
                    var selectedItems = multiselect.getSelectedItems(grid);
                    $('#' + destFieldName + '_title').val(selectedItems.title);
                    $('#' + destFieldName).val(JSON.stringify(selectedItems.id));
                    grid.modal('hide');
                });
                $('body').on('click', '.disabled', function(e){
                    e.preventDefault();
                });
            },
            getSelectedItems: function(grid) {
                var selectedItems = {
                    title: '',
                    id: []
                };
                grid.find('tbody :checkbox:checked').each(function(){
                    selectedItems.title += $(this).data('title') + ', ';
                    selectedItems.id.push($(this).data('id'));
                });
                if (selectedItems.title.length) {
                    selectedItems.title = selectedItems.title.slice(0, -2);
                }
                return selectedItems;
            },
            appendClearButton: function(grid) {
//                var destFieldName = grid.data('dest-field');
//                $('#' + destFieldName + '_title').after('<div class="btn btn-small btn-danger clear-popup" data-dest-field="' + destFieldName + '">X</div>');
            },
            bindClearObserver: function() {
                $('body').on('click', '.popup-clear', function(){
                    var destFieldName = $(this).data('dest-field');
                    $('#' + destFieldName, '#' + destFieldName + '_title').val('');
                });
            },
            initTabs: function() {
                $('.multigrid .nav.nav-tabs a').click(function (e) {
                    e.preventDefault();
                    $(this).tab('show');
                })
            },
            defaultSetup: function(grid) {
                var destFieldName = grid.data('dest-field');
                $('#' + destFieldName).val();
                var ids = JSON.parse($('#' + destFieldName).val());
                if (ids.length) {
                    for (var i = 0; i < ids.length; i++) {
                        grid.find('tbody :checkbox[data-id="' + ids[i] + '"]').prop('checked', true);
                    }
                    grid.find('.add-selected-items').removeClass('disabled');
                }
            }
        }
    }
});