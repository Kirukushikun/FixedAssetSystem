<?php

namespace App\Livewire;

use App\Models\SystemNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public int    $unreadCount  = 0;
    public array  $notifications = [];
    public bool   $showDropdown = false;

    protected $listeners = ['notificationReceived' => 'refresh'];

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $userId = Auth::id();

        $this->unreadCount = SystemNotification::where('user_id', $userId)
            ->unread()
            ->count();

        $this->notifications = SystemNotification::where('user_id', $userId)
            ->latest()
            ->take(15)
            ->get(['id', 'type', 'title', 'message', 'url', 'read_at', 'created_at'])
            ->toArray();
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = !$this->showDropdown;

        if ($this->showDropdown) {
            $this->refresh();
        }
    }

    public function markRead(int $id): void
    {
        SystemNotification::where('user_id', Auth::id())
            ->where('id', $id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->refresh();
    }

    public function markAllRead(): void
    {
        SystemNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->refresh();
    }

    public function dismiss(int $id): void
    {
        SystemNotification::where('user_id', Auth::id())->where('id', $id)->delete();
        $this->refresh();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
