<?php

namespace App\Enums;

enum PropertySource: string
{
    case Manual = 'manual';
    case Import = 'import';
    case Api    = 'api';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manual',
            self::Import => 'Importação',
            self::Api    => 'Api',
        };
    }
}