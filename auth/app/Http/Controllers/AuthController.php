<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @throws Exception
     */
    public function login($id, $password, $carrierId = null): JsonResponse
    {
        try {
            $credentials = $this->service->getAccessToken($id, $password, $carrierId);
        } catch (HttpException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }

        return response()->json(['credentials' => $credentials]);
    }

    public function hash($id): JsonResponse
    {
        $hash = $this->service->getHash($id);

        return response()->json(['hash' => $hash]);
    }

    public function reset($id, $hash, $password): JsonResponse
    {
        $user = $this->service->resetPassword($id, $hash, $password);

        return response()->json(['user' => $user]);
    }
}
