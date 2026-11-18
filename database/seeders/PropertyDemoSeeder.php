<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\PropertyType;

class PropertyDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeIds = PropertyType::pluck('id');

        Property::factory()
            ->count(5000)
            ->state(fn () => ['property_type_id' => $typeIds->random()])
            ->create();
    }
}
