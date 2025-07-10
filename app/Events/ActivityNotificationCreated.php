<?php

namespace App\Events;

use App\Models\ActivityNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityNotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The activity notification instance.
     *
     * @var \App\Models\ActivityNotification
     */
    public $notification;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\ActivityNotification  $notification
     * @return void
     */
    public function __construct(ActivityNotification $notification)
    {
        $this->notification = $notification->load('user');
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'activity.notification.created';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'uuid' => $this->notification->uuid,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toDateTimeString(),
            'user' => [
                'id' => $this->notification->user->id,
                'name' => $this->notification->user->name,
                'email' => $this->notification->user->email,
            ],
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]>
     */
    public function broadcastOn()
    {
        // Broadcast to a private channel for the specific user
        return [
            new PrivateChannel('user.' . $this->notification->user_id),
        ];
    }
}
