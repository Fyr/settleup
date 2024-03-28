<?php

class Application_Views_Helpers_RenderSearchAutocomplete extends Zend_View_Helper_Abstract
{
    public function renderSearchAutocomplete(
        $hintActionName,
        $searchResultActionName
    ) {
        $searchWrapperClass = "search_wrapper";
        $searchInputboxClass = "search_input";

        $searchButtonText = "Search";
        $searchButtonId = "search_button_id";
        $searchButtonClass = "search_button";

        $searchInputboxId = "search_imput_id";

        $resultDivId = "resultDivId";

        $htmlToReturn = "<div class='$searchWrapperClass' >
            <input id='$searchInputboxId' name='q' class='$searchInputboxClass'" . "type='text'  value=''>
            <div id='$searchButtonId' class='$searchButtonClass' >
                <span> $searchButtonText </span>
            </div>
        </div>
        <div id='$resultDivId' style='margin-left:12px;'>result here</div>
        
        </br></br></br></br></br>
                       
                
          <script type='text/javascript'> 
        
        $(document).ready(function() {
            var a = $('#$searchInputboxId').autocomplete({ 
                    serviceUrl:'$hintActionName',
                    minChars:2, 
                    delimiter: /(,|;)\s*/, // regex or character
                    maxHeight:400,
                    width:156,
                    zIndex: 9999,
                    deferRequestBy: 300, //miliseconds
                    onSelect: function(value, data){ 
                        search();
                    }
                });
            });
        
        
        
          $('#$searchButtonId').click(function () {                        
                search();
            });    
        
        function search(){
        
         toSearch = $('#$searchInputboxId').val();
        
            if (toSearch != ''){        
                    var toSend = {data: toSearch}           
                    $.ajax({
                            url: '$searchResultActionName',
                            data: toSend,
                            type: 'GET',                           
                            success: function(response){
                            $('#$resultDivId').html(response);
                            }
                        });    
            }        
        }              
        </script>";

        return $htmlToReturn;
    }
}
