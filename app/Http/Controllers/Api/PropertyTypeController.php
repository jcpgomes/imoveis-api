<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use App\Http\Resources\PropertyTypeResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PropertyTypeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $propertyTypes = PropertyType::orderBy('name')->get();
        return PropertyTypeResource::collection($propertyTypes);
    }

}
