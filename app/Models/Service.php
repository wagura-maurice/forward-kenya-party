<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'configuration' => 'array',
        'is_featured' => 'boolean',
        'requires_payment' => 'boolean',
        '_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'type_id',
        'category_id',
        'name',
        'slug',
        'description',
        'notes',
        'logo_path',
        'banner_path',
        'is_featured',
        'requires_payment',
        'configuration',
        '_status',
    ];

    /**
     * Get status labels with their human-readable names
     * 
     * @return array
     */
    public static function statusLabels(): array
    {
        return [
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        ];
    }

    /**
     * Get status value by its label
     * 
     * @param string $label
     * @return int|false
     */
    public static function getStatusValueByLabel(string $label)
    {
        return self::getValueByLabel(self::statusLabels(), $label);
    }
    
    /**
     * Helper method to get value by label from an array of options
     * 
     * @param array $options
     * @param string $label
     * @return mixed
     */
    protected static function getValueByLabel(array $options, string $label)
    {
        $lowerLabel = strtolower($label);
        
        foreach ($options as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }
        
        return false;
    }

    /**
     * Get the request class for the model.
     *
     * @return string
     */
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\ServiceRequest::class;
    }

    /**
     * Get the resource class for the model.
     *
     * @return string
     */
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\ServiceResource::class;
    }

    /**
     * Get the validation rules for creating a new service.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('services', 'uuid')],
            'type_id' => 'required|integer|exists:service_types,id',
            'category_id' => 'required|integer|exists:service_categories,id',
            'name' => 'required|string|max:255|unique:services,name',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'logo_path' => 'nullable|string|max:255',
            'banner_path' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'requires_payment' => 'boolean',
            'configuration' => 'nullable|array',
            '_status' => 'required|integer|in:' . implode(',', array_keys(self::statusLabels())),
        ];
    }

    /**
     * Get the validation rules for updating an existing service.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('services', 'uuid')->ignore($id)],
            'type_id' => 'nullable|integer|exists:service_types,id',
            'category_id' => 'nullable|integer|exists:service_categories,id',
            'name' => 'nullable|string|max:255|unique:services,name,' . $id,
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $id,
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'logo_path' => 'nullable|string|max:255',
            'banner_path' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'requires_payment' => 'boolean',
            'configuration' => 'nullable|array',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::statusLabels())),
        ];
    }

    /**
     * Get the service type that owns the service.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'type_id');
    }

    /**
     * Get the service category that owns the service.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * Get all departments that offer this service.
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_service')
            ->withTimestamps();
            // ->withPivot(['is_primary', 'notes']);
    }

    /**
     * Get the status label attribute.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->_status] ?? 'Unknown';
    }

    /**
     * Scope a query to only include active services.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('_status', self::ACTIVE);
    }

    /**
     * Scope a query to only include featured services.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include paid services.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid($query)
    {
        return $query->where('requires_payment', true);
    }
}
