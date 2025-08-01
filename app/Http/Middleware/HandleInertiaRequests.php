<?php
// app/Http/Middleware/HandleInertiaRequests.php
namespace App\Http\Middleware;

use App\Models\Ward;
use App\Models\County;
use App\Models\Gender;
use App\Models\Service;
use App\Models\Village;
use Inertia\Middleware;
use App\Models\Location;
use App\Models\Religion;
use App\Models\Ethnicity;
use App\Models\SubCounty;
use App\Models\Department;
use App\Models\Constituency;
use Illuminate\Http\Request;
use App\Models\PollingCenter;
use App\Models\PollingStream;
use App\Models\PollingStation;
use App\Services\OTP\OneTimePasswordServices;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $sharedData = [
            'app' => [
                'name' => config('app.name'),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];

        // Share all location and form data for the registration and profile pages
        if ($request->routeIs('register') || str_starts_with($request->path(), 'user/profile') || $request->routeIs('profile.*')) {
            // Import the OTP service constants
            $otpConstants = [
                'ttl' => OneTimePasswordServices::TTL,
                'rate_limit' => OneTimePasswordServices::RATE_LIMIT,
                'attempts_limit' => OneTimePasswordServices::ATTEMPTS_LIMIT,
            ];
            
            $sharedData['formData'] = [
                'genders' => array_map(
                    fn($name, $id) => ['id' => $id, 'name' => $name],
                    array_values(Gender::getGenderOptions()),
                    array_keys(Gender::getGenderOptions())
                ),
                'ethnicities' => Ethnicity::select('id', 'name')
                    ->orderBy('name')
                    ->get()
                    ->toArray(),
                'religions' => Religion::select('id', 'name')
                    ->orderBy('name')
                    ->get()
                    ->toArray(),
                'locations' => [
                    'counties' => County::select('id', 'name')->get()->toArray(),
                    'sub_counties' => SubCounty::select('id', 'name', 'county_id')->get()->toArray(),
                    'constituencies' => Constituency::select('id', 'name', 'county_id')->get()->toArray(),
                    'wards' => Ward::select('id', 'name', 'county_id', 'constituency_id')->get()->toArray(),
                    'locations' => Location::select('id', 'name', 'county_id', 'constituency_id', 'ward_id')->get()->toArray(),
                    'villages' => Village::select('id', 'name', 'county_id', 'constituency_id', 'ward_id', 'location_id')->get()->toArray(),
                    'polling_centers' => PollingCenter::select('id', 'name', 'county_id', 'constituency_id', 'ward_id', 'location_id', 'village_id')->get()->toArray(),
                    'polling_stations' => PollingStation::select('id', 'name', 'center_id')->get()->toArray(),
                    'polling_streams' => PollingStream::select('id', 'name', 'center_id', 'station_id')->get()->toArray(),
                ],
                'membership_number' => generateUniqueMembershipNumber(),
                'otp_config' => $otpConstants,
            ];
        }

        // Share departments for Inertia requests
        if ($request->isMethod('GET') /* && $request->header('X-Inertia') */) {
            $departments = Department::with(['services' => function($query) {
                $query->where('_status', Service::ACTIVE);
            }])
            ->where('_status', Department::ACTIVE)
            ->orderBy('name')
            ->get();

            $sharedData['navigation'] = [
                'departments' => $departments->map(function($department) {
                    return [
                        'id' => $department->id,
                        'name' => $department->name,
                        'slug' => $department->slug,
                        'description' => $department->description,
                        'url' => route('platform.department.show', ['department' => $department->slug]),
                        'services' => $department->services->map(function($service) use ($department) {
                            return [
                                'id' => $service->id,
                                'name' => $service->name,
                                'slug' => $service->slug,
                                'description' => $service->description,
                                'url' => route('platform.service.show', ['service' => $service->slug]),
                                'department_slug' => $department->slug,
                            ];
                        })->values()->toArray(),
                    ];
                })->values()->toArray(),
            ];
        }

        return array_merge(parent::share($request), $sharedData);
    }
}
