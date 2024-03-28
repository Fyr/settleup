<?php

namespace App\Services;

use App\Exceptions\UserNotFoundException;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function create($data)
    {
        $data['password'] = password_hash((string) $data['password'], PASSWORD_BCRYPT);
        $carriers = $data['carriers'] ?? [];
        if (isset($data['carriers'])) {
            unset($data['carriers']);
        }
        $user = User::query()->create($data);
        if (!$user instanceof User) {
            return null;
        }
        if ($user->isContractor() || $user->isVendor()) {
            $user->carriers()->sync($carriers);
        }

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function update($id, $data)
    {
        $user = $this->find($id);
        if (!$user instanceof User) {
            throw new UserNotFoundException();
        }

        if (isset($data['password'])) {
            $data['password'] = password_hash((string) $data['password'], PASSWORD_BCRYPT);
        }
        $carriers = $data['carriers'] ?? [];
        if (isset($data['carriers'])) {
            unset($data['carriers']);
        }
        unset($data['id']);

        $user->update($data);

        $user = $this->find($id);
        if (!$user instanceof User) {
            return null;
        }
        if ($user->isContractor() || $user->isVendor()) {
            $user->carriers()->sync($carriers);
        }

        return $user;
    }

    public function find($id)
    {
        // TODO: make sure that if $id is not int, it works fine
        // In some cases it returns 1st user whatever $id is
        // TODO: refactor it to ::firstOrFail()
        return User::query()->find($id);
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}
