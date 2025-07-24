<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\Activity;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class BackendController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get the authenticated user with their profile and relationships
        $user = $request->user()->load(['profile' => function($query) {
            $query->with([
                'religion',
                'ethnicity',
                'citizen.county',
                'citizen.subCounty',
                'citizen.constituency',
                'citizen.ward'
            ]);
        }]);

        // Prepare the user data for the frontend
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'profile' => null
        ];

        if ($user->profile) {
            $userData['profile'] = [
                'uuid' => $user->profile->uuid,
                'first_name' => $user->profile->first_name,
                'middle_name' => $user->profile->middle_name,
                'last_name' => $user->profile->last_name,
                'gender' => $user->profile->gender,
                'date_of_birth' => $user->profile->date_of_birth,
                'telephone' => $user->profile->telephone,
                'disability_status' => $user->profile->disability_status,
                'ncpwd_number' => $user->profile->ncpwd_number,
                'address_line_1' => $user->profile->address_line_1,
                'address_line_2' => $user->profile->address_line_2,
                'city' => $user->profile->city,
                'state' => $user->profile->state,
                'postal_code' => $user->profile->postal_code,
                'religion' => $user->profile->religion ? [
                    'id' => $user->profile->religion->id,
                    'name' => $user->profile->religion->name
                ] : null,
                'ethnicity' => $user->profile->ethnicity ? [
                    'id' => $user->profile->ethnicity->id,
                    'name' => $user->profile->ethnicity->name
                ] : null,
                'national_identification_number' => $user->profile->citizen->national_identification_number,
                'county' => $user->profile->citizen->county ? [
                    'id' => $user->profile->citizen->county->id,
                    'name' => $user->profile->citizen->county->name
                ] : null,
                'sub_county' => $user->profile->citizen->subCounty ? [
                    'id' => $user->profile->citizen->subCounty->id,
                    'name' => $user->profile->citizen->subCounty->name
                ] : null,
                'constituency' => $user->profile->citizen->constituency ? [
                    'id' => $user->profile->citizen->constituency->id,
                    'name' => $user->profile->citizen->constituency->name
                ] : null,
                'ward' => $user->profile->citizen->ward ? [
                    'id' => $user->profile->citizen->ward->id,
                    'name' => $user->profile->citizen->ward->name
                ] : null,
                'created_at' => $user->profile->created_at,
                'updated_at' => $user->profile->updated_at
            ];
        }

        // Get recent activities
        $recentActivities = Activity::latest()
            ->take(5)
            ->get();

        // Get statistics
        $stats = [
            'total_users' => User::count(),
            'total_services' => Service::count(),
            'total_departments' => Department::count(),
            'recent_activities' => $recentActivities->count(),
        ];

        // Get featured services and departments
        $featuredServices = Service::where('is_featured', true)
            ->with('departments')
            ->orderBy('name')
            ->take(4)
            ->get();

        $featuredDepartments = Department::where('is_featured', true)
            ->withCount('services')
            ->orderBy('name')
            ->take(4)
            ->get();

        return Inertia::render('Dashboard', [
            'title' => 'Dashboard',
            'auth' => [
                'user' => $userData
            ],
            'stats' => $stats,
            'featuredServices' => $featuredServices,
            'featuredDepartments' => $featuredDepartments,
            'recentActivities' => $recentActivities,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')]
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
