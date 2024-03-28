$('#gridModal.modal thead tr').css('cursor','default');
$('#gridModal.modal tbody tr').css('cursor','pointer');


$(document).ready(function() {
    $('#gridModal.modal tbody tr').bind('click', function() {
        $('#titleField').val($.trim($(this).children('td.titleField').html()));
        $('#idField').val($.trim($(this).children('td.idField').html()));
        $('#gridModal').modal('hide');
    });
});
