<?php

namespace App\Actions;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class LogAudit
{
    public function __invoke(
        User $user,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?int $patientId = null,
        ?array $metadata = null,
        ?Request $request = null,
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $user->id,
            'patient_id' => $patientId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => $request?->ip() ?? request()->ip(),
            'user_agent' => $request?->userAgent() ?? request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }
}
