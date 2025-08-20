<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = [
        'travel_id',
        'name',
        'starting_date',
        'ending_date',
        'price'
    ];
    public function getPriceAttribute($value)
    {
        return $value / 100;
    }
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (int)($value * 100);
    }
}
