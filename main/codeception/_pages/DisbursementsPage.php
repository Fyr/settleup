<?php
use Codeception\Module\BaseSelectors;
use Codeception\Module\Disbursement_Data;
use Codeception\Module\Input_Data;

class DisbursementsPage
{
    use \Codeception\Util\Shared\Asserts;
    protected $user;

    public function __construct(AcceptanceTester $I)
    {
        $this->user = $I;
    }

    public function disbursementsTransactions($disbursements_Reconciliations)
    {
        $Base = new BaseFunctionsPage($this->user);
        $this->user->click(Disbursement_Data::$disbursements_Menu);
        $this->user->wait(3);
        $recipient = $Base->getTextElement(Disbursement_Data::$disbursements_Recipient_Column);
        $amount = $Base->getTextElement(Disbursement_Data::$disbursements_Amount_Column);
        $disbursements = array();
        for($i=0; $i<count($recipient); $i++){
            $disbursements[$i] = array($recipient[$i],$amount[$i]);
        }
        for ($i = 0; $i < count($disbursements); $i++) {
            for ($q = 0; $q < 2; $q++) {
                if ($disbursements_Reconciliations[$i][$q] != $disbursements[$i][$q]) {
                    $this->fail("Assigned disbursement has a wrong values " . $disbursements_Reconciliations[$i][$q] . " = " . $disbursements[$i][$q]);
                }
            }
        }
    }
}