<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const INACTIVE = 0;
    const ACTIVE = 1;
    const SUSPENDED = 2;
    const UNDER_REVIEW = 3;

    public static function getStatusOptions(): array
    {
        return [
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::UNDER_REVIEW => 'Under Review',
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
     * @var array
     */
    protected $fillable = [
        'uuid',
        'type_id',
        'category_id',
        'user_id',
        'payable',
        'discount',
        'paid',
        'balance',
        'description',
        'issued_at_timestamp',
        'due_at_timestamp',
        '_receipts',
        '_statement',
        '_status',
        'total_with_tax',
        'tax_rate',
        'metadata',
    ];

    protected function getRequestClass(): string
    {
        return \App\Http\Requests\API\BankRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\BankResource::class;
    }

    public static function createRules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('banks', 'uuid')],
            'type_id' => 'required|integer|exists:bank_types,id',
            'category_id' => 'required|integer|exists:bank_categories,id',
            'user_id' => 'required|integer|exists:users,id',
            'payable' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'issued_at_timestamp' => 'nullable|date',
            'due_at_timestamp' => 'nullable|date',
            '_receipts' => 'nullable|json|array|min:1',
            '_receipts.*' => 'uuid|exists:receipts,uuid',
            '_statement' => 'nullable|json',
            '_status' => 'required|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
            'total_with_tax' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'metadata' => 'nullable|json',
        ];
    }

    public static function updateRules(int $id): array
    {
        return [
            'uuid' => ['nullable', 'uuid', Rule::unique('banks', 'uuid')->ignore($id)],
            'type_id' => 'nullable|integer|exists:bank_types,id',
            'category_id' => 'nullable|integer|exists:bank_categories,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'payable' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'issued_at_timestamp' => 'nullable|date',
            'due_at_timestamp' => 'nullable|date',
            '_receipts' => 'nullable|json|array|min:1',
            '_receipts.*' => 'uuid|exists:receipts,uuid',
            '_statement' => 'nullable|json',
            '_status' => 'nullable|integer|in:' . implode(',', array_keys(self::getStatusOptions())),
            'total_with_tax' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'metadata' => 'nullable|json',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(BankType::class, 'type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BankCategory::class, 'category_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'bank_id');
    }
}
