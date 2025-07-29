<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\Activity;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class FrontendController extends Controller
{
    public function services(Request $request)
    {
        $search = $request->input('search', '');
        $department = $request->input('department', '');
        $sortBy = $request->input('sort', 'name_asc');
        
        $query = Service::where('is_featured', true)
            ->with('departments')
            ->select('*');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($department) {
            $query->whereHas('departments', function ($query) use ($department) {
                $query->where('uuid', $department);
            });
        }
            
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                $sortLabel = 'A to Z';
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                $sortLabel = 'Z to A';
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                $sortLabel = 'Newest';
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                $sortLabel = 'Oldest';
                break;
            default:
                $query->orderBy('name', 'asc');
                $sortLabel = 'A to Z';
        }

        $services = $query->paginate(6);

        return Inertia::render('Frontend/Services/Index', [
            'services' => $services,
            'departments' => Department::where('is_featured', true)->get(),
            'sortOptions' => [
                ['value' => 'name_asc', 'label' => 'A to Z'],
                ['value' => 'name_desc', 'label' => 'Z to A'],
                ['value' => 'newest', 'label' => 'Newest First'],
                ['value' => 'oldest', 'label' => 'Oldest First'],
            ],
            'currentSort' => $sortBy,
            'currentSortLabel' => $sortLabel,
            'filters' => [
                'search' => $search,
                'department' => $department,
                'sort' => $sortBy,
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'),
            'title' => 'Our Services',
        ]);
    }

    public function departments(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort', 'name_asc');
        
        $query = Department::where('is_featured', true)
            ->select('*');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }
            
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                $sortLabel = 'A to Z';
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                $sortLabel = 'Z to A';
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                $sortLabel = 'Newest';
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                $sortLabel = 'Oldest';
                break;
            default:
                $query->orderBy('name', 'asc');
                $sortLabel = 'A to Z';
        }

        $departments = $query->paginate(6);

        return Inertia::render('Frontend/Departments/Index', [
            'departments' => $departments,
            'sortOptions' => [
                ['value' => 'name_asc', 'label' => 'A to Z'],
                ['value' => 'name_desc', 'label' => 'Z to A'],
                ['value' => 'newest', 'label' => 'Newest First'],
                ['value' => 'oldest', 'label' => 'Oldest First'],
            ],
            'currentSort' => $sortBy,
            'currentSortLabel' => $sortLabel,
            'filters' => [
                'search' => $search,
                'sort' => $sortBy,
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'),
            'title' => 'Our Departments',
        ]);
    }

    public function welcome(Request $request)
    {
        // Log the incoming request parameters
        Log::info('Welcome page requested', [
            'all_params' => $request->all(),
            'services_page' => $request->input('services_page'),
            'departments_page' => $request->input('departments_page')
        ]);

        // Get paginated services (6 per page)
        $services = Service::where('is_featured', true)
            ->with('departments')
            ->orderBy('name', 'asc')
            ->select('*')
            ->paginate(4, ['*'], 'services_page')
            ->appends($request->except('services_page', 'page'));

        // Get paginated departments (6 per page)
        $departments = Department::where('is_featured', true)
            ->orderBy('name', 'asc')
            ->select('*')
            ->paginate(4, ['*'], 'departments_page')
            ->appends($request->except('departments_page', 'page'));

        Log::info('Pagination data', [
            'services_current_page' => $services->currentPage(),
            'services_last_page' => $services->lastPage(),
            'departments_current_page' => $departments->currentPage(),
            'departments_last_page' => $departments->lastPage(),
        ]);

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'),
            'title' => "Welcome",
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'heroSlides' => [
                [
                    'file_path' => asset('assets/img/hero-home-1.jpg'),
                    'name' => 'Forward Kenya Party',
                    'description' => 'Championing progressive leadership and sustainable development for all Kenyans.',
                ],
                [
                    'file_path' => asset('assets/img/eCitizen-hero-banner-3.jpg'),
                    'name' => 'Our Lives, Our Heritage',
                    'description' => 'Safeguarding our collective future while honoring our rich heritage.',
                ],
                [
                    'file_path' => asset('assets/img/eCitizen-hero-banner-8.jpg'),
                    'name' => 'Inclusive Governance',
                    'description' => 'Empowering communities through transparent and accountable leadership.',
                ],
            ],
            'stats' => [
                'users' => User::count(),
                'services' => Service::count(),
                'departments' => Department::count(),
                'activities' => Activity::count(),
            ],
            'initialServices' => $services,
            'initialDepartments' => $departments,
        ]);
    }

    public function aboutUs()
    {
        return Inertia::render('Frontend/AboutUs', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'), // asset('assets/img/logo.svg'),
            'title' => "About Us",
        ]);
    }

    public function contactUs()
    {
        return Inertia::render('Frontend/ContactUs', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'), // asset('assets/img/logo.svg'),
            'title' => "Contact Us",
        ]);
    }

    public function helpSupport()
    {
        return Inertia::render('Frontend/HelpSupport', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'), // asset('assets/img/logo.svg'),
            'title' => "Help and Support",
        ]);
    }

    public function frequentlyAskedQuestions()
    {
        return Inertia::render('Frontend/FrequentlyAskedQuestions', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'), // asset('assets/img/logo.svg'),
            'title' => "Frequently Asked Questions",
        ]);
    }

    public function termsConditions()
    {
        return Inertia::render('Frontend/TermsConditions', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'), // asset('assets/img/logo.svg'),
            'title' => "Terms and Conditions",
        ]);
    }

    public function privacyPolicy()
    {
        return Inertia::render('Frontend/PrivacyPolicy', [
            'title' => 'Privacy Policy',
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'),
        ]);
    }

    /**
     * Show a specific service
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function showService($id)
    {
        $service = Service::with([
                'department',
                'requirements',
                'type',
                'category',
                'department.type',
                'department.category'
            ])
            ->where('is_featured', true)
            ->findOrFail($id);

        // Get related services from the same department and category
        $relatedServices = Service::where('id', '!=', $id)
            ->where('is_featured', true)
            ->where(function($query) use ($service) {
                $query->where('department_id', $service->department_id)
                      ->orWhere('category_id', $service->category_id);
            })
            ->with(['department', 'category'])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // If not enough related services, get any visible services
        if ($relatedServices->count() < 3) {
            $additionalServices = Service::where('id', '!=', $id)
                ->where('is_featured', true)
                ->whereNotIn('id', $relatedServices->pluck('id'))
                ->with(['department', 'category'])
                ->inRandomOrder()
                ->limit(3 - $relatedServices->count())
                ->get();
            
            $relatedServices = $relatedServices->merge($additionalServices);
        }

        return Inertia::render('Frontend/Services/Show', [
            'title' => $service->name,
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'),
            'service' => $service->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
                '_status',
                'configuration',
                'type_id',
                'category_id',
                'department_id',
                'slug',
                'uuid',
            ]),
            'relatedServices' => $relatedServices->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
                '_status',
                'configuration',
                'type_id',
                'category_id',
                'department_id',
                'slug',
                'uuid',
            ]),
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('frontend.welcome')],
                ['label' => 'Services', 'url' => route('frontend.services')],
                ['label' => $service->name, 'url' => '']
            ]
        ]);
    }

    /**
     * Show a specific department
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function showDepartment($id)
    {
        // Eager load relationships with pagination for services
        $department = Department::with([
            'type',
            'category',
            'services' => function($query) {
                return $query->where('is_featured', true)
                    ->with(['type', 'category'])
                    ->orderBy('name', 'asc')
                    ->paginate(6, ['*'], 'services_page');
            },
            'staff' => function($query) {
                return $query->orderBy('name', 'asc');
            }
        ])->findOrFail($id);

        // Get related departments (same type or category)
        $relatedDepartments = Department::where('id', '!=', $id)
            ->where('is_featured', true)
            ->where(function($query) use ($department) {
                if ($department->type_id) {
                    $query->where('type_id', $department->type_id);
                }
                if ($department->category_id) {
                    $query->orWhere('category_id', $department->category_id);
                }
            })
            ->with(['type', 'category'])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // If not enough related departments, get any visible departments
        if ($relatedDepartments->count() < 3) {
            $additionalDepartments = Department::where('id', '!=', $id)
                ->where('is_featured', true)
                ->whereNotIn('id', $relatedDepartments->pluck('id'))
                ->with(['type', 'category'])
                ->inRandomOrder()
                ->limit(3 - $relatedDepartments->count())
                ->get();
            
            $relatedDepartments = $relatedDepartments->merge($additionalDepartments);
        }

        // Get the paginated services separately to avoid serialization issues
        $services = $department->services()->where('is_featured', true)
            ->with(['type', 'category'])
            ->orderBy('name', 'asc')
            ->paginate(6, ['*'], 'services_page')
            ->appends(request()->except('services_page'));

        return Inertia::render('Frontend/Departments/Show', [
            'title' => $department->name,
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'),
            'department' => $department->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
                '_status',
                'configuration',
                'type_id',
                'category_id',
                'slug',
                'uuid',
            ]),
            'services' => $services,
            'relatedDepartments' => $relatedDepartments->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
                '_status',
                'configuration',
                'type_id',
                'category_id',
                'slug',
                'uuid',
            ]),
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('frontend.welcome')],
                ['label' => 'Departments', 'url' => route('frontend.departments')],
                ['label' => $department->name, 'url' => '']
            ]
        ]);
    }

    public function store(Request $request)
    {
        // 
    }
}
