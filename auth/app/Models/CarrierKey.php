<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $carrier_id
 * @property string $key
 */
class CarrierKey extends Model
{
    protected $primaryKey = 'carrier_id';
    protected $fillable = ['carrier_id', 'key'];
    public $incrementing = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_carrier', 'carrier_id');
    }
}
