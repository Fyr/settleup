<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Accounts_UserEntity as UserEntity;

trait Application_Model_Entity_EntitySyncTrait
{
    public bool $isNeedUpdateRestData = false;

    public function syncEntities(User $user, array $newEntityIds): self
    {
        //get entity id from db
        $oldEntityIds = $user->getAssociatedCarrierIds();

        $insertItems = array_diff($newEntityIds, $oldEntityIds);
        $deleteItems = array_diff($oldEntityIds, $newEntityIds);

        //insert new items
        foreach ($insertItems as $item) {
            $userEntity = new UserEntity();
            $userEntity->setUserId($user->getId());
            $userEntity->setEntityId($item);
            $userEntity->save();
            $this->isNeedUpdateRestData = true;
        }

        //delete old items
        foreach ($deleteItems as $item) {
            $userEntity = UserEntity::staticLoad([
                'user_id' => $user->getId(),
                'entity_id' => $item,
            ]);
            $userEntity->delete();
            $this->isNeedUpdateRestData = true;
        }

        //update current user
        if (!in_array($user->getEntityId(), $newEntityIds)) {
            $firstEntityId = array_pop($newEntityIds);
            if ($firstEntityId) {
                $user->setEntityId($firstEntityId);
                $user->save();
            }
        }

        return $this;
    }
}
