<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\User;
use App\Models\Religion;
use App\Models\Ethnicity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BackendController extends Controller
{
    /**
     * Display the backend dashboard.
     */
    public function dashboard()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Get user roles to determine dashboard type
        $roles = $user->roles->pluck('name')->toArray();

        // Get counts for dashboard statistics
        $totalUsers = \App\Models\User::count();
        $totalMembers = \App\Models\Member::count();
        $totalRoles = \App\Models\Role::count();
        $totalAbilities = \App\Models\Ability::count();

        // Get recent activities (you can customize this as needed)
        $recentActivities = collect([
            (object) [
                'title' => 'New member registration',
                'description' => 'John Doe registered as a new member',
                'time' => '2 hours ago',
                'icon' => 'user-plus'
            ],
            (object) [
                'title' => 'Profile update',
                'description' => 'Jane Smith updated her profile information',
                'time' => '5 hours ago',
                'icon' => 'user-edit'
            ],
        ]);

        return Inertia::render('Dashboard', [
            'title' => ucwords($roles[0] == 'citizen' ? 'Member' : $roles[0]) . ' Dashboard',
            'breadcrumbs' => [
                [
                    'label' => 'Dashboard',
                    'active' => true
                ]
            ],
            'user' => $user,
            'totalUsers' => $totalUsers,
            'totalMembers' => $totalMembers,
            'totalRoles' => $totalRoles,
            'totalAbilities' => $totalAbilities  ,
            'recentActivities' => $recentActivities,
            'locations' => $this->getLocations(),
            'religions' => $this->getReligions(),
            'ethnicities' => $this->getEthnicities(),
        ]);
    }

    /**
     * Get locations data for dropdowns
     */
    public function getLocations()
    {
        $counties = \App\Models\County::orderBy('name')->get(['id', 'name']);
        $subCounties = \App\Models\SubCounty::orderBy('name')->get(['id', 'name']);
        $constituencies = \App\Models\Constituency::orderBy('name')->get(['id', 'name']);
        $wards = \App\Models\Ward::orderBy('name')->get(['id', 'name']);
        $locations = \App\Models\Location::orderBy('name')->get(['id', 'name']);
        $villages = \App\Models\Village::orderBy('name')->get(['id', 'name']);
        $pollingCenters = \App\Models\PollingCenter::orderBy('name')->get(['id', 'name']);
        $pollingStations = \App\Models\PollingStation::orderBy('name')->get(['id', 'name']);
        $pollingStreams = \App\Models\PollingStream::orderBy('name')->get(['id', 'name']);

        return [
            'counties' => $counties,
            'sub_counties' => $subCounties,
            'constituencies' => $constituencies,
            'wards' => $wards,
            'locations' => $locations,
            'villages' => $villages,
            'polling_centers' => $pollingCenters,
            'polling_stations' => $pollingStations,
            'polling_streams' => $pollingStreams,
        ];
    }

    /**
     * Get religions data for dropdowns
     */
    public function getReligions()
    {
        $religions = \App\Models\Religion::orderBy('name')->get(['id', 'name']);
        return $religions;
    }

    /**
     * Get ethnicities data for dropdowns
     */
    public function getEthnicities()
    {
        $ethnicities = \App\Models\Ethnicity::orderBy('name')->get(['id', 'name']);
        return $ethnicities;
    }

    /**
     * Display the user profile with all related data.
     */
    public function profile(Request $request)
    {
        // Get the authenticated user with their profile and relationships
        $user = $request->user()->load([
            'profile' => function($query) {
                $query->with([
                    'ethnicity',
                    'member' => function($query) {
                        $query->with([
                            'religion',
                            'county',
                            'subCounty',
                            'constituency',
                            'ward',
                            'location',
                            'village',
                            'pollingCenter',
                            'pollingStation',
                            'pollingStream',
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
            
            $profile['religion'] = $user->member->religion ? [
                'id' => $user->member->religion->id,
                'name' => $user->member->religion->name
            ] : null;

            $profile['ethnicity'] = $user->member->ethnicity ? [
                'id' => $user->member->ethnicity->id,
                'name' => $user->member->ethnicity->name
            ] : null;

            $profile['member'] = $user->profile->member ? [
                'uuid' => $user->profile->member->uuid,
                'national_identification_number' => $user->profile->member->national_identification_number,
                'passport_number' => $user->profile->member->passport_number,
                'county' => $user->profile->member->county ? [
                    'id' => $user->profile->member->county->id,
                    'name' => $user->profile->member->county->name
                ] : ['id' => null, 'name' => 'Not Provided'],
                'sub_county' => $user->profile->member->subCounty ? [
                    'id' => $user->profile->member->subCounty->id,
                    'name' => $user->profile->member->subCounty->name
                ] : ['id' => null, 'name' => 'Not Provided'],
                'constituency' => $user->profile->member->constituency ? [
                    'id' => $user->profile->member->constituency->id,
                    'name' => $user->profile->member->constituency->name
                ] : ['id' => null, 'name' => 'Not Provided'],
                'ward' => $user->profile->member->ward ? [
                    'id' => $user->profile->member->ward->id,
                    'name' => $user->profile->member->ward->name
                ] : ['id' => null, 'name' => 'Not Provided'],
                'location' => $user->profile->member->location ? [
                    'id' => $user->profile->member->location->id,
                    'name' => $user->profile->member->location->name
                ] : ['id' => null, 'name' => 'Not Provided'],
                'village' => $user->profile->member->village ? [
                    'id' => $user->profile->member->village->id,
                    'name' => $user->profile->member->village->name
                ] : ['id' => null, 'name' => 'Not Provided'],
                'polling_center' => $user->profile->member->pollingCenter ? [
                    'id' => $user->profile->member->pollingCenter->id,
                    'name' => $user->profile->member->pollingCenter->name
                ] : ['id' => null, 'name' => 'Not Provided'],
                'polling_station' => $user->profile->member->pollingStation ? [
                    'id' => $user->profile->member->pollingStation->id,
                    'name' => $user->profile->member->pollingStation->name
                ] : ['id' => null, 'name' => 'Not Provided'],
                'polling_stream' => $user->profile->member->pollingStream ? [
                    'id' => $user->profile->member->pollingStream->id,
                    'name' => $user->profile->member->pollingStream->name
                 ] : ['id' => null, 'name' => 'Not Provided'],
                'disability_status' => $user->profile->member->disability_status,
                'party_membership_id' => $user->profile->member->party_membership_id
                 ] : null;
            
            $userData['profile'] = $profile;
        }

        return Inertia::render('Profile/View', [
            'title' => 'View Profile',
            'breadcrumbs' => [
                [
                    'label' => 'Dashboard',
                    'active' => false
                ],
                [
                    'label' => 'View Profile',
                    'active' => true
                ]
            ],
            'user' => $userData,
            'locations' => $this->getLocations(),
            'religions' => $this->getReligions(),
            'ethnicities' => $this->getEthnicities(),
        ]);
    }
}
