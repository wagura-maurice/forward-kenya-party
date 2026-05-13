<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'phone_number',
        'current_step',
        'conversation_data',
        'last_activity_at',
        'is_active',
    ];

    protected $casts = [
        'conversation_data' => 'array',
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the phone number attribute
     */
    public function getPhoneNumberAttribute($value)
    {
        return $value;
    }

    /**
     * Get the chat ID attribute
     */
    public function getChatIdAttribute($value)
    {
        return $value;
    }

    /**
     * Get the current step attribute
     */
    public function getCurrentStepAttribute($value)
    {
        return $value;
    }

    /**
     * Get conversation data as array
     */
    public function getConversationDataAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Set conversation data as JSON
     */
    public function setConversationDataAttribute($value)
    {
        $this->attributes['conversation_data'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Scope to get active conversations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get conversations by step
     */
    public function scopeByStep($query, $step)
    {
        return $query->where('current_step', $step);
    }

    /**
     * Scope to get conversations with recent activity
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('last_activity_at', '>=', now()->subHours($hours));
    }
}
