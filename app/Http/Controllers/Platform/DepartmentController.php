<?php

namespace App\Http\Controllers\Platform;

use Inertia\Inertia;
use App\Models\Service;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    /**
     * Show the department dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Department $department, Request $request)
    {
        // Eager load the active services
        $department->load(['services' => function($query) {
            $query->where('_status', Service::ACTIVE);
        }]);

        // Verify department is active and visible
        if ($department->_status !== Department::ACTIVE || !$department->is_featured) {
            abort(404, 'Department not found or inactive');
        }

        // Cache the department with its services
        $department = cache()->remember("department.{$department->slug}", 3600, function () use ($department) {
            return $department->load(['services' => function($query) {
                $query->where('_status', Service::ACTIVE);
            }]);
        });

        return Inertia::render('Platform/Department/Show', [
            'department' => $department,
            'services' => $department->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,
                    'description' => $service->description,
                    'url' => route('platform.service.show', ['service' => $service->slug]),
                ];
            }),
            'breadcrumbs' => [
                [
                    'name' => $department->name,
                    'url' => route('platform.department.show', ['department' => $department->slug]),
                    'current' => true,
                ],
            ],
        ]);
    }
}
