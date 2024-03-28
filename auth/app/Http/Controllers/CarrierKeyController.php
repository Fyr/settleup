<?php

namespace App\Http\Controllers;

use App\Services\CarrierKeyService;
use Illuminate\Http\JsonResponse;

class CarrierKeyController extends Controller
{
    public function __construct(private readonly CarrierKeyService $service)
    {
        $this->middleware('auth:api');
    }

    public function show($carrierId): JsonResponse
    {
        $carrierKey = $this->service->getCarrierKey($carrierId);

        return response()->json(['carrier_key' => $carrierKey]);
    }
}
