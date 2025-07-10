<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UnstructuredSupplementaryServiceData extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'unstructured_supplementary_service_data';

    // Status constants
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    public static function getStatusValueByLabel(string $label)
    {
        $statusOptions = self::getStatusOptions();
        $lowerLabel = strtolower(explodeUppercase($label));

        foreach ($statusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }
        
        return false;
    }

    // USSD Operation Types
    const OP_REQUEST = 1;
    const OP_NOTIFY = 2;
    const OP_RESPONSE = 3;
    const OP_RELEASE = 4;

    public static function getOperationOptions()
    {
        return [
            self::OP_REQUEST => 'Request',
            self::OP_NOTIFY => 'Notify',
            self::OP_RESPONSE => 'Response',
            self::OP_RELEASE => 'Release',
        ];
    }

    public static function getOperationValueByLabel(string $label)
    {
        $operationOptions = self::getOperationOptions();
        $lowerLabel = strtolower(explodeUppercase($label));

        foreach ($operationOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }
        
        return false;
    }

    // Response Types
    const RESPONSE_END = 'end';
    const RESPONSE_CONTINUE = 'continue';
    const RESPONSE_TIMEOUT = 'timeout';

    public static function getResponseTypeOptions()
    {
        return [
            self::RESPONSE_END => 'End',
            self::RESPONSE_CONTINUE => 'Continue',
            self::RESPONSE_TIMEOUT => 'Timeout',
        ];
    }

    public static function getResponseTypeValueByLabel(string $label)
    {
        $responseTypeOptions = self::getResponseTypeOptions();
        $lowerLabel = strtolower(explodeUppercase($label));

        foreach ($responseTypeOptions as $key => $value) {
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
        'communication_id',
        'type_id',
        'category_id',
        'session_id',
        'msisdn',
        'ussd_service_code',
        'ussd_string',
        'user_input',
        'ussd_operation',
        'network',
        'session_state',
        'sequence_number',
        'max_sequence',
        'response_message',
        'expects_response',
        'response_type',
        'network_code',
        'country_code',
        'error_message',
        'error_code',
        'session_data',
        'started_at',
        'last_interaction_at',
        'ended_at',
        '_status',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['communication', 'type', 'category'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expects_response' => 'boolean',
        'sequence_number' => 'integer',
        'max_sequence' => 'integer',
        'ussd_operation' => 'integer',
        'error_code' => 'integer',
        'session_data' => 'array',
        'started_at' => 'datetime',
        'last_interaction_at' => 'datetime',
        'ended_at' => 'datetime',
        '_status' => 'integer',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'session_state' => 'new',
        'sequence_number' => 1,
        'max_sequence' => 1,
        'ussd_operation' => self::OP_REQUEST,
        'expects_response' => true,
        'response_type' => self::RESPONSE_END,
        '_status' => self::STATUS_ACTIVE,
    ];

    /**
     * Get the validation rules for creating a new USSD session.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'max:255', Rule::unique('unstructured_supplementary_service_data', 'uuid')],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'session_id' => 'required|string|max:255|unique:unstructured_supplementary_service_data,session_id',
            'msisdn' => 'required|string|max:20',
            'ussd_service_code' => 'required|string|max:20',
            'ussd_string' => 'nullable|string',
            'user_input' => 'nullable|string',
            'ussd_operation' => 'required|integer|in:1,2,3,4',
            'network' => 'nullable|string|max:50',
            'session_state' => 'required|string|in:new,existing,timeout,ended',
            'sequence_number' => 'required|integer|min:1',
            'max_sequence' => 'required|integer|min:1|gte:sequence_number',
            'response_message' => 'nullable|string',
            'expects_response' => 'required|boolean',
            'response_type' => 'required|string|in:end,continue,timeout',
            'network_code' => 'nullable|string|max:10',
            'country_code' => 'nullable|string|size:2',
            'error_message' => 'nullable|string',
            'error_code' => 'nullable|integer',
            'session_data' => 'nullable|array',
            'started_at' => 'nullable|date',
            'last_interaction_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            '_status' => 'required|integer|in:0,1,2,3',
        ];
    }

    /**
     * Get the validation rules for updating an existing USSD session.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'string', 'max:255', Rule::unique('unstructured_supplementary_service_data', 'uuid')->ignore($id)],
            'communication_id' => 'nullable|exists:communications,id',
            'type_id' => 'nullable|exists:communication_types,id',
            'category_id' => 'nullable|exists:communication_categories,id',
            'session_id' => ['nullable', 'string', 'max:255', Rule::unique('unstructured_supplementary_service_data', 'session_id')->ignore($id)],
            'msisdn' => 'nullable|string|max:20',
            'ussd_service_code' => 'nullable|string|max:20',
            'ussd_string' => 'nullable|string',
            'user_input' => 'nullable|string',
            'ussd_operation' => 'nullable|integer|in:1,2,3,4',
            'network' => 'nullable|string|max:50',
            'session_state' => 'nullable|string|in:new,existing,timeout,ended',
            'sequence_number' => 'nullable|integer|min:1',
            'max_sequence' => 'nullable|integer|min:1',
            'response_message' => 'nullable|string',
            'expects_response' => 'nullable|boolean',
            'response_type' => 'nullable|string|in:end,continue,timeout',
            'network_code' => 'nullable|string|max:10',
            'country_code' => 'nullable|string|size:2',
            'error_message' => 'nullable|string',
            'error_code' => 'nullable|integer',
            'session_data' => 'nullable|array',
            'started_at' => 'nullable|date',
            'last_interaction_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
            '_status' => 'nullable|integer|in:0,1,2,3',
        ];
    }

    /**
     * Get the communication that owns the USSD session.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    /**
     * Get the communication type that owns the USSD session.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CommunicationType::class, 'type_id');
    }

    /**
     * Get the communication category that owns the USSD session.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CommunicationCategory::class, 'category_id');
    }

    /**
     * Scope a query to only include active USSD sessions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('_status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include completed USSD sessions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('_status', self::STATUS_COMPLETED);
    }

    /**
     * Check if the USSD session is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->_status === self::STATUS_ACTIVE;
    }

    /**
     * Mark the USSD session as completed.
     *
     * @return bool
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            '_status' => self::STATUS_COMPLETED,
            'ended_at' => now(),
            'expects_response' => false,
            'response_type' => self::RESPONSE_END,
        ]);
    }

    /**
     * Get the request class for the model.
     *
     * @return string
     */
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\UssdRequest::class;
    }

    /**
     * Get the resource class for the model.
     *
     * @return string
     */
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\UssdResource::class;
    }
}
