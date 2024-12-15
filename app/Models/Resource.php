<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    protected $fillable = [
        'name',
        'description',
        'capacity'
    ];


    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
