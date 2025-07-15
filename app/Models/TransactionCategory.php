<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TransactionCategory extends Model
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
        return \App\Http\Requests\API\TransactionCategoryRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\TransactionCategoryResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('transaction_categories', 'uuid')],
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('transaction_categories', 'slug')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('transaction_categories', 'uuid')->ignore($id)],
            'name' => 'nullable|string',
            'slug' => ['nullable', 'string', Rule::unique('transaction_categories', 'slug')->ignore($id)],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class)->withTimestamps();
    }
}
