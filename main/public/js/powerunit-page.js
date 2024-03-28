$(function(){
    $('#contractor_code').change(function(e){
        configureContractorId($(this).val());
        configureContractorCompanyName($(this).val());
    });

    configureContractorId($('#contractor_code').val());
    function configureContractorId(code) {
        var contractorId = $('#contractor_id').empty();
        var id = contractors[code]['entity_id'];
        contractorId.append($('<option>').text(id).attr('value', id));
    }
    configureContractorId($('#contractor_code').val());

    configureContractorCompanyName($('#contractor_code').val());
    function configureContractorCompanyName(code) {
        var contractorName = $('#contractor_name').empty();
        var name = contractors[code]['company_name'];
        contractorName.append($('<option>').text(name).attr('value', name));
    }
    configureContractorCompanyName($('#contractor_code').val());
});