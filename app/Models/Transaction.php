<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'child_user_id',
        'parent_user_id',
        'type',
        'amount',
        'note',
        'voided_at',
        'void_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'voided_at' => 'datetime',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(User::class, 'child_user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function isVoided(): bool
    {
        return $this->voided_at !== null;
    }
}
