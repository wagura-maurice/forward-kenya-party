<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PollingStationCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'configuration'
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\PollingStationCategoryRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\PollingStationCategoryResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('polling_station_categories', 'uuid')],
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('polling_station_categories', 'slug')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('polling_station_categories', 'uuid')->ignore($id)],
            'name' => 'nullable|string',
            'slug' => ['nullable', 'string', Rule::unique('polling_station_categories', 'slug')->ignore($id)],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public function polling_stations(): BelongsToMany
    {
        return $this->belongsToMany(PollingStation::class)->withTimestamps();
    }
}
