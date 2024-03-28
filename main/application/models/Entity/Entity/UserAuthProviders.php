<?php

class Application_Model_Entity_Entity_UserAuthProviders extends Application_Model_Base_Entity
{
    final public const PROVIDER_TYPE_AZURE = 'azure';

    public function _beforeSave(): void
    {
        parent::_beforeSave();

        if ($this->getAdData()) {
            $this->setAdData(json_encode($this->getAdData()));
        }
    }

    public function getByUserId(int $userId)
    {
        return (new self())
            ->getCollection()
            ->addFilter(
                'user_id',
                $userId
            )
            ->getFirstItem();
    }

    public function create(array $data): self
    {
        $model = new self();
        $model->setData([
            'provider_id' => $data['providerId'],
            'provider_type' => $data['providerType'],
            'user_id' => (int)$data['userId'],
            'ad_data' => $data['adData'],
            'dt' => time(),
        ]);
        $model->save();

        return $model;
    }
}
