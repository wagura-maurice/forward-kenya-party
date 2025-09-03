<?php

namespace App\Models;

use App\Traits\LogsActivityWithMetadata;
use App\Enums\Gender;
use App\Enums\Salutation;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class Profile extends Model
{
    use HasFactory, SoftDeletes, LogsActivityWithMetadata;
    
    /**
     * Get the options for the activity logger.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
        'special_interest_groups',
        'disability_status',
        'ncpwd_number',
        'ethnicity_id',
        'language_id',
        'religion_id',
        'other_religion',
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
        'special_interest_groups' => 'array',
        'email_verified_at' => 'datetime',
        'kyc_verified' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the default log options for the model.
     */
    protected function getDefaultLogOptions(): \Spatie\Activitylog\LogOptions
    {
        return parent::getDefaultLogOptions()
            ->useLogName('profiles')
            ->dontLogIfAttributesChangedOnly([
                'last_updated_at',
                'updated_at',
                'last_modified_by',
                'last_modified_ip',
                'created_at',
                'deleted_at'
            ]);
    }

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
            'special_interest_groups' => 'nullable|json',
            'disability_status' => 'nullable|string',
            'ncpwd_number' => 'nullable|string',
            'ethnicity_id' => 'nullable|exists:ethnicities,id',
            'language_id' => 'nullable|exists:languages,id',
            'religion_id' => 'nullable|exists:religions,id',
            'other_religion' => 'nullable|string',
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
            'special_interest_groups' => 'nullable|json',
            'disability_status' => 'nullable|string',
            'ncpwd_number' => 'nullable|string',
            'ethnicity_id' => 'nullable|exists:ethnicities,id',
            'language_id' => 'nullable|exists:languages,id',
            'religion_id' => 'nullable|exists:religions,id',
            'other_religion' => 'nullable|string',
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
