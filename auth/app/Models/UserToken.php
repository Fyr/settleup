<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $secret
 */
class UserToken extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'token', 'secret'];

    public function getAuthPassword()
    {
        return $this->secret;
    }
}
