<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Travel extends Model
{
    use Sluggable ;
    protected $table = "travels";
    protected $fillable = [
        'is_public',
        'slug',
        'name',
        'description',
        'number_of_days'
    ];
    public function getNumberOfNightsAttribute(): ?int
    {
        return isset($this->number_of_days)
            ? $this->number_of_days - 1
            : null;
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
