<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Enums\PropertySource;

class PropertyController extends Controller
{
    public function store(StorePropertyRequest $request): PropertyResource
    {
        $property = Property::create([
            ...$request->validated(),
            'source'     => PropertySource::Manual,
            'created_by' => $request->user()->id,
        ]);

        return new PropertyResource($property->load('propertyType'));
    }
}
