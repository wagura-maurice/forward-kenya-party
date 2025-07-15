<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceType extends Model
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
        return \App\Http\Requests\API\ServiceTypeRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\ServiceTypeResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('service_types', 'uuid')],
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('service_types', 'slug')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('service_types', 'uuid')->ignore($id)],
            'name' => 'nullable|string',
            'slug' => ['nullable', 'string', Rule::unique('service_types', 'slug')->ignore($id)],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }
}
