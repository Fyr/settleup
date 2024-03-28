<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
        $this->middleware('auth:api', ['except' => ['storeSso']]);
    }

    public function show($id): JsonResponse
    {
        $user = $this->service->find($id);

        return response()->json(['user' => $user->toArray()]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->service->create($request->all());

        return response()->json(['user' => $user->toArray()]);
    }

    public function storeSso(Request $request): JsonResponse
    {
        $user = $this->service->create($request->all());

        return response()->json(['user' => $user->toArray()]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        Log::debug(sprintf("going to update id = $id"));
        Log::debug($request);

        $user = $this->service->update($id, $request->all());

        return response()->json(['user' => $user->toArray()]);
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->service->delete($id);

        return response()->json(['deleted' => $result]);
    }
}
