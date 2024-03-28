<?php

namespace App\Services;

use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthTokenGuard extends TokenGuard
{
    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest(): string
    {
        return $this->request->getUser() ?? '';
    }

    public function getUser(): ?Authenticatable
    {
        return $this->user();
    }
}
