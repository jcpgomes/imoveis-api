<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case Available = 'available';
    case Sold      = 'sold';
    case Rented    = 'rented';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponível',
            self::Sold      => 'Vendido',
            self::Rented    => 'Alugado',
        };
    }
}