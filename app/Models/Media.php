<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Media extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'configuration' => 'array',
        'metadata' => 'array',
    ];

    // Status constants
    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;

    public static function statusLabels()
    {
        return [
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        ];
    }

    public static function getStatusValueByLabel(string $label)
    {
        $statusOptions = self::getStatusOptions();
        $lowerLabel = strtolower($label);

        foreach ($statusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    /**
     * The name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'uuid',
        'name',
        'file_name',
        'mime_type',
        'size',
        'path',
        'disk',
        'conversions',
        'custom_properties',
        'responsive_images',
        'order_column',
        'model_type',
        'model_id',
        'collection_name',
        'media_type_id',
        'media_category_id',
        'is_featured',
        'alt_text',
        'caption',
        'description',
        'metadata',
        'configuration',
        'session_id',
        'session_amount',
        'network_code',
        'failure_reason',
        'processed_at',
        '_status',
    ];
    
    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Generate UUID when creating a new model
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    
    /**
     * Get the media type that owns the media.
     */
    public function mediaType(): BelongsTo
    {
        return $this->belongsTo(MediaType::class, 'media_type_id');
    }
    
    /**
     * Get the media category that owns the media.
     */
    public function mediaCategory(): BelongsTo
    {
        return $this->belongsTo(MediaCategory::class, 'media_category_id');
    }

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\MediaRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\MediaResource::class;
    }

    public static function createRules(): array
    {
        return [
            'uuid' => ['required', 'uuid', 'unique:media,uuid'],
            'name' => ['required', 'string', 'max:255', 'unique:media,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:media,slug'],
            'description' => ['nullable', 'string'],
            'type_id' => ['required', 'exists:document_types,id'],
            'category_id' => ['required', 'exists:document_categories,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'endpoint_url' => ['nullable', 'string', 'url', 'regex:/^https:\/\/.*\.(jpg|jpeg|png|gif|pdf|docx|txt)$/i'],
            'file_type' => ['nullable', 'string', 'max:10'],
            'file_extension' => ['nullable', 'string', 'max:10'],
            'file_path' => ['nullable', 'string', 'url', 'regex:/^https:\/\/.*\.(jpg|jpeg|png|gif|pdf|docx|txt)$/i'],
            'file_size' => ['nullable', 'integer', 'min:0'],
            'file_format' => ['nullable', 'string', 'max:10'],
            'metadata' => ['nullable', 'json'],
            'approved_at' => ['nullable', 'date'],
            '_status' => ['nullable', 'integer', Rule::in(array_keys(self::getStatusOptions()))],
        ];
    }

    public static function updateRules($id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('media', 'uuid')->ignore($id)],
            'name' => ['nullable', 'string', 'max:255', Rule::unique('media', 'name')->ignore($id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('media', 'slug')->ignore($id)],
            'description' => ['nullable', 'string'],
            'type_id' => ['nullable', 'exists:document_types,id'],
            'category_id' => ['nullable', 'exists:document_categories,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'endpoint_url' => ['nullable', 'string', 'url', 'regex:/^https:\/\/.*\.(jpg|jpeg|png|gif|pdf|docx|txt)$/i'],
            'file_type' => ['nullable', 'string', 'max:10'],
            'file_extension' => ['nullable', 'string', 'max:10'],
            'file_path' => ['nullable', 'string', 'url', 'regex:/^https:\/\/.*\.(jpg|jpeg|png|gif|pdf|docx|txt)$/i'],
            'file_size' => ['nullable', 'integer', 'min:0'],
            'file_format' => ['nullable', 'string', 'max:10'],
            'metadata' => ['nullable', 'json'],
            'approved_at' => ['nullable', 'date'],
            '_status' => ['nullable', 'integer', Rule::in(array_keys(self::getStatusOptions()))],
        ];
    }

    public function type()
    {
        return $this->belongsTo(MediaType::class);
    }

    public function category()
    {
        return $this->belongsTo(MediaCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
