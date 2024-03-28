<?php

namespace App\Services;

use App\Exceptions\AccessDeniedException;
use App\Exceptions\InvalidPasswordException;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthService implements AuthServiceInterface
{
    final public const DEFAULT_CARRIER = 1;

    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @throws AccessDeniedException
     * @throws InvalidPasswordException
     * @throws UserNotFoundException
     */
    public function getAccessToken(int $id, string $password, int $carrierId = null): array
    {
        Log::debug("trying to authenticate user ID = $id w/ carrier ID = $carrierId");
        $user = $this->userService->find($id);
        if ($user instanceof User) {
            Log::debug("user ID = $id was found");
            if ($user->verifyPassword($password)) {
                Log::debug("user ID = $id has correct password");
                if ($user->isAdmin() || $user->isGuest()) {
                    if (!$carrierId) {
                        $carrierId = self::DEFAULT_CARRIER;
                    }
                } elseif ($user->isCarrier() || $user->isModerator()) {
                    $carrierId = $user->carrier_id;
                } elseif (!$user->carriers()->count() || !in_array($carrierId, $user->carriers()->allRelatedIds()->toArray())) {
                    Log::error("user ID = $id is not admin and doesn't have carrier ID = $carrierId in the list of carriers associated with this user");

                    throw new AccessDeniedException();
                }
                /** @var CarrierKeyService $carrierKeyService */
                $carrierKeyService = app(CarrierKeyServiceInterface::class);

                return $carrierKeyService->getCarrierKey($carrierId, $id);
            }
            Log::debug("user ID = $id has invalid password");

            throw new InvalidPasswordException();
        }
        Log::debug("user ID = $id was NOT found");

        throw new UserNotFoundException();
    }

    public function getHash(int $id): false|string
    {
        $user = $this->userService->find($id);

        if ($user instanceof User) {
            return md5((string) $user->password);
        }

        return false;
    }

    public function resetPassword(int $id, string $hash, string $password): ?array
    {
        $user = $this->userService->find($id);
        if ($user instanceof User && $this->getHash($id) === $hash) {
            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->save();

            return $user->toArray();
        }

        return null;
    }
}
