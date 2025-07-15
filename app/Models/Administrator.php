<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Administrator extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;
    const SUSPENDED = 3;
    
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
        $statusOptions = self::statusLabels();
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
     * @var array
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'designation',
        'permissions',
        '_status',
    ];
    
    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\AdministratorRequest::class;
    }
    
    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\AdministratorResource::class;
    }
    
    public static function createRules()
    {
        return [
            'uuid' => 'required|uuid|unique:administrators,uuid',
            'user_id' => 'required|exists:users,id',
            'designation' => 'nullable|string',
            'permissions' => 'nullable|json',
            '_status' => 'nullable|in:' . implode(',', array_keys(self::statusLabels())),
        ];
    }
    
    public static function updateRules()
    {
        return [
            'uuid' => 'nullable|uuid|unique:administrators,uuid',
            'user_id' => 'nullable|exists:users,id',
            'designation' => 'nullable|string',
            'permissions' => 'nullable|json',
            '_status' => 'nullable|in:' . implode(',', array_keys(self::statusLabels())),
        ];
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
