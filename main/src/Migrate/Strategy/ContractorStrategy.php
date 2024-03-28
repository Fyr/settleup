<?php

namespace Migrate\Strategy;

use Application_Model_Base_CryptAdvanced as Crypt;
use Application_Model_Entity_Entity_Contractor as Contractor;

class ContractorStrategy extends BaseStrategy implements StrategyInterface
{
    public function migrate()
    {
        $contractorEntity = new Contractor();
        $contractors = $contractorEntity->getCollection()->addFilter('carrier_id', $this->carrier->getEntityId());
        $crypt = new Crypt();

        /** @var Contractor $contractor */
        foreach ($contractors as $contractor) {
            $newSocialSecurity = null;
            $newTaxId = null;
            $socialSecurity = $contractor->getSocialSecurityId();
            $taxId = $contractor->getTaxId();

            // encrypt social security id
            if (!$this->isEncrypted($socialSecurity)) {
                if (filter_var($socialSecurity, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^\d{3}-\d{2}-\d{4}$/']])) {
                    $newSocialSecurity = $socialSecurity;
                } elseif (filter_var($socialSecurity, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^\d{9}$/']])) {
                    $newSocialSecurity = $this->addDashesForSocialSecurityId($socialSecurity);
                } else {
                    $newSocialSecurity = '123-45-6789';
                }
                $newSocialSecurity = $crypt->encrypt($newSocialSecurity, $this->carrierKey);
                $contractor->setSocialSecurityId($newSocialSecurity);
            }

            // encrypt tax id
            if (!$this->isEncrypted($taxId)) {
                if (filter_var($taxId, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^\d{2}-\d{7}$/']])) {
                    $newTaxId = $taxId;
                } elseif (filter_var($taxId, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^\d{9}$/']])) {
                    $newTaxId = $this->addDashesForTaxId($taxId);
                } else {
                    $newTaxId = '12-3456789';
                }
                $newTaxId = $crypt->encrypt($newTaxId, $this->carrierKey);
                $contractor->setTaxId($newTaxId);
            }

            if ($newSocialSecurity || $newTaxId) {
                $contractor->save();
            }
        }
    }

    /**
     * check is encrypted string
     *
     * @param $field
     * @return bool
     */
    protected function isEncrypted($field)
    {
        return (strlen((string) $field) > 30);
    }

    /**
     * add Dashes for 9-digits string for social security id format
     *
     * @param $str
     * @return string
     */
    protected function addDashesForSocialSecurityId($str)
    {
        return substr((string) $str, 0, 3) . '-' . substr((string) $str, 3, 2) . '-' . substr((string) $str, 5, 4);
    }

    /**
     * add Dashes for 9-digits string for Tax ID format
     *
     * @param $str
     * @return string
     */
    protected function addDashesForTaxId($str)
    {
        return substr((string) $str, 0, 2) . '-' . substr((string) $str, 2, 7);
    }
}
