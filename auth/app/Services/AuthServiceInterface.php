<?php

namespace App\Services;

interface AuthServiceInterface
{
    public function getAccessToken(int $id, string $password, int $carrierId = null);
}
