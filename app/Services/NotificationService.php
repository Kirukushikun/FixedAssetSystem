<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\User;

class NotificationService
{
    /**
     * Notify all users who have the given permission.
     * Optionally exclude a specific user (e.g. the one who triggered the action).
     */
    public static function notifyByPermission(
        string $permission,
        string $type,
        string $title,
        string $message,
        string $url = '',
        ?int   $excludeUserId = null
    ): void {
        $users = User::where('is_admin', false)
            ->whereHas('roles.permissions', fn($q) => $q->where('key', $permission))
            ->when($excludeUserId, fn($q) => $q->where('id', '!=', $excludeUserId))
            ->pluck('id');

        // Also notify admins
        $adminIds = User::where('is_admin', true)
            ->when($excludeUserId, fn($q) => $q->where('id', '!=', $excludeUserId))
            ->pluck('id');

        $allIds = $users->merge($adminIds)->unique();

        $now = now();
        $rows = $allIds->map(fn($id) => [
            'user_id'    => $id,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'url'        => $url,
            'read_at'    => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->values()->toArray();

        if (!empty($rows)) {
            SystemNotification::insert($rows);
        }
    }
}
