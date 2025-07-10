<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LightOfGuidance extends Model
{
    use HasFactory, SoftDeletes;

    // status
    const PENDING = 0;
    const PROCESSING = 1;
    const PROCESSED = 2;
    const OPEN = 3;
    const CLOSED = 4;

    // Status labels
    public static function getStatusOptions()
    {
        return [
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
        ];
    }

    // Get status value by label
    public static function getStatusValueByLabel($label)
    {
        $statusOptions = self::getStatusOptions();

        // Perform case-insensitive search
        $lowerLabel = strtolower(explodeUppercase($label));

        foreach ($statusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }
        
        return false;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'user_id',
        '_class',
        'message',
        'trace',
        'exception_type',
        'exception_code',
        'request_info',
        'job_class',
        'job_id',
        'queue_name',
        'queue_connection',
        'model_class',
        'model_id',
        'payload',
        'environment',
        '_status'
    ];

    public static function createRules(): array
    {
        return [
            'uuid' => ['required', 'string', Rule::unique('light_of_guidances', 'uuid')],
            'user_id' => 'nullable|exists:users,id',
            '_class' => 'required|string',
            'message' => 'required|string',
            'trace' => 'nullable|string',
            'exception_type' => 'nullable|string',
            'exception_code' => 'nullable|integer',
            'request_info' => 'nullable|json',
            'job_class' => 'nullable|string',
            'job_id' => 'nullable|integer',
            'queue_name' => 'nullable|string',
            'queue_connection' => 'nullable|string',
            'model_class' => 'nullable|string',
            'model_id' => 'nullable|integer',
            'payload' => 'nullable|text',
            'environment' => 'nullable|string',
            '_status' => 'nullable|integer'
        ];
    }
    
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('light_of_guidances', 'uuid')->ignore($id)],
            'user_id' => 'nullable|exists:users,id',
            '_class' => 'nullable|string',
            'message' => 'nullable|string',
            'trace' => 'nullable|string',
            'exception_type' => 'nullable|string',
            'exception_code' => 'nullable|integer',
            'request_info' => 'nullable|json',
            'job_class' => 'nullable|string',
            'job_id' => 'nullable|integer',
            'queue_name' => 'nullable|string',
            'queue_connection' => 'nullable|string',
            'model_class' => 'nullable|string',
            'model_id' => 'nullable|integer',
            'payload' => 'nullable|text',
            'environment' => 'nullable|string',
            '_status' => 'nullable|integer'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
