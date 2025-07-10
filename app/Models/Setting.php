<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item',
        'default_value',
        'current_value'
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\SettingRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\SettingResource::class;
    }
    
    public static function createRules()
    {
        return [
            'item' => ['required', 'string', Rule::unique('settings', 'item')],
            'default_value' => 'required|string',
            'current_value' => 'nullable|string'
        ];
    }
    
    public static function updateRules(int $id)
    {
        return [
            'item' => ['nullable', 'string', Rule::unique('settings', 'item')->ignore($id)],
            'default_value' => 'nullable|string',
            'current_value' => 'nullable|string'
        ];
    }
}
