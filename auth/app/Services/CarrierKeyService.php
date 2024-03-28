<?php

namespace App\Services;

use App\Models\CarrierKey;
use Illuminate\Support\Facades\Log;

class CarrierKeyService implements CarrierKeyServiceInterface
{
    public function __construct(private readonly CryptService $cryptService)
    {
    }

    public function getCarrierKey(int $carrierId, int $userId = null): array
    {
        $carrierKeyModel = $this->find($carrierId);
        Log::debug("checking carrier key model for ID = $carrierId");
        if (!$carrierKeyModel instanceof CarrierKey) {
            Log::info("carrier key model w/ ID = $carrierId doesn't exist, going to create a new");
            $randomKey = $this->cryptService->getRandomKey();
            $carrierKey = $this->cryptService->encode($randomKey);
            Log::info('Key: ' . $randomKey);
            $data = [
                'carrier_id' => $carrierId,
                'key' => $carrierKey,
            ];
            $carrierKeyModel = CarrierKey::query()->create($data);
            Log::info("carrier key model w/ ID = $carrierId was successfully created");
        }

        if ($carrierKeyModel instanceof CarrierKey) {
            return $this->cryptService->createCredentialsFromCarrierKey($carrierKeyModel, $userId);
        }

        return [];
    }

    public function find($id)
    {
        return CarrierKey::query()->find($id);
    }
}
