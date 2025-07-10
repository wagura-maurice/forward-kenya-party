<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use SoftDeletes;

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
        'configuration'
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
     * Get the type that owns the language.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LanguageType::class);
    }

    /**
     * Get the category that owns the language.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(LanguageCategory::class);
    }
}
