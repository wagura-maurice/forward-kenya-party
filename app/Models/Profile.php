<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\CausesActivity;

class Profile extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, CausesActivity;
    
    // Constants for marital status
    const MARITAL_STATUS_SINGLE = 0;
    const MARITAL_STATUS_MARRIED = 1;
    const MARITAL_STATUS_DIVORCED = 2;
    const MARITAL_STATUS_SEPARATED = 3;
    const MARITAL_STATUS_WIDOWED = 4;

    // Constants for highest level of education
    const HIGHEST_LEVEL_OF_EDUCATION_PRIMARY = 0;
    const HIGHEST_LEVEL_OF_EDUCATION_SECONDARY = 1;
    const HIGHEST_LEVEL_OF_EDUCATION_HIGH_SCHOOL = 2;
    const HIGHEST_LEVEL_OF_EDUCATION_UNIVERSITY = 3;
    const HIGHEST_LEVEL_OF_EDUCATION_OTHER = 4;

    /**
     * The attributes that should be logged for the profile.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        $options = LogOptions::defaults()
            ->useLogName('profiles')
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['last_updated_at', 'updated_at'])
            ->logExcept([
                'created_at', 
                'updated_at', 
                'deleted_at',
                'user_id',
                'id_number_verified_at',
                'email_verified_at',
                'phone_verified_at'
            ]);
            
        // Add properties to all logged activities
        $options->properties = array_merge($options->properties ?? [], [
            'type_id' => 1,
            'category_id' => 1,
        ]);
        
        // Set description for events
        $options->setDescriptionForEvent(function(string $eventName) {
            return match($eventName) {
                'created' => 'Profile was created',
                'updated' => 'Profile was updated',
                'deleted' => 'Profile was deleted',
                'restored' => 'Profile was restored',
                'forceDeleted' => 'Profile was permanently deleted',
                default => "Profile was {$eventName}",
            };
        });
        
        return $options;
    }
    
    /**
     * Log a custom activity for this profile.
     *
     * @param string $action
     * @param string $description
     * @param array $properties
     * @param string|null $logName
     * @return \Spatie\Activitylog\Models\Activity
     */
    public function logActivity(string $action, string $description, array $properties = [], ?string $logName = null)
    {
        $causer = auth()->user() ?? $this->user ?? null;
        
        $activity = activity($logName ?? 'profiles')
            ->performedOn($this)
            ->withProperties($properties);
            
        if ($causer) {
            $activity->causedBy($causer);
        }
            
        return $activity->log($description);
    }

    /**
     * Get all activities for this profile.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
    
    /**
     * Get activities related to this profile.
     */
    public function profileActivities()
    {
        return Activity::where('subject_type', self::class)
            ->where('subject_id', $this->id);
    }
    
    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::updating(function ($profile) {
            // Log when sensitive information is updated
            if ($profile->isDirty(['id_number', 'date_of_birth', 'gender'])) {
                $profile->logActivity(
                    'sensitive_update',
                    'Updated sensitive profile information',
                    [
                        'changed' => $profile->getDirty(),
                        'old' => array_intersect_key($profile->getOriginal(), $profile->getDirty())
                    ]
                );
            }
        });
        
        static::created(function ($profile) {
            // Log profile creation with additional context
            $profile->logActivity(
                'created',
                'Profile was created',
                ['user_id' => $profile->user_id]
            );
        });
        
        static::deleted(function ($profile) {
            // Log profile deletion with soft delete status
            $profile->logActivity(
                $profile->isForceDeleting() ? 'force_deleted' : 'deleted',
                'Profile was ' . ($profile->isForceDeleting() ? 'permanently deleted' : 'soft deleted')
            );
        });
        
        static::restored(function ($profile) {
            // Log profile restoration
            $profile->logActivity('restored', 'Profile was restored');
        });
    }



    // Constants for employment status
    const EMPLOYMENT_STATUS_EMPLOYED = 0;
    const EMPLOYMENT_STATUS_UNEMPLOYED = 1;
    const EMPLOYMENT_STATUS_SELF_EMPLOYED = 2;
    const EMPLOYMENT_STATUS_RETIRED = 3;
    const EMPLOYMENT_STATUS_STUDENT = 4;

    // Constants for income source
    const INCOME_SOURCE_SALARY = 0;
    const INCOME_SOURCE_BUSINESS = 1;
    const INCOME_SOURCE_INVESTMENT = 2;
    const INCOME_SOURCE_PENSION = 3;
    const INCOME_SOURCE_OTHER = 4;

    public static function maritalStatusLabels()
    {
        return [
            self::MARITAL_STATUS_SINGLE => 'Single',
            self::MARITAL_STATUS_MARRIED => 'Married',
            self::MARITAL_STATUS_DIVORCED => 'Divorced',
            self::MARITAL_STATUS_SEPARATED => 'Separated',
            self::MARITAL_STATUS_WIDOWED => 'Widowed',
        ];
    }

    public static function highestLevelOfEducationLabels()
    {
        return [
            self::HIGHEST_LEVEL_OF_EDUCATION_PRIMARY => 'Primary',
            self::HIGHEST_LEVEL_OF_EDUCATION_SECONDARY => 'Secondary',
            self::HIGHEST_LEVEL_OF_EDUCATION_HIGH_SCHOOL => 'High School',
            self::HIGHEST_LEVEL_OF_EDUCATION_UNIVERSITY => 'University',
            self::HIGHEST_LEVEL_OF_EDUCATION_OTHER => 'Other',
        ];
    }

    public static function employmentStatusLabels()
    {
        return [
            self::EMPLOYMENT_STATUS_EMPLOYED => 'Employed',
            self::EMPLOYMENT_STATUS_UNEMPLOYED => 'Unemployed',
            self::EMPLOYMENT_STATUS_SELF_EMPLOYED => 'Self-employed',
            self::EMPLOYMENT_STATUS_RETIRED => 'Retired',
            self::EMPLOYMENT_STATUS_STUDENT => 'Student',
        ];
    }

    public static function incomeSourceLabels()
    {
        return [
            self::INCOME_SOURCE_SALARY => 'Salary',
            self::INCOME_SOURCE_BUSINESS => 'Business',
            self::INCOME_SOURCE_INVESTMENT => 'Investment',
            self::INCOME_SOURCE_PENSION => 'Pension',
            self::INCOME_SOURCE_OTHER => 'Other',
        ];
    }

    public static function getMaritalStatusValueByLabel(string $label)
    {
        $maritalStatusOptions = self::maritalStatusLabels();
        $lowerLabel = strtolower($label);

        foreach ($maritalStatusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    public static function getHighestLevelOfEducationValueByLabel(string $label)
    {
        $highestLevelOfEducationOptions = self::highestLevelOfEducationLabels();
        $lowerLabel = strtolower($label);

        foreach ($highestLevelOfEducationOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    public static function getEmploymentStatusValueByLabel(string $label)
    {
        $employmentStatusOptions = self::employmentStatusLabels();
        $lowerLabel = strtolower($label);

        foreach ($employmentStatusOptions as $key => $value) {
            if (strpos(strtolower($value), $lowerLabel) !== false) {
                return $key;
            }
        }

        return false;
    }

    public static function getIncomeSourceValueByLabel(string $label)
    {
        $incomeSourceOptions = self::incomeSourceLabels();
        $lowerLabel = strtolower($label);

        foreach ($incomeSourceOptions as $key => $value) {
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
        'telephone',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'nationality',
        'place_of_birth',
        'languages_spoken',
        'blood_type',
        'emergency_contact',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'date_of_birth',
        'disability_status',
        'ncpwd_number',
        'ethnicity_id',
        'language_id',
        'religion_id',
        'marital_status',
        'highest_level_of_education',
        'employment_status',
        'income_source',
        'job_title',
        'company_name',
        'work_address',
        'work_phone',
        'linkedin_username',
        'proof_of_address',
        'proof_of_identity',
        'security_question',
        'security_answer',
        'social_media',
        'biography',
        'hobbies_interests',
        'communication_preferences',
        'preferred_contact_method',
        'telegram_user_id',
        'telegram_username',
        'kyc_verified',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime',
        'kyc_verified' => 'boolean',
        'metadata' => 'array',
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\ProfileRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\ProfileResource::class;
    }

    public static function createRules(): array
    {
        return [
            'uuid' => ['required', 'string', Rule::unique('profiles')],
            'user_id' => 'required|exists:users,id',
            'telephone' => ['nullable', 'string', 'telephone', Rule::unique('profiles')],
            'salutation' => ['nullable', 'integer', Rule::in(array_keys(Salutation::getSalutationOptions()))],
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'gender' => ['nullable', 'string', Rule::in(array_keys(Gender::getGenderOptions()))],
            'nationality' => 'nullable|string',
            'place_of_birth' => 'nullable|string',
            'languages_spoken' => 'nullable|json',
            'blood_type' => 'nullable|string',
            'emergency_contact' => 'nullable|json',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'date_of_birth' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    $minDate = Carbon::now()->subYears(72);
                    $maxDate = Carbon::now()->subYears(18);
            
                    if ($date->lt($minDate) || $date->gt($maxDate)) {
                        $fail("The $attribute must be between 18 and 72 years old.");
                    }
                },
            ],
            'disability_status' => 'nullable|string',
            'ncpwd_number' => 'nullable|string|unique:profiles,ncpwd_number',
            'ethnicity_id' => 'nullable|exists:ethnicities,id',
            'language_id' => 'nullable|exists:languages,id',
            'religion_id' => 'nullable|exists:religions,id',
            'marital_status' => 'nullable|string',
            'highest_level_of_education' => 'nullable|string',
            'employment_status' => 'nullable|string',
            'income_source' => 'nullable|string',
            'job_title' => 'nullable|string',
            'company_name' => 'nullable|string',
            'work_address' => 'nullable|string',
            'work_phone' => 'nullable|string',
            'linkedin_username' => 'nullable|string',
            'proof_of_address' => 'nullable|json',
            'proof_of_identity' => 'nullable|json',
            'security_question' => 'nullable|string',
            'security_answer' => 'nullable|string',
            'social_media' => 'nullable|json',
            'biography' => 'nullable|string',
            'hobbies_interests' => 'nullable|json',
            'communication_preferences' => 'nullable|json',
            'preferred_contact_method' => 'nullable|json',
            'telegram_user_id' => 'nullable|integer',
            'telegram_username' => 'nullable|string',
            'kyc_verified' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
    
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('profiles')->ignore($id)],
            'user_id' => 'nullable|exists:users,id',
            'telephone' => ['nullable', 'string', 'telephone', Rule::unique('profiles')->ignore($id)],
            'salutation' => ['nullable', 'integer', Rule::in(array_keys(Salutation::getSalutationOptions()))],
            'first_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'gender' => ['nullable', 'string', Rule::in(array_keys(Gender::getGenderOptions()))],
            'nationality' => 'nullable|string',
            'place_of_birth' => 'nullable|string',
            'languages_spoken' => 'nullable|json',
            'blood_type' => 'nullable|string',
            'emergency_contact' => 'nullable|json',
            'address_line_1' => 'nullable|string',
            'address_line_2' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'date_of_birth' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);
                    $minDate = Carbon::now()->subYears(72);
                    $maxDate = Carbon::now()->subYears(18);
            
                    if ($date->lt($minDate) || $date->gt($maxDate)) {
                        $fail("The $attribute must be between 18 and 72 years old.");
                    }
                },
            ],
            'disability_status' => 'nullable|string',
            'ncpwd_number' => ['nullable', 'string', 'unique:profiles,ncpwd_number', Rule::unique('profiles')->ignore($id)],
            'ethnicity_id' => 'nullable|exists:ethnicities,id',
            'language_id' => 'nullable|exists:languages,id',
            'religion_id' => 'nullable|exists:religions,id',
            'marital_status' => 'nullable|string',
            'highest_level_of_education' => 'nullable|string',
            'employment_status' => 'nullable|string',
            'income_source' => 'nullable|string',
            'job_title' => 'nullable|string',
            'company_name' => 'nullable|string',
            'work_address' => 'nullable|string',
            'work_phone' => 'nullable|string',
            'linkedin_username' => 'nullable|string',
            'proof_of_address' => 'nullable|json',
            'proof_of_identity' => 'nullable|json',
            'security_question' => 'nullable|string',
            'security_answer' => 'nullable|string',
            'social_media' => 'nullable|json',
            'biography' => 'nullable|string',
            'hobbies_interests' => 'nullable|json',
            'communication_preferences' => 'nullable|json',
            'preferred_contact_method' => 'nullable|json',
            'telegram_user_id' => 'nullable|integer',
            'telegram_username' => 'nullable|string',
            'kyc_verified' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ethnicity(): BelongsTo
    {
        return $this->belongsTo(Ethnicity::class, 'ethnicity_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'user_id', 'user_id');
    }
}
