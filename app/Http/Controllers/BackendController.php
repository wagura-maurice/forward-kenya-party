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
    /**
     * Get the appropriate icon for an activity
     *
     * @param string $description
     * @return string
     */
    protected function getActivityIcon($description)
    {
        if (str_contains(strtolower($description), 'created')) {
            return 'plus';
        } elseif (str_contains(strtolower($description), 'updated')) {
            return 'pencil-alt';
        } elseif (str_contains(strtolower($description), 'deleted')) {
            return 'trash';
        } elseif (str_contains(strtolower($description), 'login')) {
            return 'sign-in-alt';
        } elseif (str_contains(strtolower($description), 'logout')) {
            return 'sign-out-alt';
        } elseif (str_contains(strtolower($description), 'registered')) {
            return 'user-plus';
        } else {
            return 'bell';
        }
    }

    /**
     * Get the appropriate color for an activity
     *
     * @param string $description
     * @return string
     */
    protected function getActivityColor($description)
    {
        if (str_contains(strtolower($description), 'created') || str_contains(strtolower($description), 'registered')) {
            return 'green';
        } elseif (str_contains(strtolower($description), 'updated')) {
            return 'blue';
        } elseif (str_contains(strtolower($description), 'deleted')) {
            return 'red';
        } elseif (str_contains(strtolower($description), 'login') || str_contains(strtolower($description), 'logout')) {
            return 'indigo';
        } else {
            return 'gray';
        }
    }
    
    public function dashboard(Request $request)
    {
        $user = $request->user()->load('roles');
        $roles = $user->roles->sortByDesc('id')->pluck('name')->toArray();

        // Get recent activities
        $featuredProjects = Activity::latest()
            ->take(5)
            ->get();

        // Get statistics with percentage changes
        $oneMonthAgo = now()->subMonth();
        
        // Helper function to calculate percentage change
        $calculateChange = function($current, $previous) {
            if ($previous === 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / $previous) * 100, 1);
        };
        
        // Get user statistics
        $currentUsers = User::count();
        $previousUsers = User::where('created_at', '<', $oneMonthAgo)->count();
        
        // Get active users (assuming last login within last 30 days)
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(30))->count();
        $previousActiveUsers = User::where('last_login_at', '>=', $oneMonthAgo->copy()->subDays(30))
            ->where('last_login_at', '<', $oneMonthAgo)
            ->count();
        
        // Get other statistics
        $currentServices = Service::count();
        $previousServices = Service::where('created_at', '<', $oneMonthAgo)->count();
        
        $currentDepartments = Department::count();
        $previousDepartments = Department::where('created_at', '<', $oneMonthAgo)->count();
        
        $currentProjects = $featuredProjects->count();
        $previousProjects = Activity::where('created_at', '<', $oneMonthAgo)->count();
        
        $stats = [
            'total_users' => [
                'count' => $currentUsers,
                'change' => $calculateChange($currentUsers, $previousUsers)
            ],
            'active_users' => [
                'count' => $activeUsers,
                'change' => $calculateChange($activeUsers, $previousActiveUsers)
            ],
            'branches' => [
                'count' => 0, // To be implemented
                'change' => 0
            ],
            'partnerships' => [
                'count' => 0, // To be implemented
                'change' => 0
            ],
            'departments' => [
                'count' => $currentDepartments,
                'change' => $calculateChange($currentDepartments, $previousDepartments)
            ],
            'services' => [
                'count' => $currentServices,
                'change' => $calculateChange($currentServices, $previousServices)
            ],
            'projects' => [
                'count' => $currentProjects,
                'change' => $calculateChange($currentProjects, $previousProjects)
            ],
            'upcoming_events' => [
                'count' => 0, // To be implemented
                'change' => 0
            ]
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
            
        // Get latest activities
        $latestActivities = Activity::with(['user', 'subject'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'subject_type' => class_basename($activity->subject_type),
                    'subject_id' => $activity->subject_id,
                    'user_name' => $activity->user->name ?? 'System',
                    'user_avatar' => $activity->user->profile_photo_url ?? null,
                    'created_at' => $activity->created_at->diffForHumans(),
                    'icon' => $this->getActivityIcon($activity->description),
                    'color' => $this->getActivityColor($activity->description)
                ];
            });

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
                'latestActivities' => $latestActivities,
                'role' => $roles[0],
                'user' => $user
            ],
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
            'breadcrumbs' => [
                ['label' => 'Profile', 'url' => route('profile')]
            ],
            'data' => [
                'user' => $userData
            ],
        ]);
    }

    public function viewProfile(Request $request, $user_id)
    {
        return Inertia::render('Profile/View', [
            'title' => 'View Profile',
            'breadcrumbs' => [
                [
                    'label' => 'Profile',
                    'url' => route('profile')
                ]
            ],
                'data' => [
                'user' => User::find($user_id)
            ],
        ]);
    }

    public function settings(Request $request)
    {
        return Inertia::render('Settings', [
            'title' => 'Systems Settings',
            'breadcrumbs' => [
                [
                    'label' => 'Settings',
                    'url' => route('settings')
                ]
            ],
            'data' => [
                'user' => $request->user()
            ]
        ]);
    }
}
