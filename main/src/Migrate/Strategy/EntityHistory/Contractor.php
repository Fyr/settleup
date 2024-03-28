<?php

namespace Migrate\Strategy\EntityHistory;

use Application_Model_Base_CryptAdvanced as Crypt;
use Application_Model_Entity_Collection_Entity_History as Collection;
use Application_Model_Entity_Entity_History as EntityHistory;
use Migrate\Strategy\BaseStrategy;
use Migrate\Strategy\StrategyInterface;

class Contractor extends BaseStrategy implements StrategyInterface
{
    public function migrate()
    {
        $crypt = new Crypt();
        $entityHistory = new EntityHistory();
        /** @var Collection $collection */
        $collection = $entityHistory->getCollection();
        $collection->addContractorsTable()->addCarrierFilter($this->carrier->getEntityId());
        /** @var EntityHistory $history */
        foreach ($collection as $history) {
            $data = json_decode((string) $history->getData('data'), true, 512, JSON_THROW_ON_ERROR);
            if (isset($data['tax_id'])) {
                $taxId = $data['tax_id'];
                if (!$this->isEncrypted($taxId)) {
                    $taxId = $crypt->encrypt($taxId, $this->carrierKey);
                    $data['tax_id'] = $taxId;
                    $history->setData('data', json_encode($data, JSON_THROW_ON_ERROR));
                    $history->save();
                }
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
}
