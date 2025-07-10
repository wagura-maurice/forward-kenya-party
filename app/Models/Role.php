<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
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
        'configuration',
        'created_at',
        'updated_at'
    ];

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('roles', 'uuid')],
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('roles', 'slug')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('roles', 'uuid')->ignore($id)],
            'name' => 'nullable|string',
            'slug' => ['nullable', 'string', Rule::unique('roles', 'slug')->ignore($id)],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public function allowTo($ability)
    {
        if (is_string($ability)) {
            $ability = Ability::where('slug', $ability)->firstOrFail();
        }

        return $this->abilities()->syncWithoutDetaching([$ability->id]);
    }

    public function disallowTo($ability)
    {
        if (is_string($ability)) {
            $ability = Ability::where('slug', $ability)->firstOrFail();
        }

        return $this->abilities()->detach([$ability->id]);
    }

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class, 'ability_role')->withTimestamps();
    }
}
