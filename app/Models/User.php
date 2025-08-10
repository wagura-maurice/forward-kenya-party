<?php

namespace App\Models;

use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivityWithMetadata;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use LogsActivityWithMetadata;

    // Status constants
    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'phone_country',
        'status',
        'last_login_at',
        'last_login_ip',
        'email_verified_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'profile_photo_path',
        'timezone',
        'language',
        'metadata',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the default log options for the model.
     */
    protected function getDefaultLogOptions(): \Spatie\Activitylog\LogOptions
    {
        return parent::getDefaultLogOptions()
            ->useLogName('users')
            ->dontLogIfAttributesChangedOnly([
                'last_login_at',
                'last_login_ip',
                'remember_token',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'profile_photo_path',
                'updated_at',
                'created_at',
                'deleted_at'
            ]);
    }

    /**
     * Get all activities where this user is the subject.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Get all activities caused by this user.
     */
    public function causedActivities()
    {
        return $this->morphMany(Activity::class, 'causer');
    }

    /**
     * Get activities where this user was affected.
     */
    public function affectedActivities()
    {
        return Activity::where('properties->affected_user_id', $this->id)
            ->orWhere('user_id', $this->id);
    }
    
    /**
     * Log a custom activity for this user.
     */
    public function logActivity(string $action, string $description, array $properties = [], ?string $logName = null)
    {
        return activity($logName)
            ->causedBy(auth()->user() ?? $this)
            ->performedOn($this)
            ->withProperties($properties)
            ->log($description);
    }
    
    /**
     * Update the user's profile photo from a file upload.
     */
    public function updateProfilePhoto($photo)
    {
        // Get media type and category
        $mediaType = MediaType::where('slug', 'image')->firstOrFail();
        $mediaCategory = MediaCategory::where('slug', 'profile_photos')->firstOrFail();
        
        // Store the file using the public disk
        $filePath = $photo->store(
            'uploads/' . $mediaType->slug . '/' . $mediaCategory->slug . '/' . Str::slug(Carbon::parse(REQUEST_TIMESTAMP)->toDateTimeString()), 
            'public' // Explicitly use the public disk
        );
        
        // Create a new media record for the profile photo
        $media = Media::create([
            'uuid' => Str::uuid()->toString(),
            'name' => ucwords(strtolower("{$this->name} profile photo at " . Carbon::parse(REQUEST_TIMESTAMP)->format('F j, Y, g:i a'))),
            'slug' => Str::slug("{$this->name} profile photo at " . Carbon::parse(REQUEST_TIMESTAMP)->format('YmdHis')),
            'description' => 'Profile photo for user: ' . $this->name,
            'file_path' => $filePath,
            'file_name' => $photo->getClientOriginalName(),
            'file_extension' => $photo->getClientOriginalExtension(),
            'file_size' => $photo->getSize(),
            'file_type' => $photo->getMimeType(),
            'media_type_id' => $mediaType->id,
            'media_category_id' => $mediaCategory->id,
            'user_id' => $this->id,
            'is_public' => true,
        ]);

        // Update the user's profile photo path
        $this->forceFill([
            'profile_photo_path' => $media->file_path,
        ])->save();

        return $media;
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoPathAttribute($value)
    {
        if (empty($value)) {
            return $this->defaultProfilePhotoUrl();
        }

        // Check if the path is a full URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Check if the file exists in storage
        if (Storage::disk('public')->exists($value)) {
            return Storage::disk('public')->url($value);
        }

        // Fallback to default if file doesn't exist
        return $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the validation rules for creating a new user.
     */
    public static function createRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_country' => ['nullable', 'string', 'max:2'],
            'status' => ['nullable', 'integer', 'in:0,1,2'],
            'timezone' => ['nullable', 'string', 'timezone'],
            'language' => ['nullable', 'string', 'in:en,sw'],
        ];
    }

    /**
     * Get the validation rules for updating a user.
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_country' => ['nullable', 'string', 'max:2'],
            'status' => ['nullable', 'integer', 'in:0,1,2'],
            'timezone' => ['nullable', 'string', 'timezone'],
            'language' => ['nullable', 'string', 'in:en,sw'],
        ];
    }

    /**
     * Scope a query to search users.
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'like', "%{$value}%")
            ->orWhere('email', 'like', "%{$value}%")
            ->orWhere('phone', 'like', "%{$value}%");
    }

    /**
     * Get the user's abilities.
     */
    public function getAbilitiesAttribute()
    {
        return $this->roles->flatMap->permissions->pluck('name')->unique()->values();
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return (bool) $role->intersect($this->roles)->count();
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrFail();
        }

        return $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Remove a role from the user.
     */
    public function unAssignRole($role)
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrFail();
        }

        return $this->roles()->detach($role);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(Role::where('name', 'administrator')->firstOrFail());
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Role::where('name', 'manager')->firstOrFail());
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Role::where('name', 'citizen')->firstOrFail());
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Role::where('name', 'resident')->firstOrFail());
    }

    public function refugee(): BelongsTo
    {
        return $this->belongsTo(Role::where('name', 'refugee')->firstOrFail());
    }

    public function diplomat(): BelongsTo
    {
        return $this->belongsTo(Role::where('name', 'diplomat')->firstOrFail());
    }

    public function foreigner(): BelongsTo
    {
        return $this->belongsTo(Role::where('name', 'foreigner')->firstOrFail());
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Role::where('name', 'guest')->firstOrFail());
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
