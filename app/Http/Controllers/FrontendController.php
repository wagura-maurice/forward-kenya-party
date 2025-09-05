<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\Citizen;
use App\Models\Activity;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
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
                    'file_path' => asset('assets/img/eCitizen-hero-banner-3.jpg'),
                    'name' => 'Forward Kenya Party',
                    'description' => 'Our Lives, Our Heritage.',
                ],
                // [
                //     'file_path' => asset('assets/img/hero-home-1.jpg'),
                //     'name' => 'Our Lives, Our Heritage',
                //     'description' => 'Safeguarding our collective future while honoring our rich heritage.',
                // ],
                // [
                //     'file_path' => asset('assets/img/eCitizen-hero-banner-8.jpg'),
                //     'name' => 'Inclusive Governance',
                //     'description' => 'Empowering communities through transparent and accountable leadership.',
                // ],
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
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'),
            'ideologyUrl' => route('frontend.party-ideology'),
            'manifestoUrl' => route('frontend.party-manifesto'),
            'constitutionUrl' => route('frontend.party-constitution'),
            'nominationRulesUrl' => route('frontend.party-nomination-rules'),
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
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'logoUrl' => asset('assets/FKP COLLATERALS/FKP PNG/Secondary logo/Asset 5FKP.png'), // asset('assets/img/logo.svg'),
            'title' => "Privacy Policy",
        ]);
    }

    /**
     * Display the party ideology PDF in the browser
     *
     * @return \Illuminate\Http\Response
     */
    public function viewPartyIdeology()
    {
        $filePath = public_path('assets/FKP_Documents/FKP IDEOLOGY.pdf');
        
        if (!file_exists($filePath)) {
            abort(404, 'The party ideology document could not be found.');
        }

        return $this->createPdfResponse($filePath, 'FKP Party Ideology');
    }

    /**
     * Display the party manifesto PDF in the browser
     *
     * @return \Illuminate\Http\Response
     */
    public function viewPartyManifesto()
    {
        $filePath = public_path('assets/FKP_Documents/FKP MANIFESTO (1).pdf');
        
        if (!file_exists($filePath)) {
            abort(404, 'The party manifesto document could not be found.');
        }

        return $this->createPdfResponse($filePath, 'FKP Party Manifesto');
    }

    /**
     * Display the party constitution PDF in the browser
     *
     * @return \Illuminate\Http\Response
     */
    public function viewPartyConstitution()
    {
        $filePath = public_path('assets/FKP_Documents/FORWARD KENYA PARTY CONSTITUTION.pdf');
        
        if (!file_exists($filePath)) {
            abort(404, 'The party constitution document could not be found.');
        }

        return $this->createPdfResponse($filePath, 'FKP Party Constitution');
    }

    /**
     * Display the party nomination rules PDF in the browser
     *
     * @return \Illuminate\Http\Response
     */
    public function viewPartyNominationRules()
    {
        $filePath = public_path('assets/FKP_Documents/NOMINATION_RULES_AMMENDED 12.5.25.pdf');
        
        if (!file_exists($filePath)) {
            abort(404, 'The party nomination rules document could not be found.');
        }

        return $this->createPdfResponse($filePath, 'FKP Party Nomination Rules');
    }

    /**
     * Create a PDF response
     *
     * @param string $filePath
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    private function createPdfResponse($filePath, $filename)
    {
        // Read the PDF file
        $fileContent = file_get_contents($filePath);
        
        // Create response with the PDF
        $response = Response::make($fileContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filename) . '"',
            'Cache-Control' => 'public, max-age=3600',
            'Pragma' => 'public',
            'Expires' => gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT',
            'Content-Length' => filesize($filePath),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'fullscreen=*, geolocation=()',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self';",
        ]);

        return $response;
    }

    /**
     * Show a specific service
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function showService($id)
    {
        // First, get the service with its relationships
        $service = Service::with(['departments', 'type', 'category'])
            ->where('is_featured', true)
            ->findOrFail($id);

        // Get related services from the same departments or category
        $relatedServices = Service::where('id', '!=', $id)
            ->where('is_featured', true)
            ->where(function($query) use ($service) {
                $query->whereHas('departments', function($q) use ($service) {
                    $q->whereIn('departments.id', $service->departments->pluck('id'));
                })->orWhere('category_id', $service->category_id);
            })
            ->with(['departments', 'category'])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // If not enough related services, get any visible services
        if ($relatedServices->count() < 3) {
            $additionalServices = Service::where('id', '!=', $id)
                ->where('is_featured', true)
                ->whereNotIn('id', $relatedServices->pluck('id'))
                ->with(['departments', 'category'])
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
            // 'staff' => function($query) {
            //     return $query->orderBy('name', 'asc');
            // }
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

    public function verifyMembership()
    {
        // For GET requests or after POST processing, return the Inertia view
        return Inertia::render('Frontend/VerifyMembership');
    }

    public function verifyMembershipRequest(Request $request)
    {
        $validated = $request->validate([
            'national_id' => 'required|string|size:8|exists:citizens,national_identification_number',
        ]);

        $national_id = $validated['national_id'];

        $citizen = Citizen::where('national_identification_number', $national_id)
            ->with([
                'county:id,name',
                'constituency:id,name',
                'ward:id,name',
                'user:id,name,email',
            ])
            ->first();

        if (!$citizen) {
            return response()->json([
                'success' => false,
                'message' => 'No member found with the provided National ID. Please check the number and try again.',
            ], 404);
        }

        // Update last verified timestamp
        $citizen->update([
            'last_verified_at' => now(),
            'verified_by' => $request->user() ? $request->user()->id : null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $citizen->user ? $citizen->user->name : 'N/A',
                'email' => $citizen->user->email ?? 'N/A',
                'national_id' => $citizen->national_identification_number,
                'registration_number' => $citizen->uuid,
                'status' => Citizen::getStatusOptions()[($citizen->_status)] ?? 'Unknown',
                'status_code' => $citizen->_status,
                'county' => $citizen->county->name ?? 'N/A',
                'constituency' => $citizen->constituency->name ?? 'N/A',
                'ward' => $citizen->ward->name ?? 'N/A',
                'registration_date' => $citizen->created_at->format('d/m/Y'),
                'last_verified' => $citizen->last_verified_at ? $citizen->last_verified_at->format('d/m/Y H:i') : 'Just now',
            ]
        ]);
    }

    public function autoLogin(Request $request)
    {
        $request['telephone'] = phoneNumberPrefix($request->input('telephone'));

        $validated = $request->validate([
            'telephone' => 'required|string|telephone|exists:profiles,telephone',
        ]);

        $profile = Profile::where('telephone', $validated['telephone'])->with('user')->firstOrFail();

        \Auth::login($profile->user);

        return redirect()->route('dashboard');
    }

    private function getStatusText($status)
    {
        return match((int)$status) {
            Citizen::PENDING => 'Pending',
            Citizen::PROCESSING => 'Processing',
            Citizen::PROCESSED => 'Processed',
            Citizen::ACCEPTED => 'Active Member',
            Citizen::REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }

    public function processDonation(Request $request)
    {
        // dd($request->all());

        try {
            // Common validation rules for all donation types
            $rules = [
                'type' => 'required|in:monetary,in_kind,volunteer',
                'donor_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'terms' => 'required|accepted',
                'amount' => 'sometimes|numeric' // Sometimes required for all types
            ];

            // Add conditional validation based on donation type
            switch ($request->type) {
                case 'monetary':
                    $rules['amount'] = 'required|numeric|min:1';
                    $rules['is_recurring'] = 'required|boolean';
                    $rules['payment_method'] = 'required|in:mobile_money,card,bank,crypto';
                    
                    // Payment method specific validations
                    if ($request->payment_method === 'mobile_money') {
                        $rules['mobile_money_provider'] = 'required|in:mpesa,airtel,tkash,equity';
                        $rules['telephone_number'] = 'required|string|regex:/^\+?[0-9]{10,15}$/';
                    } elseif ($request->payment_method === 'card') {
                        $rules['card_last_four'] = 'required|string|size:4';
                        // Note: In production, never handle full card details directly
                        // This should be handled by a payment processor like Stripe
                    }
                    break;
                    
                case 'in_kind':
                    $rules['in_kind_type'] = 'required|string|max:255';
                    $rules['description'] = 'required|string';
                    $rules['estimated_value'] = 'nullable|numeric|min:0';
                    break;
                    
                case 'volunteer':
                    $rules['skills'] = 'required|string|max:500';
                    $rules['availability'] = 'required|string|in:weekdays,weekends,evenings,flexible';
                    $rules['notes'] = 'nullable|string|max:1000';
                    break;
            }

            // Validate the request
            $validated = $request->validate($rules);

            // Process the donation based on type
            $donation = null;
            
            switch ($request->type) {
                case 'monetary':
                    // Map mobile_money to mpesa for backward compatibility
                    if ($validated['payment_method'] === 'mobile_money') {
                        $validated['payment_method'] = $validated['mobile_money_provider'] ?? 'mpesa';
                        $validated['phone'] = $validated['telephone_number'] ?? $validated['phone'];
                    }
                    
                    $donation = $this->processMonetaryDonation($validated);
                    $message = 'Thank you for your monetary donation of KES ' . number_format($validated['amount'], 2) . '!';
                    if (($validated['is_recurring'] ?? false)) {
                        $message .= ' Your recurring donation has been set up successfully.';
                    }
                    break;
                    
                case 'in_kind':
                    $donation = $this->processInKindDonation($validated);
                    $message = 'Thank you for your in-kind donation! We appreciate your contribution of ' . 
                              $validated['in_kind_type'] . '.';
                    break;
                    
                case 'volunteer':
                    $donation = $this->processVolunteerDonation($validated);
                    $message = 'Thank you for volunteering with us! We appreciate your ' . 
                              $validated['skills'] . ' skills and will be in touch soon.';
                    break;
            }

            // Send confirmation email
            // $this->sendDonationConfirmation($donation, $validated['email']);

            return response()->json([
                'success' => true,
                'message' => $message,
                'donation' => $donation,
                'receipt_number' => $donation['reference'] ?? null,
                'next_steps' => $this->getNextSteps($request->type, $validated)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Donation processing failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your donation. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Process monetary donation
     */
    protected function processMonetaryDonation($data)
    {
        // Log the donation attempt
        \Log::info('Processing monetary donation', [
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'donor_email' => $data['email']
        ]);
        
        $status = 'pending';
        
        // Process based on payment method
        switch ($data['payment_method']) {
            case 'mpesa':
                // In a real app, this would call M-Pesa STK push
                // $mpesaResponse = $this->initiateMpesaSTKPush($data);
                $status = 'pending_mpesa_confirmation';
                $reference = 'DON-' . now()->format('Ymd') . '-' . strtoupper(\Str::random(6));
                break;
                
            case 'card':
                // In a real app, this would process via Stripe/PayPal
                // $payment = $this->processCardPayment($data);
                $status = 'processing';
                $reference = 'DON-' . now()->format('Ymd') . '-' . strtoupper(\Str::random(6));
                break;
                
            case 'bank':
                // Provide bank transfer details
                $status = 'awaiting_transfer';
                $reference = 'DON-' . now()->format('Ymd') . '-' . strtoupper(\Str::random(6));
                break;
                
            case 'crypto':
                // Provide crypto wallet address
                $status = 'awaiting_payment';
                $reference = 'DON-' . now()->format('Ymd') . '-' . strtoupper(\Str::random(6));
                break;
        }
        
        // Save to database
        // $donation = Donation::create([
        //     'reference' => $reference,
        //     'donor_name' => $data['donor_name'],
        //     'email' => $data['email'],
        //     'phone' => $data['phone'],
        //     'amount' => $data['amount'],
        //     'payment_method' => $data['payment_method'],
        //     'is_recurring' => $data['is_recurring'] ?? false,
        //     'status' => $status,
        //     'metadata' => [
        //         'mpesa_phone' => $data['mpesa_phone'] ?? null,
        //         'card_last_four' => $data['card_last_four'] ?? null
        //     ]
        // ]);
        
        return [
            'type' => 'monetary',
            'amount' => $data['amount'],
            'currency' => 'KES',
            'payment_method' => $data['payment_method'],
            'is_recurring' => $data['is_recurring'] ?? false,
            'status' => $status,
            'reference' => $reference,
            'timestamp' => now()->toDateTimeString()
        ];
    }
    
    /**
     * Process in-kind donation
     */
    protected function processInKindDonation($data)
    {
        $reference = 'IKD-' . now()->format('Ymd') . '-' . strtoupper(\Str::random(6));
        
        // Log the in-kind donation
        \Log::info('Processing in-kind donation', [
            'type' => $data['in_kind_type'],
            'donor' => $data['email'],
            'reference' => $reference
        ]);
        
        // Save to database
        // $donation = InKindDonation::create([
        //     'reference' => $reference,
        //     'donor_name' => $data['donor_name'],
        //     'email' => $data['email'],
        //     'phone' => $data['phone'],
        //     'type' => $data['in_kind_type'],
        //     'description' => $data['description'],
        //     'estimated_value' => $data['estimated_value'] ?? null,
        //     'status' => 'received',
        // ]);
        
        return [
            'type' => 'in_kind',
            'in_kind_type' => $data['in_kind_type'],
            'description' => $data['description'],
            'estimated_value' => $data['estimated_value'] ?? null,
            'status' => 'received',
            'reference' => $reference,
            'timestamp' => now()->toDateTimeString()
        ];
    }
    
    /**
     * Process volunteer signup
     */
    protected function processVolunteerDonation($data)
    {
        $reference = 'VOL-' . now()->format('Ymd') . '-' . strtoupper(\Str::random(6));
        
        // Log the volunteer signup
        \Log::info('Processing volunteer signup', [
            'reference' => $reference,
            'email' => $data['email'],
            'skills' => $data['skills']
        ]);
        
        // Save to database
        // $volunteer = Volunteer::create([
        //     'reference' => $reference,
        //     'name' => $data['donor_name'],
        //     'email' => $data['email'],
        //     'phone' => $data['phone'],
        //     'skills' => $data['skills'],
        //     'availability' => $data['availability'],
        //     'notes' => $data['notes'] ?? null,
        //     'status' => 'pending_review'
        // ]);
        
        return [
            'type' => 'volunteer',
            'skills' => $data['skills'],
            'availability' => $data['availability'],
            'status' => 'pending_review',
            'reference' => $reference,
            'timestamp' => now()->toDateTimeString()
        ];
    }
    
    /**
     * Get next steps based on donation type
     */
    protected function getNextSteps($type, $data)
    {
        switch ($type) {
            case 'monetary':
                switch ($data['payment_method']) {
                    case 'mpesa':
                        return [
                            'Check your phone to complete the M-Pesa payment',
                            'You will receive a confirmation message once payment is received'
                        ];
                    case 'card':
                        return [
                            'Your card payment is being processed',
                            'You will receive an email receipt shortly'
                        ];
                    case 'bank':
                        return [
                            'Please transfer to our bank account',
                            'Account Name: Forward Kenya Party',
                            'Bank: [Bank Name]',
                            'Account: [Account Number]',
                            'Branch: [Branch Name]',
                            'Use reference: ' . ($data['email'] ?? '')
                        ];
                    case 'crypto':
                        return [
                            'Send your crypto to:',
                            'Bitcoin: [BTC Address]',
                            'Ethereum: [ETH Address]',
                            'Please include this reference in your transaction: ' . ($data['email'] ?? '')
                        ];
                }
                break;
                
            case 'in_kind':
                return [
                    'Our team will contact you within 2 business days',
                    'Please have your ' . ($data['in_kind_type'] ?? 'donation') . ' ready for collection/delivery'
                ];
                
            case 'volunteer':
                return [
                    'Our volunteer coordinator will contact you within 3 business days',
                    'Please check your email for next steps',
                    'Be ready to provide any additional information about your skills'
                ];
        }
        
        return [];
    }
}
