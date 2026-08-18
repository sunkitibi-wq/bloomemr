<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class NotificationsDropdown extends Component
{
    public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render(): View
    {
        $notifications = auth()->user()->unreadNotifications()->latest()->take(10)->get();
        $unreadCount = auth()->user()->unreadNotifications()->count();

        return view('livewire.notifications-dropdown', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
