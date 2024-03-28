$(function () {
    $('#gridModal.modal tr').bind('click', function() {
        $('#name').val($.trim($(this).children('td.name').html()));
        $('#id').val($.trim($(this).children('td.id').html()));
        $('#gridModal').modal('hide');
    });
});