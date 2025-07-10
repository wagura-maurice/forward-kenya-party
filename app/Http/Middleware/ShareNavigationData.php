<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Inertia;
use App\Models\Service;
use App\Models\Department;
use Illuminate\Support\Str;

class ShareNavigationData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!app()->runningInConsole() && !app()->runningUnitTests()) {
            $navigation = cache()->remember('navigation_data', 3600, function () {
                return [
                    'departments' => Department::where('_status', Department::ACTIVE)
                        ->where('is_featured', true)
                        ->with(['services' => function($query) {
                            $query->where('_status', Service::ACTIVE);
                        }])
                        ->orderBy('name')
                        ->get()
                        ->map(function($department) {
                            return [
                                'id' => $department->id,
                                'name' => $department->name,
                                'slug' => $department->slug,
                                'url' => route('platform.department.' . $department->slug . '.show'),
                                'services' => $department->services->map(function($service) use ($department) {
                                    return [
                                        'id' => $service->id,
                                        'name' => $service->name,
                                        'slug' => Str::slug($service->name),
                                        'url' => route('platform.department.' . $department->slug . '.service.' . $service->slug . '.show'),
                                    ];
                                })
                            ];
                        })
                ];
            });

            // Share with Inertia
            Inertia::share('navigation', $navigation);
        }

        return $next($request);
    }
}
