<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PollingStation extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    public const PENDING = 0;
    public const ACTIVE = 1;
    public const INACTIVE = 2;
    public const SUSPENDED = 3;

    public static function statusLabels()
    {
        return [
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_id',
        'category_id',
        'center_id',
        'name',
        'code',
        'voter_capacity',
        'is_active',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'voter_capacity' => 'integer',
    ];

    /**
     * Get the polling center that owns the polling station.
     */
    public function center(): BelongsTo
    {
        return $this->belongsTo(PollingCenter::class);
    }

    /**
     * Get the type of the polling station.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(PollingStationType::class);
    }

    /**
     * Get the category of the polling station.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PollingStationCategory::class);
    }

    /**
     * Get the streams for the polling station.
     */
    public function streams(): HasMany
    {
        return $this->hasMany(PollingStream::class);
    }
}
