<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportBatch extends Model
{
    protected $fillable = [
        'original_filename',
        'file_path',
        'error_rows',
        'status',
        'total_rows',
        'processed_rows',
        'success_rows',
        'started_at',
        'finished_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'started_at'  => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function errors()
    {
        return $this->hasMany(ImportError::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}