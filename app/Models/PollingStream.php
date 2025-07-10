<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PollingStream extends Model
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
        'station_id',
        'name',
        'code',
        'voter_capacity',
        'registered_voters',
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
        'registered_voters' => 'integer',
    ];

    /**
     * Get the polling center that owns the stream.
     */
    public function center(): BelongsTo
    {
        return $this->belongsTo(PollingCenter::class);
    }

    /**
     * Get the polling station that owns the stream.
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(PollingStation::class);
    }

    /**
     * Get the type of the polling stream.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(PollingStationType::class);
    }

    /**
     * Get the category of the polling stream.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PollingStationCategory::class);
    }
}
