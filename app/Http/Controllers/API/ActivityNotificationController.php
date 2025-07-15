<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ActivityNotificationRequest;
use App\Http\Resources\API\ActivityNotificationResource;
use App\Models\ActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ActivityNotificationController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ActivityNotification::class);
        
        $notifications = ActivityNotification::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate();
            
        return ActivityNotificationResource::collection($notifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ActivityNotificationRequest $request): ActivityNotificationResource
    {
        $this->authorize('create', ActivityNotification::class);
        
        $notification = ActivityNotification::create($request->validated());
        
        return new ActivityNotificationResource($notification->load(['activity', 'user']));
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityNotification $activityNotification): ActivityNotificationResource
    {
        $this->authorize('view', $activityNotification);
        
        // Mark as read when viewed
        if (is_null($activityNotification->read_at)) {
            $activityNotification->markAsRead();
        }
        
        return new ActivityNotificationResource($activityNotification->load(['activity', 'user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActivityNotificationRequest $request, ActivityNotification $activityNotification): ActivityNotificationResource
    {
        $this->authorize('update', $activityNotification);
        
        $activityNotification->update($request->validated());
        
        return new ActivityNotificationResource($activityNotification->load(['activity', 'user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityNotification $activityNotification): Response
    {
        $this->authorize('delete', $activityNotification);
        
        $activityNotification->delete();
        
        return response()->noContent();
    }
    
    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $this->authorize('update', ActivityNotification::class);
        
        $count = Auth::user()
            ->activityNotifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json(['message' => "$count notifications marked as read"]);
    }
    
    /**
     * Get unread notifications count.
     */
    public function unreadCount(): JsonResponse
    {
        $count = Auth::user()
            ->activityNotifications()
            ->whereNull('read_at')
            ->count();
            
        return response()->json(['count' => $count]);
    }
}
