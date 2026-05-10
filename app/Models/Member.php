<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'county_id',
        'sub_county_id',
        'constituency_id',
        'ward_id',
        'location_id',
        'village_id',
        'polling_center_id',
        'polling_station_id',
        'polling_stream_id',
        'special_interest_groups',
        'disability_status',
        'ncpwd_number',
        'ethnicity_id',
        'religion_id',
        'passport_number',
        'national_identification_number',
        'party_membership_number',
        'configuration',
        'is_featured',
        'metadata',
        'last_verified_at',
        'verified_by',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'special_interest_groups' => 'array',
        'disability_status' => 'boolean',
        'configuration' => 'array',
        'is_featured' => 'boolean',
        'metadata' => 'array',
        'last_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the member record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the county that the member belongs to.
     */
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    /**
     * Get the sub-county that the member belongs to.
     */
    public function subCounty(): BelongsTo
    {
        return $this->belongsTo(SubCounty::class);
    }

    /**
     * Get the constituency that the member belongs to.
     */
    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    /**
     * Get the ward that the member belongs to.
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the location that the member belongs to.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the village that the member belongs to.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Get the polling center that the member belongs to.
     */
    public function pollingCenter(): BelongsTo
    {
        return $this->belongsTo(PollingCenter::class);
    }

    /**
     * Get the polling station that the member belongs to.
     */
    public function pollingStation(): BelongsTo
    {
        return $this->belongsTo(PollingStation::class);
    }

    /**
     * Get the polling stream that the member belongs to.
     */
    public function pollingStream(): BelongsTo
    {
        return $this->belongsTo(PollingStream::class);
    }

    /**
     * Get the ethnicity that the member belongs to.
     */
    public function ethnicity(): BelongsTo
    {
        return $this->belongsTo(Ethnicity::class);
    }

    /**
     * Get religion that member belongs to.
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }

    /**
     * Get user who verified the member.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the profile associated with the member.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'user_id', 'user_id');
    }

    /**
     * Get the validation rules for creating a new member.
     */
    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'string', 'unique:members,uuid'],
            'user_id' => ['required', 'exists:users,id'],
            'county_id' => ['nullable', 'exists:counties,id'],
            'sub_county_id' => ['nullable', 'exists:sub_counties,id'],
            'constituency_id' => ['nullable', 'exists:constituencies,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'polling_center_id' => ['nullable', 'exists:polling_centers,id'],
            'polling_station_id' => ['nullable', 'exists:polling_stations,id'],
            'polling_stream_id' => ['nullable', 'exists:polling_streams,id'],
            'special_interest_groups' => ['nullable', 'array'],
            'disability_status' => ['boolean'],
            'ncpwd_number' => ['nullable', 'string', 'max:50'],
            'ethnicity_id' => ['nullable', 'exists:ethnicities,id'],
            'religion_id' => ['nullable', 'exists:religions,id'],
            'passport_number' => ['nullable', 'string', 'max:50', 'unique:members,passport_number'],
            'national_identification_number' => ['nullable', 'string', 'max:50', 'unique:members,national_identification_number'],
            'party_membership_number' => ['required', 'string', 'max:50', 'unique:members,party_membership_number'],
            'configuration' => ['nullable', 'array'],
            'is_featured' => ['boolean'],
            'metadata' => ['nullable', 'array'],
            'last_verified_at' => ['nullable', 'date'],
            'verified_by' => ['nullable', 'exists:users,id'],
        ];
    }

    /**
     * Get the validation rules for updating a member.
     */
    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'string', 'unique:members,uuid,' . $id],
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'county_id' => ['sometimes', 'nullable', 'exists:counties,id'],
            'sub_county_id' => ['sometimes', 'nullable', 'exists:sub_counties,id'],
            'constituency_id' => ['sometimes', 'nullable', 'exists:constituencies,id'],
            'ward_id' => ['sometimes', 'nullable', 'exists:wards,id'],
            'location_id' => ['sometimes', 'nullable', 'exists:locations,id'],
            'village_id' => ['sometimes', 'nullable', 'exists:villages,id'],
            'polling_center_id' => ['sometimes', 'nullable', 'exists:polling_centers,id'],
            'polling_station_id' => ['sometimes', 'nullable', 'exists:polling_stations,id'],
            'polling_stream_id' => ['sometimes', 'nullable', 'exists:polling_streams,id'],
            'special_interest_groups' => ['sometimes', 'nullable', 'array'],
            'disability_status' => ['sometimes', 'boolean'],
            'ncpwd_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'ethnicity_id' => ['sometimes', 'nullable', 'exists:ethnicities,id'],
            'religion_id' => ['sometimes', 'nullable', 'exists:religions,id'],
            'passport_number' => ['nullable', 'string', 'max:50', 'unique:members,passport_number,' . $id],
            'national_identification_number' => ['nullable', 'string', 'max:50', 'unique:members,national_identification_number,' . $id],
            'party_membership_number' => ['sometimes', 'required', 'string', 'max:50', 'unique:members,party_membership_number,' . $id],
            'configuration' => ['sometimes', 'nullable', 'array'],
            'is_featured' => ['sometimes', 'boolean'],
            'metadata' => ['sometimes', 'nullable', 'array'],
            'last_verified_at' => ['sometimes', 'nullable', 'date'],
            'verified_by' => ['sometimes', 'nullable', 'exists:users,id'],
        ];
    }

    /**
     * Scope a query to search members.
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('party_membership_number', 'like', "%{$value}%")
            ->orWhere('national_identification_number', 'like', "%{$value}%")
            ->orWhere('passport_number', 'like', "%{$value}%")
            ->orWhereHas('user', function ($userQuery) use ($value) {
                $userQuery->where('name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%");
            });
    }
}
