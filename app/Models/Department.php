<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User; // For Administrator and Manager models

class Department extends Model
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
        'founded_date' => 'date',
        'is_non_government_operated' => 'boolean',
        'is_government_operated' => 'boolean',
        'number_of_employees' => 'integer',
        'number_of_branches' => 'integer',
        'total_assets' => 'decimal:2',
        'documents' => 'array',
        'social_media_links' => 'array',
        'configuration' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        '_status' => 'integer',
        'last_verified_at' => 'datetime',
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
        'registration_number',
        'tax_identification_number',
        'iso_code',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'phone_number',
        'toll_free_number',
        'fax_number',
        'email',
        'website',
        'logo',
        'banner',
        'customer_service_email',
        'customer_service_phone',
        'founded_date',
        'is_non_government_operated',
        'is_government_operated',
        'parent_department',
        'ceo_name',
        'number_of_employees',
        'number_of_branches',
        'total_assets',
        'documents',
        'description',
        'notes',
        'services_offered',
        'operating_hours',
        'social_media_links',
        'contact_person_name',
        'contact_person_telephone',
        'contact_person_email',
        'configuration',
        'is_active',
        'is_featured',
        '_status',
        'metadata',
        'last_verified_at',
        'verified_by',
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
        return \App\Http\Requests\API\DepartmentRequest::class;
    }

    /**
     * Get the resource class for the model.
     *
     * @return string
     */
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\DepartmentResource::class;
    }

    /**
     * Get the validation rules for creating a new department.
     *
     * @return array
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('departments', 'uuid')],
            'type_id' => 'required|integer|exists:department_types,id',
            'category_id' => 'required|integer|exists:department_categories,id',
            'name' => 'required|string|max:255|unique:departments,name',
            'slug' => 'nullable|string|max:255|unique:departments,slug',
            'registration_number' => 'nullable|string|max:100|unique:departments,registration_number',
            'tax_identification_number' => 'nullable|string|max:100|unique:departments,tax_identification_number',
            'iso_code' => 'nullable|string|max:10|unique:departments,iso_code',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone_number' => 'nullable|string|max:30',
            'toll_free_number' => 'nullable|string|max:30',
            'fax_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100|unique:departments,email',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|string|max:255',
            'banner' => 'nullable|string|max:255',
            'customer_service_email' => 'nullable|email|max:100',
            'customer_service_phone' => 'nullable|string|max:30',
            'founded_date' => 'nullable|date',
            'is_non_government_operated' => 'boolean',
            'is_government_operated' => 'boolean',
            'parent_department' => 'nullable|string|max:255',
            'ceo_name' => 'nullable|string|max:255',
            'number_of_employees' => 'nullable|integer|min:0',
            'number_of_branches' => 'nullable|integer|min:0',
            'total_assets' => 'nullable|numeric|min:0',
            'documents' => 'nullable|array',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'services_offered' => 'nullable|string',
            'operating_hours' => 'nullable|string',
            'social_media_links' => 'nullable|array',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_telephone' => 'nullable|string|max:30',
            'contact_person_email' => 'nullable|email|max:100',
            'configuration' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            '_status' => 'required|integer|in:' . implode(',', array_keys(self::statusLabels())),
            'metadata' => 'nullable|array',
            'last_verified_at' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get the validation rules for updating an existing department.
     *
     * @param int $id
     * @return array
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('departments', 'uuid')->ignore($id)],
            'type_id' => 'nullable|integer|exists:department_types,id',
            'category_id' => 'nullable|integer|exists:department_categories,id',
            'name' => 'nullable|string|max:255|unique:departments,name,' . $id,
            'slug' => 'nullable|string|max:255|unique:departments,slug,' . $id,
            'registration_number' => 'nullable|string|max:100|unique:departments,registration_number,' . $id,
            'tax_identification_number' => 'nullable|string|max:100|unique:departments,tax_identification_number,' . $id,
            'iso_code' => 'nullable|string|max:10|unique:departments,iso_code,' . $id,
            'email' => 'nullable|email|max:100|unique:departments,email,' . $id,
            'customer_service_email' => 'nullable|email|max:100|unique:departments,customer_service_email,' . $id,
            'contact_person_email' => 'nullable|email|max:100|unique:departments,contact_person_email,' . $id,
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::statusLabels())),
            'verified_by' => 'nullable|exists:users,id',
            // Other fields are nullable and don't need uniqueness constraints on update
        ] + collect((new self())->fillable)
            ->filter(fn($field) => !in_array($field, [
                'uuid', 'type_id', 'category_id', 'name', 'slug', 'registration_number',
                'tax_identification_number', 'iso_code', 'email', 'customer_service_email',
                'contact_person_email', '_status', 'verified_by'
            ]))
            ->mapWithKeys(fn($field) => [$field => 'nullable'])
            ->toArray();
    }

    /**
     * Get the department type that owns the department.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(DepartmentType::class, 'type_id');
    }

    /**
     * Get the department category that owns the department.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DepartmentCategory::class, 'category_id');
    }

    /**
     * Get the user who last verified the department.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Attach a service to the department.
     *
     * @param mixed $service
     * @return array
     */
    public function attachService($service): array
    {
        if (is_string($service)) {
            $service = Service::where('uuid', $service)->firstOrFail();
        }

        return $this->services()->syncWithoutDetaching([$service->id]);
    }

    /**
     * Detach a service from the department.
     *
     * @param mixed $service
     * @return int
     */
    public function detachService($service): int
    {
        if (is_string($service)) {
            $service = Service::where('uuid', $service)->firstOrFail();
        }

        return $this->services()->detach([$service->id]);
    }

    /**
     * Get all services for the department.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'department_service')
            ->withTimestamps();
            // ->withPivot(['is_primary', 'notes']);
    }

    /**
     * Get all administrators for the department.
     */
    public function administrators(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'administrator_department',
            'department_id',
            'administrator_id'
        )->withTimestamps();
    }

    /**
     * Get all managers for the department.
     */
    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'department_manager',
            'department_id',
            'manager_id'
        )->withTimestamps();
    }

    /**
     * Assign an administrator to the department.
     *
     * @param int|string|User $administrator
     * @return array
     */
    public function assignAdministrator($administrator): array
    {
        $userId = $administrator instanceof User ? $administrator->id : $administrator;
        
        if (is_string($userId)) {
            $user = User::where('uuid', $userId)->firstOrFail();
            $userId = $user->id;
        }

        return $this->administrators()->syncWithoutDetaching([$userId]);
    }

    /**
     * Remove an administrator from the department.
     *
     * @param int|string|User $administrator
     * @return int
     */
    public function removeAdministrator($administrator): int
    {
        $userId = $administrator instanceof User ? $administrator->id : $administrator;
        
        if (is_string($userId)) {
            $user = User::where('uuid', $userId)->firstOrFail();
            $userId = $user->id;
        }

        return $this->administrators()->detach([$userId]);
    }

    /**
     * Assign a manager to the department.
     *
     * @param int|string|User $manager
     * @return array
     */
    public function assignManager($manager): array
    {
        $userId = $manager instanceof User ? $manager->id : $manager;
        
        if (is_string($userId)) {
            $user = User::where('uuid', $userId)->firstOrFail();
            $userId = $user->id;
        }

        return $this->managers()->syncWithoutDetaching([$userId]);
    }

    /**
     * Remove a manager from the department.
     *
     * @param int|string|User $manager
     * @return int
     */
    public function removeManager($manager): int
    {
        $userId = $manager instanceof User ? $manager->id : $manager;
        
        if (is_string($userId)) {
            $user = User::where('uuid', $userId)->firstOrFail();
            $userId = $user->id;
        }

        return $this->managers()->detach([$userId]);
    }

    /**
     * Check if the department has a specific administrator.
     *
     * @param int|string|User $administrator
     * @return bool
     */
    public function hasAdministrator($administrator): bool
    {
        $userId = $administrator instanceof User ? $administrator->id : $administrator;
        
        if (is_string($userId)) {
            $user = User::where('uuid', $userId)->firstOrFail();
            $userId = $user->id;
        }

        return $this->administrators()->where('administrator_id', $userId)->exists();
    }

    /**
     * Check if the department has a specific manager.
     *
     * @param int|string|User $manager
     * @return bool
     */
    public function hasManager($manager): bool
    {
        $userId = $manager instanceof User ? $manager->id : $manager;
        
        if (is_string($userId)) {
            $user = User::where('uuid', $userId)->firstOrFail();
            $userId = $user->id;
        }

        return $this->managers()->where('manager_id', $userId)->exists();
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
     * Scope a query to only include active departments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured departments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include pending departments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('_status', self::PENDING);
    }

    /**
     * Scope a query to only include active status departments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveStatus($query)
    {
        return $query->where('_status', self::ACTIVE);
    }

    /**
     * Scope a query to only include inactive departments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('_status', self::INACTIVE);
    }
}
