<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $notifications = $this->notificationService->getAllNotifications($request->user())
            ->paginate(20);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    public function unread(Request $request)
    {
        $notifications = $this->notificationService->getUnreadNotifications($request->user())
            ->paginate(20);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'filter' => 'unread',
        ]);
    }

    public function read(Request $request)
    {
        $notifications = $this->notificationService->getReadNotifications($request->user())
            ->paginate(20);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'filter' => 'read',
        ]);
    }

    public function markAsRead(Request $request, string $notificationId)
    {
        $this->notificationService->markNotificationAsRead($notificationId, $request->user());

        return redirect()->back()
            ->with('success', __('Notification marked as read.'));
    }

    public function markAllAsRead(Request $request)
    {
        $this->notificationService->markAllNotificationsAsRead($request->user());

        return redirect()->back()
            ->with('success', __('All notifications marked as read.'));
    }
} 