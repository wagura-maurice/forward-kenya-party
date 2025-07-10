<?php

namespace App\Http\Controllers\API;

use App\Models\Profile;
use Illuminate\Support\Carbon;
use App\Filters\DateRangeFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\AbstractController;

class ProfileController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getModel(): string
    {
        return Profile::class;
    }

    protected function getAllowedFilters(): array
    {
        return [
            'gender',
            '_status',
            AllowedFilter::callback('search', function ($query, $value) {
                $query->where('uuid', 'like', "%{$value}%")
                    ->orWhere('telephone', 'like', "%{$value}%")
                    ->orWhere('salutation', 'like', "%{$value}%")
                    ->orWhere('first_name', 'like', "%{$value}%")
                    ->orWhere('middle_name', 'like', "%{$value}%")
                    ->orWhere('last_name', 'like', "%{$value}%")
                    ->orWhere('address_line_1', 'like', "%{$value}%")
                    ->orWhere('address_line_2', 'like', "%{$value}%")
                    ->orWhere('city', 'like', "%{$value}%")
                    ->orWhere('state', 'like', "%{$value}%")
                    ->orWhere('country', 'like', "%{$value}%")
                    ->orWhere('date_of_birth', 'like', "%{$value}%")
                    ->orWhere('passport_number', 'like', "%{$value}%")
                    ->orWhere('national_identification_number', 'like', "%{$value}%")
                    ->orWhere('driver_license_number', 'like', "%{$value}%")
                    ->orWhere('occupation', 'like', "%{$value}%")
                    ->orWhere('employer_details', 'like', "%{$value}%")
                    ->orWhereHas('user', function($query) use ($value) {
                        $query->where('name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    });
            }),
            AllowedFilter::callback('created_at_date_range', function ($query, $value) {
                // Validate the value using the Date Range Filter Format rule
                $validator = Validator::make(['created_at_date_range' => $value], [
                    'created_at_date_range' => ['required', new DateRangeFilter],
                ]);

                // Check if validation fails
                if ($validator->fails()) {
                    // Handle validation failure (e.g., throw an exception or return a response)
                    // For simplicity, let's assume we throw an exception
                    throw new \InvalidArgumentException('Invalid created_at_date_range format: ' . $validator->errors()->first('created_at_date_range'));
                }

                // If validation passes, proceed with filtering
                $dates = explode('\\', $value);
                if (count($dates) === 2) {
                    $start = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
                    $end = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();

                    $query->whereBetween('created_at', [$start, $end]);
                }
            })
        ];
    }

    protected function getAllowedIncludes(): array
    {
        return [
            'user'
        ];
    }

    protected function getDefaultSort(): string
    {
        return '-id';
    }

    protected function getAllowedSorts(): array
    {
        // Get the model class name
        $modelClass = $this->getModel();
        // Retrieve fillable attributes of the model
        $fillableAttributes = (new $modelClass())->getFillable();
        // Append 'created_at' and 'updated_at' to the fillable attributes
        $sortableAttributes = array_merge($fillableAttributes, ['created_at', 'updated_at']);

        return $sortableAttributes;
    }
}
