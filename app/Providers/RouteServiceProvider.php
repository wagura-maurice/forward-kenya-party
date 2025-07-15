<?php

namespace App\Providers;

use App\Models\Service;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseServiceProvider;

class RouteServiceProvider extends BaseServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map($router = null)
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapDynamicRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    public function boot(): void
    {
        parent::boot();
        $this->configureRateLimiting();
        $this->map();
    }

    /**
     * Register dynamic routes for departments and services
     */
    /**
     * Register dynamic routes for departments and services
     * 
     * @return void
     */
    protected function mapDynamicRoutes(): void
    {
        // Only register dynamic routes when not in console or when running route:list
        if ($this->app->runningInConsole() && !$this->app->runningUnitTests() && !$this->app->runningInConsole('route:list')) {
            return;
        }

        Route::middleware(['web'])
            ->group(function () {
                try {
                    // If we're in console and not running route:list, skip database queries
                    if ($this->app->runningInConsole() && !$this->app->runningInConsole('route:list')) {
                        return;
                    }

                    if (!Schema::hasTable('departments')) {
                        Log::error('Departments table does not exist');
                        return;
                    }


                    // Get all active departments with their active services
                    $departments = cache()->remember('active_departments_with_services', 3600, function () {
                        return Department::where('_status', Department::ACTIVE)
                            ->where('is_featured', true)
                            ->with(['services' => function($query) {
                                $query->where('_status', Service::ACTIVE);
                            }])
                            ->orderBy('name')
                            ->get();
                    });

                    // Register department routes
                    Route::prefix('platform/department')
                        ->name('platform.department.')
                        ->group(function () use ($departments) {
                            // Single department route with parameter constraint
                            Route::get('{department:slug}/show', [\App\Http\Controllers\Platform\DepartmentController::class, 'show'])
                                ->where('department', '^[a-z0-9-]+$')
                                ->name('show');
                        });

                    // Register service routes
                    Route::prefix('platform/service')
                        ->name('platform.service.')
                        ->group(function () use ($departments) {
                            // Single service route with parameter constraint
                            Route::get('{service:slug}/show', [\App\Http\Controllers\Platform\ServiceController::class, 'show'])
                                ->where('service', '^[a-z0-9-]+$')
                                ->name('show');
                        });

                    // Log registered routes for debugging
                    $departmentSlugs = $departments->pluck('slug')->toArray();
                    Log::info('Registered department routes:', [
                        'count' => count($departmentSlugs),
                        'slugs' => $departmentSlugs
                    ]);

                } catch (\Exception $e) {
                    Log::error('Error registering dynamic routes: ' . $e->getMessage());
                }
            });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
