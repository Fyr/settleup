<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $role_id
 * @property int $carrier_id
 * @property string $password
 */
class User extends Model
{
    final public const ROLE_ADMIN = 1;
    final public const ROLE_CARRIER = 2;
    final public const ROLE_CONTRACTOR = 3;
    final public const ROLE_VENDOR = 4;
    final public const ROLE_MODERATOR = 5;
    final public const ROLE_GUEST = 6;

    protected $guarded = [];

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function isAdmin($checkSuperAdmin = false): bool
    {
        if ($checkSuperAdmin) {
            return $this->isSuperAdmin();
        }

        return $this->isSuperAdmin() || $this->isModerator();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_id == self::ROLE_ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->role_id == self::ROLE_MODERATOR;
    }

    public function isVendor(): bool
    {
        return $this->role_id == self::ROLE_VENDOR;
    }

    public function isContractor(): bool
    {
        return $this->role_id == self::ROLE_CONTRACTOR;
    }

    public function isCarrier(): bool
    {
        return $this->role_id == self::ROLE_CARRIER;
    }

    public function isGuest(): bool
    {
        return $this->role_id == self::ROLE_GUEST;
    }

    public function carriers()
    {
        return $this->belongsToMany(CarrierKey::class, 'user_carrier', 'user_id', 'carrier_id');
    }
}
