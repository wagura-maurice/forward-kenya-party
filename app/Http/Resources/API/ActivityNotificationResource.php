<?php

namespace App\Http\Resources\API;

use App\Models\ActivityNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'message' => $this->message,
            'data' => $this->data,
            '_status' => $this->getStatusLabel(),
            'is_read' => !is_null($this->read_at),
            'read_at' => $this->read_at?->toIso8601String(),
            'sent_at' => $this->sent_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'created_at_human' => $this->created_at->diffForHumans(),
            'activity' => $this->whenLoaded('activity'),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
        ];
    }
    
    /**
     * Get the status label for the notification.
     *
     * @return string
     */
    protected function getStatusLabel(): string
    {
        return match($this->_status) {
            ActivityNotification::PENDING => 'pending',
            ActivityNotification::SENT => 'sent',
            ActivityNotification::FAILED => 'failed',
            ActivityNotification::READ => 'read',
            default => 'unknown',
        };
    }
}
