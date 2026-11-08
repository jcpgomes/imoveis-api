<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportError extends Model
{
    protected $fillable = [
        'line_number',
        'message',
        'import_batch_id',
        'raw_data',
    ];

    protected function casts(): array
    {
        return ['raw_data' => 'array'];
    }

    public function importBatch()
    {
        return $this->belongsTo(ImportBatch::class);
    }
}