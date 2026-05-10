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

    // Constants for preferred contact method
    const CONTACT_METHOD_EMAIL = 0;
    const CONTACT_METHOD_TEXT_MESSAGE = 1;
    const CONTACT_METHOD_WHATSAPP = 2;

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
        'language_id',
        'marital_status',
        'highest_level_of_education',
        'employment_status',
        'income_source',
        'job_title',
        'company_name',
        'work_address',
        'work_phone',
        'proof_of_address',
        'proof_of_identity',
        'security_question',
        'security_answer',
        'social_media',
        'biography',
        'hobbies_interests',
        'preferred_contact_method',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'languages_spoken' => 'array',
        'emergency_contact' => 'array',
        'proof_of_address' => 'array',
        'proof_of_identity' => 'array',
        'social_media' => 'array',
        'hobbies_interests' => 'array',
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
            'language_id' => 'nullable|exists:languages,id',
            'marital_status' => 'nullable|integer',
            'highest_level_of_education' => 'nullable|integer',
            'employment_status' => 'nullable|integer',
            'income_source' => 'nullable|integer',
            'job_title' => 'nullable|string',
            'company_name' => 'nullable|string',
            'work_address' => 'nullable|string',
            'work_phone' => 'nullable|string',
            'proof_of_address' => 'nullable|json',
            'proof_of_identity' => 'nullable|json',
            'security_question' => 'nullable|string',
            'security_answer' => 'nullable|string',
            'social_media' => 'nullable|json',
            'biography' => 'nullable|string',
            'hobbies_interests' => 'nullable|json',
            'preferred_contact_method' => 'nullable|integer',
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
            'language_id' => 'nullable|exists:languages,id',
            'marital_status' => 'nullable|integer',
            'highest_level_of_education' => 'nullable|integer',
            'employment_status' => 'nullable|integer',
            'income_source' => 'nullable|integer',
            'job_title' => 'nullable|string',
            'company_name' => 'nullable|string',
            'work_address' => 'nullable|string',
            'work_phone' => 'nullable|string',
            'proof_of_address' => 'nullable|json',
            'proof_of_identity' => 'nullable|json',
            'security_question' => 'nullable|string',
            'security_answer' => 'nullable|string',
            'social_media' => 'nullable|json',
            'biography' => 'nullable|string',
            'hobbies_interests' => 'nullable|json',
            'preferred_contact_method' => 'nullable|integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'user_id', 'user_id');
    }

    /**
     * Get the activities for the profile.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the communications for the profile.
     */
    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    /**
     * Get the documents for the profile.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the feedback for the profile.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
}
