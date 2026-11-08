<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PropertyType;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        $types = [
            'apartment'  => 'Apartamento',
            'house'      => 'Casa',
            'land'       => 'Terreno',
            'commercial' => 'Comercial',
        ];

        foreach ($types as $slug => $name) {
            PropertyType::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }
    }
}
