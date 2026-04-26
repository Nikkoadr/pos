<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        // 1. Gate khusus Admin
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        // 2. Gate khusus Karyawan
        Gate::define('isKaryawan', function (User $user) {
            return $user->role === 'karyawan';
        });

        // 3. Trik Superadmin (Otomatis lolos semua Gate)
        // Gate::before(function ($user, $ability) {
        //     return $user->role === 'admin' ? true : null;
        // });
    }
}
