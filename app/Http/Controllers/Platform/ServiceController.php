<?php

namespace App\Http\Controllers\Platform;

use Inertia\Inertia;
use App\Models\Service;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    /**
     * Show the service details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Service $service, Request $request)
    {
        // Eager load the departments and verify at least one is active and visible
        $service->load(['departments' => function($query) {
            $query->where('_status', Department::ACTIVE)
                  ->where('is_featured', true);
        }]);

        // Verify service is active
        if ($service->_status !== Service::ACTIVE) {
            abort(404, 'Service not found or inactive');
        }

        // Get the first active department for breadcrumbs and navigation
        $department = $service->departments->first();
        
        if (!$department) {
            abort(404, 'No active departments found for this service');
        }

        // Cache the service with its departments
        $service = cache()->remember("service.{$service->slug}", 3600, function () use ($service) {
            return $service->load(['departments' => function($query) {
                $query->where('_status', Department::ACTIVE)
                      ->where('is_featured', true)
                      ->select(['id', 'name', 'slug', 'description']);
            }]);
        });

        return Inertia::render('Platform/Service/Show', [
            'department' => $department,
            'departments' => $service->departments,
            'service' => $service,
            'breadcrumbs' => [
                [
                    'name' => $department->name,
                    'url' => route('platform.department.show', ['department' => $department->slug]),
                    'current' => false,
                ],
                [
                    'name' => $service->name,
                    'url' => route('platform.service.show', ['service' => $service->slug]),
                    'current' => true,
                ],
            ],
            'currentDepartment' => $department->slug,
        ]);
    }
}
