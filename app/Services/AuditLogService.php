<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Log a user or system action.
     *
     * @param string $action The action description (e.g. 'user.login', 'customer.create')
     * @param string|null $entityType The model class name (optional)
     * @param string|null $entityId The UUID or ID of the model (optional)
     * @param array|null $oldValues The state before change (optional)
     * @param array|null $newValues The state after change (optional)
     * @param array|null $metadata Additional metadata (optional)
     * @return AuditLog
     */
    public function log(
        string $action,
        ?string $entityType = null,
        ?string $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
