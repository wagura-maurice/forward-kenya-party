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
        $user = $request->user()->load('roles');
        $roles = $user->roles->sortByDesc('id')->pluck('name')->toArray();

        // Get recent activities
        $featuredProjects = Activity::latest()
            ->take(5)
            ->get();

        // Get statistics
        $stats = [
            'total_users' => User::count(),
            'total_services' => Service::count(),
            'total_departments' => Department::count(),
            'featured_projects' => $featuredProjects->count(),
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
            'title' => ucwords($roles[0]) . ' Dashboard',
            'breadcrumbs' => [
                [
                    'label' => 'Dashboard',
                    'url' => route('dashboard')
                ]
            ],
            'data' => [
                'stats' => $stats,
                'featuredServices' => $featuredServices,
                'featuredDepartments' => $featuredDepartments,
                'featuredProjects' => $featuredProjects,
                'role' => $roles[0],
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function profile(Request $request)
    {
        // Get the authenticated user with their profile and relationships
        $user = $request->user()->load([
            'profile' => function($query) {
                $query->with([
                    'religion',
                    'ethnicity',
                    'citizen' => function($query) {
                        $query->with([
                            'county',
                            'sub_county',
                            'constituency',
                            'ward',
                            'location',
                            'village',
                            'polling_center',
                            'polling_station',
                            'polling_stream',
                            'consulate',
                            'refugee_center'
                        ]);
                    }
                ]);
            }
        ]);

        // Prepare the user data for the frontend
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'profile' => null
        ];

        if ($user->profile) {
            $profile = $user->profile->toArray();
            
            $profile['religion'] = $user->profile->religion ? [
                'id' => $user->profile->religion->id,
                'name' => $user->profile->religion->name
            ] : null;

            $profile['ethnicity'] = $user->profile->ethnicity ? [
                'id' => $user->profile->ethnicity->id,
                'name' => $user->profile->ethnicity->name
            ] : null;

            $profile['citizen'] = $user->profile->citizen ? [
                'national_identification_number' => $user->profile->citizen->national_identification_number,
                'county' => $user->profile->citizen->county ? [
                    'id' => $user->profile->citizen->county->id,
                    'name' => $user->profile->citizen->county->name
                ] : null,
                'sub_county' => $user->profile->citizen->sub_county ? [
                    'id' => $user->profile->citizen->sub_county->id,
                    'name' => $user->profile->citizen->sub_county->name
                ] : null,
                'constituency' => $user->profile->citizen->constituency ? [
                    'id' => $user->profile->citizen->constituency->id,
                    'name' => $user->profile->citizen->constituency->name
                ] : null,
                'ward' => $user->profile->citizen->ward ? [
                    'id' => $user->profile->citizen->ward->id,
                    'name' => $user->profile->citizen->ward->name
                ] : null,
                'location' => $user->profile->citizen->location ? [
                    'id' => $user->profile->citizen->location->id,
                    'name' => $user->profile->citizen->location->name
                ] : null,
                'village' => $user->profile->citizen->village ? [
                    'id' => $user->profile->citizen->village->id,
                    'name' => $user->profile->citizen->village->name
                ] : null,
                'polling_center' => $user->profile->citizen->polling_center ? [
                    'id' => $user->profile->citizen->polling_center->id,
                    'name' => $user->profile->citizen->polling_center->name
                ] : null,
                'polling_station' => $user->profile->citizen->polling_station ? [
                    'id' => $user->profile->citizen->polling_station->id,
                    'name' => $user->profile->citizen->polling_station->name
                ] : null,
                'polling_stream' => $user->profile->citizen->polling_stream ? [
                    'id' => $user->profile->citizen->polling_stream->id,
                    'name' => $user->profile->citizen->polling_stream->name
                ] : null,
                'consulate' => $user->profile->citizen->consulate ? [
                    'id' => $user->profile->citizen->consulate->id,
                    'name' => $user->profile->citizen->consulate->name
                ] : null,
                'refugee_center' => $user->profile->citizen->refugee_center ? [
                    'id' => $user->profile->citizen->refugee_center->id,
                    'name' => $user->profile->citizen->refugee_center->name
                ] : null,
            ] : null;

            $userData['profile'] = $profile;
        }

        return Inertia::render('Profile/Index', [
            'title' => 'My Profile',
            'data' => [
                'user' => $userData
            ],
            'breadcrumbs' => [
                ['label' => 'Profile', 'url' => route('profile')]
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function viewProfile(Request $request, $user_id)
    {
        return Inertia::render('Profile/View', [
            'title' => 'View Profile',
            'data' => [
                'user' => User::find($user_id)
            ],
            'breadcrumbs' => [
                [
                    'label' => 'Profile',
                    'url' => route('profile')
                ]
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function settings(Request $request)
    {
        return Inertia::render('Settings', [
            'title' => 'Systems Settings',
            'data' => [
                'user' => $request->user()
            ],
            'breadcrumbs' => [
                [
                    'label' => 'Settings',
                    'url' => route('settings')
                ]
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
