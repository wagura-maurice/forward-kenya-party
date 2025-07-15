<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InvoiceType extends Model
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
        return \App\Http\Requests\API\InvoiceTypeRequest::class;
    }

    protected function getResourceClass(): string
    {
        return \App\Http\Resources\API\InvoiceTypeResource::class;
    }

    public static function createRules()
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('invoice_types', 'uuid')],
            'name' => 'required|string',
            'slug' => ['nullable', 'string', Rule::unique('invoice_types', 'slug')],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public static function updateRules(int $id)
    {
        return [
            'uuid' => ['nullable', 'string', Rule::unique('invoice_types', 'uuid')->ignore($id)],
            'name' => 'nullable|string',
            'slug' => ['nullable', 'string', Rule::unique('invoice_types', 'slug')->ignore($id)],
            'description' => 'nullable|string',
            'configuration' => 'nullable|json'
        ];
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class)->withTimestamps();
    }
}
