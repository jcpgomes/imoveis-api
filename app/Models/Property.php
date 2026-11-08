<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PropertyStatus;
use App\Enums\PropertySource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;
    protected $attributes = [
        'status' => 'available',
        'source' => 'manual',
    ];

    protected $fillable = [
        'title',
        'price',
        'address',
        'property_type_id',
        'bedrooms',
        'bathrooms',
        'garage_spaces',
        'area_m2',
        'city',
        'state',
        'neighborhood',
        'status',
        'source',
        'external_code',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2', 
            'area_m2' => 'decimal:2',
            'status' => PropertyStatus::class,
            'source' => PropertySource::class,
        ];
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
