<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Religion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'type_id',
        'category_id',
        'name',
        'slug',
        'description',
        'configuration',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'configuration' => 'array',
    ];

    /**
     * Get the type that owns the religion.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ReligionType::class);
    }

    /**
     * Get the category that owns the religion.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ReligionCategory::class);
    }
}
