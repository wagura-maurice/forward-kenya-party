<?php

namespace App\Models;

use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable /* implements MustVerifyEmail */
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    
    // We'll implement our own profile photo handling
    use HasProfilePhoto;

    // status
    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;
    
    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        // If we have a profile photo ID, get the URL from the media table
        if ($this->profile_photo_id && $this->profilePhoto) {
            return Storage::url($this->profilePhoto->file_path);
        }
        
        // Fall back to the default implementation from HasProfilePhoto
        $default = $this->defaultProfilePhotoUrl();
        return $default ?: null;
    }
    
    /**
     * Update the user's profile photo from a file upload.
     *
     * @param  mixed  $photo
     * @return void
     */
    public function updateProfilePhoto($photo)
    {
        // Create a new media record for the profile photo
        $media = Media::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'profile_photo_' . $this->id,
            'slug' => 'profile-photo-' . $this->id,
            'description' => 'Profile photo for ' . $this->name,
            'type_id' => MediaType::where('slug', $photo->extension())->first()->id, // Assuming 2 is the ID for 'image' type
            'category_id' => MediaCategory::where('slug', 'profile_photo')->first()->id, // Default category
            'file_path' => $photo->store('uploads/media/profile-photos/' . Str::snake(now()->toDateTimeString()), 'public'),
            'file_name' => $photo->hashName(),
            'file_extension' => $photo->extension(),
            'file_size' => $photo->getSize(),
            'mime_type' => $photo->getMimeType(),
            'is_public' => true,
            'metadata' => [
                'upload_source' => 'profile_photo',
                'user_id' => $this->id,
                'original_name' => $photo->getClientOriginalName(),
            ]
        ]);
        
        // Update the user's profile_photo_path
        $this->forceFill([
            'profile_photo_path' => $media->file_path,
        ])->save();
    }
    
    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token', 
        'current_team_id',
        'profile_photo_path',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'last_login_at', // Add last login timestamp
        'last_login_ip', // Add last login IP address
        'last_login_device', // Add last login device
        'last_login_user_agent', // Add last login user agent
        'last_login_os', // Add last login operating system
        'last_login_location', // Add last login location
        
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
     * Get all of the activity notifications for the user.
     */
    public function activityNotifications(): HasMany
    {
        return $this->hasMany(ActivityNotification::class);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasVerifiedEmail()
    {
        // Only check if the email is present
        if ($this->email) {
            return ! is_null($this->email_verified_at);
        }
        
        // If email is not provided, return true or handle as required
        return true; // Email verification is considered "not required"
    }

    public static function createRules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'nullable|email|unique:users',
            'email_verified_at' => 'nullable|string|confirmed',
            'password' => 'required|string|min:8|confirmed',
            'remember_token' => 'nullable|string',
            'current_team_id' => 'nullable|exists:teams,id',
            'profile_photo_path' => 'nullable|string',
            'two_factor_secret' => 'nullable|string',
            'two_factor_recovery_codes' => 'nullable|string',
            'two_factor_confirmed_at' => 'nullable|string',
            'last_login_at' => 'nullable|string',
            'last_login_ip' => 'nullable|string',
            'last_login_device' => 'nullable|string',
            'last_login_user_agent' => 'nullable|string',
            'last_login_os' => 'nullable|string',
            'last_login_location' => 'nullable|string',
        ];
    }

    public static function updateRules(int $id): array
    {
        return [
            'name' => 'nullable|string',
            'email' => 'nullable|email|' . Rule::unique('users', 'email')->ignore($id),
            'email_verified_at' => 'nullable|string',
            'password' => 'nullable|string',
            'remember_token' => 'nullable|string',
            'current_team_id' => 'nullable|exists:teams,id',
            'profile_photo_path' => 'nullable|string',
            'two_factor_secret' => 'nullable|string',
            'two_factor_recovery_codes' => 'nullable|string',
            'two_factor_confirmed_at' => 'nullable|string',
            'last_login_at' => 'nullable|string',
            'last_login_ip' => 'nullable|string',
            'last_login_device' => 'nullable|string',
            'last_login_user_agent' => 'nullable|string',
            'last_login_os' => 'nullable|string',
            'last_login_location' => 'nullable|string',
        ];
    }

    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            $queryTelephoneUtil = PhoneNumberUtil::getInstance();
            $queryTelephoneValue = null;

            // Validate phone number as a Kenyan number
            try {
                $queryTelephoneProto = $queryTelephoneUtil->parse($value, 'KE'); // 'KE' is the country code for Kenya
                if ($queryTelephoneUtil->isValidNumberForRegion($queryTelephoneProto, 'KE')) {
                    $queryTelephoneValue = $queryTelephoneUtil->format($queryTelephoneProto, PhoneNumberFormat::E164);
                }
            } catch (\libphonenumber\NumberParseException $e) {
                // If parsing fails, $queryTelephoneValue will remain null
            }

            // Main table searches
            $query->where('name', 'like', "%{$value}%")
                ->orWhere('email', 'like', "%{$value}%");

            // Profile-related search (use formatted phone number with '+' symbol)
            $query->orWhereHas('profile', function ($profileQuery) use ($value, $queryTelephoneValue) {
                $profileQuery->where(function ($q) use ($value, $queryTelephoneValue) {
                    $q->where('telephone', 'like', "%" . (isset($queryTelephoneValue) ? $queryTelephoneValue : $value) . "%")
                        ->orWhere('uuid', 'like', "%{$value}%")
                        ->orWhere('first_name', 'like', "%{$value}%")
                        ->orWhere('middle_name', 'like', "%{$value}%")
                        ->orWhere('last_name', 'like', "%{$value}%")
                        ->orWhere('address_line_1', 'like', "%{$value}%")
                        ->orWhere('address_line_2', 'like', "%{$value}%")
                        ->orWhere('city', 'like', "%{$value}%")
                        ->orWhere('state', 'like', "%{$value}%")
                        ->orWhere('country', 'like', "%{$value}%")
                        ->orWhere('passport_number', 'like', "%{$value}%")
                        ->orWhere('national_identification_number', 'like', "%{$value}%")
                        ->orWhere('driver_license_number', 'like', "%{$value}%")
                        ->orWhere('employer_details', 'like', "%{$value}%")
                        ->orWhere('proof_of_address', 'like', "%{$value}%")
                        ->orWhere('proof_of_identity', 'like', "%{$value}%");
                });
            });
        });
    }

    public function getAbilitiesAttribute()
    {
        return $this->roles->map->abilities->flatten()->pluck('slug')->unique();
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        if (is_array($role)) {
            return count($this->roles->whereIn('slug', $role)) > 0;
        }

        return false;
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        return $this->roles()->sync($role, false);
    }

    public function unAssignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        return $this->roles()->detach($role);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'id', 'user_id');
    }

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'id', 'user_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'id', 'user_id');
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'id', 'user_id');
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'id', 'user_id');
    }

    public function refugee(): BelongsTo
    {
        return $this->belongsTo(Refugee::class, 'id', 'user_id');
    }

    public function diplomat(): BelongsTo
    {
        return $this->belongsTo(Diplomat::class, 'id', 'user_id');
    }

    public function foreigner(): BelongsTo
    {
        return $this->belongsTo(Foreigner::class, 'id', 'user_id');
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'id', 'user_id');
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
