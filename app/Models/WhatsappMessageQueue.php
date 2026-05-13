<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessageQueue extends Model
{
    protected $table = 'whatsapp_message_queue';
    
    protected $fillable = [
        'chat_id',
        'phone_number',
        'message',
        'message_type',
        'status',
        'retry_count',
        'scheduled_at',
        'sent_at',
        'failed_at',
        'error_message',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    public function markAsSent()
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failed_at' => now(),
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    public function canRetry()
    {
        return $this->retry_count < 3 && $this->status === self::STATUS_FAILED;
    }

    public function scheduleForRetry($minutes = 5)
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'scheduled_at' => now()->addMinutes($minutes),
        ]);
    }
}
