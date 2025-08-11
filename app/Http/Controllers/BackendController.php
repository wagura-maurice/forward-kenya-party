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
            return 'fa-solid fa-plus';
        } elseif (str_contains(strtolower($description), 'updated')) {
            return 'fa-solid fa-pen';
        } elseif (str_contains(strtolower($description), 'deleted')) {
            return 'fa-solid fa-trash';
        } elseif (str_contains(strtolower($description), 'login')) {
            return 'fa-solid fa-sign-in-alt';
        } elseif (str_contains(strtolower($description), 'logout')) {
            return 'fa-solid fa-sign-out-alt';
        } elseif (str_contains(strtolower($description), 'registered')) {
            return 'fa-solid fa-user-plus';
        } else {
            return 'fa-solid fa-bell';
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
    
    /**
     * Get the status text for an activity status code
     *
     * @param int $status
     * @return string
     */
    protected function getStatusText($status)
    {
        return match ((int)$status) {
            0 => 'Pending',
            1 => 'Completed',
            2 => 'Failed',
            3 => 'In Progress',
            default => 'Unknown'
        };
    }
    
    /**
     * Get the CSS class for an activity status
     *
     * @param int $status
     * @return string
     */
    protected function getStatusClass($status)
    {
        return match ((int)$status) {
            0 => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            1 => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            2 => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            3 => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
        };
    }
    
    public function dashboard(Request $request)
    {
        $user = $request->user()->load('roles');
        $roles = $user->roles->sortByDesc('id')->pluck('name')->toArray();

        // Get recent activities with relationships
        $activities = Activity::with(['user', 'service', 'department'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'user_id' => $activity->user ? $activity->user->id : NULL,
                    'user_name' => $activity->user ? $activity->user->name : 'System',
                    'user_avatar' => $activity->user ? $activity->user->profile_photo_path : null,
                    'title' => $activity->title,
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'details' => $activity->details,
                    'status' => $this->getStatusText($activity->_status),
                    'status_class' => $this->getStatusClass($activity->_status),
                    'created_at' => $activity->created_at->diffForHumans(),
                    'started_at' => $activity->started_at?->format('M d, Y H:i'),
                    'completed_at' => $activity->completed_at?->format('M d, Y H:i'),
                    'scheduled_for' => $activity->scheduled_for?->format('M d, Y H:i'),
                    'service_name' => $activity->service?->name,
                    'department_name' => $activity->department?->name,
                    'icon' => $this->getActivityIcon($activity->action),
                    'color' => $this->getActivityColor($activity->action)
                ];
            });

        // Get statistics with percentage changes
        $oneMonthAgo = now()->subMonth();
        
        // Helper function to calculate percentage change
        $calculateChange = function($current, $previous) {
            if ($previous === 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / $previous) * 100, 1);
        };
        
        // Calculate date ranges for 30-day comparison
        $thirtyDaysAgo = now()->subDays(30);
        $sixtyDaysAgo = now()->subDays(60);
        
        // User statistics
        $currentUsers = User::count();
        $previousPeriodUsers = User::where('created_at', '<', $thirtyDaysAgo)
            ->where('created_at', '>=', $sixtyDaysAgo)
            ->count();
        
        // Active users (logged in within last 30 days)
        $activeUsers = User::where('last_login_at', '>=', $thirtyDaysAgo)->count();
        $previousActiveUsers = User::where('last_login_at', '>=', $sixtyDaysAgo)
            ->where('last_login_at', '<', $thirtyDaysAgo)
            ->count();
        
        // Other statistics with 30-day comparison
        $currentServices = Service::count();
        $previousServices = Service::where('created_at', '<', $thirtyDaysAgo)
            ->where('created_at', '>=', $sixtyDaysAgo)
            ->count();
        
        $currentDepartments = Department::count();
        $previousDepartments = Department::where('created_at', '<', $thirtyDaysAgo)
            ->where('created_at', '>=', $sixtyDaysAgo)
            ->count();
        
        // Get featured projects (using activities as a placeholder for now)
        $featuredProjects = Activity::where('_status', 1) // Assuming 1 is the status for active/approved
            ->latest()
            ->get();
            
        $currentProjects = $featuredProjects->count();
        $previousProjects = Activity::where('created_at', '<', $thirtyDaysAgo)
            ->where('created_at', '>=', $sixtyDaysAgo)
            ->count();
        
        // Financial metrics
        // Membership Collections (one-time onboarding fees)
        $currentMembershipCollections = 0; // TODO: Implement actual query
        $previousMembershipCollections = 0; // TODO: Implement actual query
        
        // Monthly Contributions (recurring membership fees)
        $currentMonthlyContributions = 0; // TODO: Implement actual query
        $previousMonthlyContributions = 0; // TODO: Implement actual query
        $currentMembers = $currentUsers; // Using users as members for now
        $previousMembers = $previousPeriodUsers;
        $currentCandidates = 0; // Implement actual query
        $previousCandidates = 0;
        $currentComplianceItems = 0; // Implement actual query
        $previousComplianceItems = 0;
        
        // New member stats with 30-day comparison
        $newMembersThisMonth = User::where('created_at', '>=', $thirtyDaysAgo)->count();
        $previousMonthMembers = User::where('created_at', '<', $thirtyDaysAgo)
            ->where('created_at', '>=', $sixtyDaysAgo)
            ->count();
            
        // Calculate engagement rate (example: % of users who logged in this month)
        $totalUsers = max(1, $currentUsers); // Avoid division by zero
        $engagedUsers = User::where('last_login_at', '>=', $thirtyDaysAgo)->count();
        $previousEngaged = User::where('last_login_at', '>=', $sixtyDaysAgo)
            ->where('last_login_at', '<', $thirtyDaysAgo)
            ->count();
            
        $engagementRate = round(($engagedUsers / $totalUsers) * 100);
        $previousEngagementRate = $previousEngaged > 0 ? round(($previousEngaged / max(1, $previousPeriodUsers)) * 100) : 0;
        
        $stats = [
            // Membership & Users
            'total_members' => [
                'count' => $currentMembers,
                'change' => $calculateChange($currentMembers, $previousMembers),
                'previous_period' => $previousMembers,
                'percentage_change' => $previousMembers > 0 ? 
                    round((($currentMembers - $previousMembers) / $previousMembers) * 100, 1) : 
                    ($currentMembers > 0 ? 100 : 0)
            ],
            'active_users' => [
                'count' => $activeUsers,
                'change' => $calculateChange($activeUsers, $previousActiveUsers),
                'previous_period' => $previousActiveUsers,
                'percentage_change' => $previousActiveUsers > 0 ? 
                    round((($activeUsers - $previousActiveUsers) / $previousActiveUsers) * 100, 1) : 
                    ($activeUsers > 0 ? 100 : 0)
            ],
            'new_members_this_month' => [
                'count' => $newMembersThisMonth,
                'change' => $calculateChange($newMembersThisMonth, $previousMonthMembers),
                'previous_period' => $previousMonthMembers,
                'percentage_change' => $previousMonthMembers > 0 ? 
                    round((($newMembersThisMonth - $previousMonthMembers) / $previousMonthMembers) * 100, 1) : 
                    ($newMembersThisMonth > 0 ? 100 : 0)
            ],
            'engagement_rate' => [
                'count' => $engagementRate,
                'change' => $calculateChange($engagementRate, $previousEngagementRate),
                'previous_period' => $previousEngagementRate,
                'percentage_change' => $previousEngagementRate > 0 ? 
                    round((($engagementRate - $previousEngagementRate) / $previousEngagementRate) * 100, 1) : 
                    ($engagementRate > 0 ? 100 : 0)
            ],
            
            // Financials
            'donations' => [
                'count' => 0, // Kept for backward compatibility
                'change' => 0,
                'previous_period' => 0,
                'percentage_change' => 0
            ],
            'monthly_subscriptions' => [
                'count' => $currentMonthlyContributions,
                'change' => $calculateChange($currentMonthlyContributions, $previousMonthlyContributions),
                'previous_period' => $previousMonthlyContributions,
                'percentage_change' => $previousMonthlyContributions > 0 ?
                    round((($currentMonthlyContributions - $previousMonthlyContributions) / $previousMonthlyContributions) * 100, 1) :
                    ($currentMonthlyContributions > 0 ? 100 : 0)
            ],
            'membership_fees' => [
                'count' => $currentMembershipCollections,
                'change' => $calculateChange($currentMembershipCollections, $previousMembershipCollections),
                'previous_period' => $previousMembershipCollections,
                'percentage_change' => $previousMembershipCollections > 0 ?
                    round((($currentMembershipCollections - $previousMembershipCollections) / $previousMembershipCollections) * 100, 1) :
                    ($currentMembershipCollections > 0 ? 100 : 0)
            ],
            'pending_approvals' => [
                'count' => 0, // Implement actual query
                'change' => 0,
                'previous_period' => 0,
                'percentage_change' => 0
            ],
            
            // Election & Compliance
            'candidates' => [
                'count' => $currentCandidates,
                'change' => $calculateChange($currentCandidates, $previousCandidates),
                'previous_period' => $previousCandidates,
                'percentage_change' => $previousCandidates > 0 ? 
                    round((($currentCandidates - $previousCandidates) / $previousCandidates) * 100, 1) : 
                    ($currentCandidates > 0 ? 100 : 0)
            ],
            'nomination_papers' => [
                'count' => 0, // Implement actual query
                'change' => 0,
                'previous_period' => 0,
                'percentage_change' => 0
            ],
            'compliance_items' => [
                'count' => $currentComplianceItems,
                'change' => $calculateChange($currentComplianceItems, $previousComplianceItems),
                'previous_period' => $previousComplianceItems,
                'percentage_change' => $previousComplianceItems > 0 ? 
                    round((($currentComplianceItems - $previousComplianceItems) / $previousComplianceItems) * 100, 1) : 
                    ($currentComplianceItems > 0 ? 100 : 0)
            ],
            'deadlines' => [
                'count' => 0, // Implement actual query
                'change' => 0,
                'previous_period' => 0,
                'percentage_change' => 0
            ],
            
            // Additional Stats
            'departments' => [
                'count' => $currentDepartments,
                'change' => $calculateChange($currentDepartments, $previousDepartments),
                'previous_period' => $previousDepartments,
                'percentage_change' => $previousDepartments > 0 ? 
                    round((($currentDepartments - $previousDepartments) / $previousDepartments) * 100, 1) : 
                    ($currentDepartments > 0 ? 100 : 0)
            ],
            'projects' => [
                'count' => $currentProjects,
                'change' => $calculateChange($currentProjects, $previousProjects),
                'previous_period' => $previousProjects,
                'percentage_change' => $previousProjects > 0 ? 
                    round((($currentProjects - $previousProjects) / $previousProjects) * 100, 1) : 
                    ($currentProjects > 0 ? 100 : 0)
            ],
            'services' => [
                'count' => $currentServices,
                'change' => $calculateChange($currentServices, $previousServices),
                'previous_period' => $previousServices,
                'percentage_change' => $previousServices > 0 ? 
                    round((($currentServices - $previousServices) / $previousServices) * 100, 1) : 
                    ($currentServices > 0 ? 100 : 0)
            ],
            
            // Media & Communications
            'press_releases' => [
                'count' => 0, // Implement actual query
                'change' => 0
            ],
            'social_media_reach' => [
                'count' => 0, // Implement actual query
                'change' => 0
            ],
            
            // Events & Meetings
            'upcoming_events' => [
                'count' => 0, // To be implemented
                'change' => 0
            ],
            'meetings_this_week' => [
                'count' => 0, // Implement actual query
                'change' => 0
            ],
            
            // Volunteers
            'active_volunteers' => [
                'count' => 0, // Implement actual query
                'change' => 0
            ],
            'volunteer_hours' => [
                'count' => 0, // Implement actual query
                'change' => 0
            ],
            
            // Special Interest Groups
            'youth_members' => [
                'count' => 0, // Implement actual query
                'change' => 0
            ],
            'women_members' => [
                'count' => 0, // Implement actual query
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
                'activities' => $activities->toArray(),
                'featuredServices' => $featuredServices,
                'featuredDepartments' => $featuredDepartments,
                'featuredProjects' => $featuredProjects,
                'roles' => $roles,
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
                'phpVersion' => PHP_VERSION,
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
                            'subCounty',
                            'constituency',
                            'ward',
                            'location',
                            'village',
                            'pollingCenter',
                            'pollingStation',
                            'pollingStream',
                            'consulate',
                            'refugeeCenter'
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
            'profile_photo_path' => $user->profile_photo_path,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'profile' => null
        ];

        // dd($userData);

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
                'uuid' => $user->profile->citizen->uuid,
                'national_identification_number' => $user->profile->citizen->national_identification_number,
                'passport_number' => $user->profile->citizen->passport_number,
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
                'location' => $user->profile->citizen->location ? [
                    'id' => $user->profile->citizen->location->id,
                    'name' => $user->profile->citizen->location->name
                ] : null,
                'village' => $user->profile->citizen->village ? [
                    'id' => $user->profile->citizen->village->id,
                    'name' => $user->profile->citizen->village->name
                ] : null,
                'polling_center' => $user->profile->citizen->pollingCenter ? [
                    'id' => $user->profile->citizen->pollingCenter->id,
                    'name' => $user->profile->citizen->pollingCenter->name
                ] : null,
                'polling_station' => $user->profile->citizen->pollingStation ? [
                    'id' => $user->profile->citizen->pollingStation->id,
                    'name' => $user->profile->citizen->pollingStation->name
                ] : null,
                'polling_stream' => $user->profile->citizen->pollingStream ? [
                    'id' => $user->profile->citizen->pollingStream->id,
                    'name' => $user->profile->citizen->pollingStream->name
                ] : null,
                'consulate' => $user->profile->citizen->consulate ? [
                    'id' => $user->profile->citizen->consulate->id,
                    'name' => $user->profile->citizen->consulate->name
                ] : null,
                'refugee_center' => $user->profile->citizen->refugeeCenter ? [
                    'id' => $user->profile->citizen->refugeeCenter->id,
                    'name' => $user->profile->citizen->refugeeCenter->name
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
        // Get the user with their profile and relationships
        $user = User::findOrFail($user_id);
        $user->load([
            'profile' => function($query) {
                $query->with([
                    'religion',
                    'ethnicity',
                    'citizen' => function($query) {
                        $query->with([
                            'county',
                            'subCounty',
                            'constituency',
                            'ward',
                            'location',
                            'village',
                            'pollingCenter',
                            'pollingStation',
                            'pollingStream',
                            'consulate',
                            'refugeeCenter'
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
            'profile_photo_path' => $user->profile_photo_path,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
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
                'uuid' => $user->profile->citizen->uuid,
                'national_identification_number' => $user->profile->citizen->national_identification_number,
                'passport_number' => $user->profile->citizen->passport_number,
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
                'location' => $user->profile->citizen->location ? [
                    'id' => $user->profile->citizen->location->id,
                    'name' => $user->profile->citizen->location->name
                ] : null,
                'village' => $user->profile->citizen->village ? [
                    'id' => $user->profile->citizen->village->id,
                    'name' => $user->profile->citizen->village->name
                ] : null,
                'polling_center' => $user->profile->citizen->pollingCenter ? [
                    'id' => $user->profile->citizen->pollingCenter->id,
                    'name' => $user->profile->citizen->pollingCenter->name
                ] : null,
                'polling_station' => $user->profile->citizen->pollingStation ? [
                    'id' => $user->profile->citizen->pollingStation->id,
                    'name' => $user->profile->citizen->pollingStation->name
                ] : null,
                'polling_stream' => $user->profile->citizen->pollingStream ? [
                    'id' => $user->profile->citizen->pollingStream->id,
                    'name' => $user->profile->citizen->pollingStream->name
                ] : null,
                'consulate' => $user->profile->citizen->consulate ? [
                    'id' => $user->profile->citizen->consulate->id,
                    'name' => $user->profile->citizen->consulate->name
                ] : null,
                'refugee_center' => $user->profile->citizen->refugeeCenter ? [
                    'id' => $user->profile->citizen->refugeeCenter->id,
                    'name' => $user->profile->citizen->refugeeCenter->name
                ] : null,
            ] : null;

            $userData['profile'] = $profile;
        }

        return Inertia::render('Profile/View', [
            'title' => 'View Profile',
            'breadcrumbs' => [
                [
                    'label' => 'Profile',
                    'url' => route('profile')
                ]
            ],
                'data' => [
                'user' => $userData
            ],
        ]);
    }

    public function activity(Request $request)
    {
        // Get pagination parameters
        $perPage = $request->query('per_page', 5);
        $page = $request->query('page', 1);
        $search = $request->query('search');
        
        // Base query
        $query = Activity::query()
            ->with(['user', 'service', 'department'])
            ->latest();
        
        // Apply search filter if search term exists
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Get paginated results
        $activities = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the paginated collection
        $transformedActivities = $activities->getCollection()->map(function ($activity) {
            return [
                'id' => $activity->id,
                'user_id' => $activity->user ? $activity->user->id : null,
                'user_name' => $activity->user ? $activity->user->name : 'System',
                'user_avatar' => $activity->user ? $activity->user->profile_photo_path : null,
                'title' => $activity->title,
                'action' => $activity->action,
                'description' => $activity->description,
                'details' => $activity->details,
                'status' => $this->getStatusText($activity->_status),
                'status_class' => $this->getStatusClass($activity->_status),
                'created_at' => $activity->created_at->diffForHumans(),
                'started_at' => $activity->started_at?->format('M d, Y H:i'),
                'completed_at' => $activity->completed_at?->format('M d, Y H:i'),
                'scheduled_for' => $activity->scheduled_for?->format('M d, Y H:i'),
                'service_name' => $activity->service?->name,
                'department_name' => $activity->department?->name,
                'icon' => $this->getActivityIcon($activity->action),
                'color' => $this->getActivityColor($activity->action)
            ];
        });

        // Create a new paginator with the transformed collection
        $paginatedResponse = new \Illuminate\Pagination\LengthAwarePaginator(
            $transformedActivities,
            $activities->total(),
            $activities->perPage(),
            $activities->currentPage(),
            ['path' => \Illuminate\Support\Facades\URL::current()]
        );
        
        return Inertia::render('Activity', [
            'title' => 'Activity',
            'breadcrumbs' => [
                [
                    'label' => 'Activity',
                    'url' => route('activity')
                ]
            ],
            'data' => [
                'user' => $request->user(),
                'activities' => $paginatedResponse
            ]
        ]);
    }

    public function deleteActivity(Request $request, $id)
    {
        $activity = Activity::find($id);
        if ($activity) {
            $activity->delete();
            return redirect()->route('activity')->with('success', 'Activity deleted successfully.');
        }
        return redirect()->route('activity')->with('error', 'Activity not found.');
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
