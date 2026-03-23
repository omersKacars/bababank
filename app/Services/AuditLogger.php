<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public static function log(
        ?User $actor,
        string $action,
        ?Model $auditable = null,
        array $meta = []
    ): void {
        AuditLog::create([
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'auditable_type' => $auditable ? $auditable::class : null,
            'auditable_id' => $auditable?->getKey(),
            'meta' => $meta ?: null,
        ]);
    }
}
