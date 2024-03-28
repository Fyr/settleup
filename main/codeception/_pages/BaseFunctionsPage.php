<?php
use Codeception\Module\BaseSelectors;

class BaseFunctionsPage extends Codeception\Module
{
    protected $user;
    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

   // Finds and returns the text contents of the given element by css selector
    public function grabTextFromElement($selector)
    {
        return $this->user->executeJS("var optionValues = $('" . $selector . "').text().trim();  return optionValues;");
    }

//Checks visible control element, which is found by selector css
public function seeControlElement($namePage, $selectorTable, $comparedTable)
{
    $getTextElement= array();
    $this->user->see($namePage,'h3');
    $this->user->see('Records');
    $this->user->see('Filter');
    $this->user->see('Period');

    $this->user->seeElement(BaseSelectors::$selector_Records);
    $getTextElement=$this->getTextElement(BaseSelectors::$selector_Records);
    $this->checkArrayDiff(BaseSelectors::$records,$getTextElement);
    $this->user->seeElement(BaseSelectors::$filter_Dropdown_Values);
    $getTextElement=$this->getTextElement(BaseSelectors::$filter_Dropdown_Values);

    if($namePage != \Codeception\Module\Disbursement_Data::$disbursements){
        $this->checkArrayDiff(BaseSelectors::$filter_Dropdown_Value_Original,$getTextElement);
    }
    else{
        $this->checkArrayDiff(\Codeception\Module\Disbursement_Data::$Disbursement_Filter_Dropdown_Value_Original,$getTextElement);
    }

    $this->user->seeElement(BaseSelectors::$period_Dropdown_Values);

    $this->user->seeElement($selectorTable);
    $getTextElement=$this->getTextElement($selectorTable);
    //var_dump($getTextElement);
    $this->checkArrayDiff($comparedTable,$getTextElement);
    //$this->user->seeNumberOfElements('.datagrid.table.table-bordered.additional-cycle-grid',1); //таблица на страрице Payments
}

    // getting all values from element and adds them to the array
    public function getTextElement($selector)
    {
        return $this->user->executeJS("var optionValues = []; $('" . $selector . "').each(function() { optionValues.push(!!$(this).text() && $(this).text().trim()); }); return optionValues;");
    }

    // Find element by text and select it
    public function findTextClickElement($selector, $provide)
    {
        $this->user->executeJS("$('".$selector.":not(.hidden)').filter(function(){return !!$(this).text() && $(this).text().trim()=='".$provide."';}).trigger('click')");
    }


    //Computes the difference of arrays and return false if arrays are different or true vise versa
    public function checkArrayDiff($compared, $input)
    {
        $this->assertEquals($compared, $input);
//        if (count($input)!=count($compared))
//        {
//
//            echo "Check item does not contain the required count of values";
//        }
//        else
//        {
//            for($i=0;$i<count($input);$i++)
//            {
//                if($input[$i]!=$compared[$i])
//                {
//                    echo "check item has wrong values instead $input[$i] should be $compared[$i] \n";
//                }
//                else
//                {
//                    echo "check item has correct values $input[$i] = $compared[$i] \n";
//                }
//            }
//         }
    }

    //Check for an item in the table and return false or true
    public function checkForItem($selector, $item)
    {
        $table = $this->getTextElement($selector);
        for($i=0;$i<count($table);$i++)
            {
                if($item==$table[$i])
                {
                    return false;
                }
            }
        return true;
    }

    public function checkFrequencyElement()
   {
       $this->user->see(BaseSelectors::$frequency_Field);
       $this->user->seeElement(BaseSelectors::$frequency_Dropdown_Locator);
       $frequency=$this->getTextElement(BaseSelectors::$frequency_Values);
       $this->checkArrayDiff($frequency,BaseSelectors::$frequency_Period);

       $this->user->selectOption(BaseSelectors::$frequency_Dropdown_Locator,BaseSelectors::$frequency_Biweekly);
       $this->user->seeOptionIsSelected(BaseSelectors::$frequency_Dropdown_Locator,BaseSelectors::$frequency_Biweekly);
       $this->user->see(BaseSelectors::$start_Date_Field);
       $this->user->seeElement(BaseSelectors::$start_Date_Biweekly_Frequency);
       $this->user->see(BaseSelectors::$select_Days_Field);
       $this->user->selectOption(BaseSelectors::$frequency_Dropdown_Locator,BaseSelectors::$frequency_Monthly);
       $this->user->seeOptionIsSelected(BaseSelectors::$frequency_Dropdown_Locator,BaseSelectors::$frequency_Monthly);
       $this->user->see(BaseSelectors::$select_Days_Field);
       $this->user->seeElement(BaseSelectors::$select_days_Of_Month_Locator);
       $selectDays=$this->getTextElement(BaseSelectors::$select_Days_Of_Month_Values);
       $this->checkArrayDiff($selectDays,BaseSelectors::$days_Of_Month);

       $this->user->selectOption(BaseSelectors::$frequency_Dropdown_Locator,BaseSelectors::$frequency_Semi_Monthly);
       $this->user->seeOptionIsSelected(BaseSelectors::$frequency_Dropdown_Locator,BaseSelectors::$frequency_Semi_Monthly);
       $this->user->see(BaseSelectors::$select_Days_Field);
       $this->user->seeElement(BaseSelectors::$select_Days_Of_Month_Values);
       $selectDays=$this->getTextElement(BaseSelectors::$select_Days_Of_Month_Values);
       $this->checkArrayDiff($selectDays,BaseSelectors::$days_Of_Month);
       $this->user->seeElement(BaseSelectors::$select_Second_Days_Of_Month__Locator);
       $selectDays=$this->getTextElement(BaseSelectors::$select_Second_Days_Of_Month_Values);
       $this->checkArrayDiff($selectDays,BaseSelectors::$days_Of_Month);

       $this->user->selectOption(BaseSelectors::$frequency_Dropdown_Locator,BaseSelectors::$frequency_Semi_Weekly);
       $this->user->seeOptionIsSelected(BaseSelectors::$frequency_Dropdown_Locator,BaseSelectors::$frequency_Semi_Weekly);
       $this->user->see(BaseSelectors::$select_Days_Field);
       $this->user->seeElement(BaseSelectors::$select_Days_Of_Week_Locator);
       $selectDays=$this->getTextElement(BaseSelectors::$select_Days_Of_Week_Values);
       $this->checkArrayDiff($selectDays,BaseSelectors::$days_Of_Week);
       $this->user->seeElement(BaseSelectors::$select_Second_Days_Of_Week_Locator);
       $selectDays=$this->getTextElement(BaseSelectors::$select_Second_Days_Of_Week_Values);
       $this->checkArrayDiff($selectDays,BaseSelectors::$days_Of_Week);

       $this->user->selectOption(BaseSelectors::$frequency_Dropdown_Locator,BaseSelectors::$frequency_Weekly);
       $this->user->wait(1);
       $this->user->seeOptionIsSelected(BaseSelectors::$frequency_Dropdown_Locator, BaseSelectors::$frequency_Weekly);
       $this->user->see(BaseSelectors::$select_Days_Field);
       $this->user->seeElement(BaseSelectors::$select_Days_Of_Week_Locator);
       $selectDays=$this->getTextElement(BaseSelectors::$select_Days_Of_Week_Values);
       $this->checkArrayDiff($selectDays,BaseSelectors::$days_Of_Week);
   }
}