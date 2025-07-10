<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property int $type_id
 * @property int $category_id
 * @property int|null $user_id
 * @property int|null $approved_by
 * @property string|null $file_name
 * @property string|null $file_path
 * @property string|null $file_url
 * @property string|null $file_hash
 * @property int|null $file_size
 * @property string|null $file_mime_type
 * @property string|null $file_extension
 * @property int $version
 * @property int $_status
 * @property array|null $metadata
 * @property Carbon|null $approved_at
 * @property Carbon|null $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read DocumentType $type
 * @property-read DocumentCategory $category
 * @property-read User|null $user
 * @property-read User|null $approver
 */
class Document extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    public const PENDING = 0;
    public const ACTIVE = 1;
    public const ARCHIVED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'type_id',
        'category_id',
        'user_id',
        'approved_by',
        'file_name',
        'file_path',
        'file_url',
        'file_hash',
        'file_size',
        'file_mime_type',
        'file_extension',
        'version',
        '_status',
        'metadata',
        'approved_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'version' => 'integer',
        '_status' => 'integer',
        'metadata' => 'array',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (self $document) {
            if (empty($document->uuid)) {
                $document->uuid = (string) Str::orderedUuid();
            }
            
            if (empty($document->slug) && !empty($document->name)) {
                $document->slug = Str::slug($document->name);
            }
        });

        static::updating(function (self $document) {
            if ($document->isDirty('name') && empty($document->slug)) {
                $document->slug = Str::slug($document->name);
            }
        });
    }

    /**
     * Get the status options for the document.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::ARCHIVED => 'Archived',
        ];
    }

    /**
     * Get the status label for the document.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->_status] ?? 'Unknown';
    }

    /**
     * Get the status value by label.
     */
    public static function getStatusValueByLabel(string $label): int|bool
    {
        $statusOptions = array_map('strtolower', self::getStatusOptions());
        $lowerLabel = strtolower($label);
        
        return array_search($lowerLabel, $statusOptions, true);
    }

    /**
     * Scope a query to only include pending documents.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('_status', self::PENDING);
    }

    /**
     * Scope a query to only include active documents.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('_status', self::ACTIVE);
    }

    /**
     * Scope a query to only include archived documents.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('_status', self::ARCHIVED);
    }

    /**
     * Scope a query to only include expired documents.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Check if the document is pending.
     */
    public function isPending(): bool
    {
        return $this->_status === self::PENDING;
    }

    /**
     * Check if the document is active.
     */
    public function isActive(): bool
    {
        return $this->_status === self::ACTIVE;
    }

    /**
     * Check if the document is archived.
     */
    public function isArchived(): bool
    {
        return $this->_status === self::ARCHIVED;
    }

    /**
     * Check if the document is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Approve the document.
     */
    public function approve(int $approvedBy): bool
    {
        return $this->update([
            '_status' => self::ACTIVE,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    /**
     * Archive the document.
     */
    public function archive(): bool
    {
        return $this->update(['_status' => self::ARCHIVED]);
    }

    /**
     * Get the document type relationship.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the document category relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    /**
     * Get the user who created the document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the document.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the validation rules for creating a new document.
     */
    public static function createRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:documents'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:documents'],
            'description' => ['nullable', 'string'],
            'type_id' => ['required', 'exists:document_types,id'],
            'category_id' => ['required', 'exists:document_categories,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'metadata' => ['nullable', 'array'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    /**
     * Get the validation rules for updating a document.
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('documents', 'name')->ignore($id)
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('documents', 'slug')->ignore($id)
            ],
            'description' => ['sometimes', 'string', 'nullable'],
            'type_id' => ['sometimes', 'exists:document_types,id'],
            'category_id' => ['sometimes', 'exists:document_categories,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'file' => ['sometimes', 'file', 'max:10240'],
            'metadata' => ['sometimes', 'array', 'nullable'],
            'expires_at' => ['sometimes', 'date', 'nullable', 'after:now'],
            '_status' => ['sometimes', 'integer', Rule::in(array_keys(self::getStatusOptions()))],
        ];
    }

    /**
     * Get the request class for the model.
     */
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\DocumentRequest::class;
    }

    /**
     * Get the resource class for the model.
     */
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\DocumentResource::class;
    }

    /**
     * Handle file upload for the document.
     *
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file, string $disk = 'public'): bool
    {
        $mimeTypes = new MimeTypes();
        $mimeType = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileHash = hash_file('sha256', $file->getRealPath());
        $storagePath = "documents/{$this->id}";
        
        // Store the file content directly
        $fileContent = file_get_contents($file->getRealPath());
        $storedPath = $storagePath . '/' . $fileHash . '.' . $extension;
        
        if (!Storage::disk($disk)->put($storedPath, $fileContent)) {
            throw new \Exception('Failed to upload file');
        }

        // Generate a public URL if using a public disk
        $fileUrl = null;
        if ($disk === 'public') {
            $fileUrl = asset('storage/' . $storedPath);
        }

        $this->update([
            'file_name' => $fileName,
            'file_path' => $storedPath,
            'file_url' => $fileUrl,
            'file_hash' => $fileHash,
            'file_size' => $fileSize,
            'file_mime_type' => $mimeType,
            'file_extension' => $extension,
        ]);

        return true;
    }

    /**
     * Get the public URL of the document file.
     */
    public function getPublicUrl(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        // If we already have a file_url, use that
        if ($this->file_url) {
            return $this->file_url;
        }

        // For public disk, generate the URL using the asset helper
        if (config('filesystems.default') === 'public') {
            return asset('storage/' . $this->file_path);
        }

        // For other storage configurations, return the stored path
        return $this->file_path;
    }

    /**
     * Get the file contents.
     */
    public function getFileContents(): ?string
    {
        if (!$this->file_path || !Storage::exists($this->file_path)) {
            return null;
        }

        return Storage::get($this->file_path);
    }

    /**
     * Create a new version of the document.
     */
    public function createNewVersion(array $attributes = []): self
    {
        return tap($this->replicate()->fill([
            'version' => $this->version + 1,
            'parent_id' => $this->id,
        ] + $attributes))->save();
    }

    /**
     * Get the file icon based on the MIME type.
     */
    public function getFileIcon(): string
    {
        return match ($this->file_mime_type) {
            'application/pdf' => 'file-pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'file-word',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'file-excel',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'file-powerpoint',
            'text/plain' => 'file-alt',
            'image/jpeg', 'image/png', 'image/gif' => 'file-image',
            'application/zip', 'application/x-rar-compressed', 'application/x-tar' => 'file-archive',
            'audio/mpeg', 'audio/wav' => 'file-audio',
            'video/mp4', 'video/quicktime' => 'file-video',
            default => 'file',
        };
    }
}
