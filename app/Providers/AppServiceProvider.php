<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Gate for Financial & HPP Reports (Kasir forbidden)
        Gate::define('access-reports', function (User $user): bool {
            return $user->isOwner() || $user->isManager();
        });

        // Gate for Cafe Settings (Kasir forbidden)
        Gate::define('access-settings', function (User $user): bool {
            return $user->isOwner() || $user->isManager();
        });

        // Gate for modifying user roles (Manager cannot alter Owner, cannot grant Owner role)
        Gate::define('edit-user-role', function (User $user, User $targetUser, ?string $newRole = null): bool {
            if ($targetUser->isOwner() && ! $user->isOwner()) {
                return false;
            }

            if ($newRole === UserRole::Owner->value && ! $user->isOwner()) {
                return false;
            }

            return $user->isOwner() || ($user->isManager() && ! $targetUser->isOwner());
        });

        // Gate for deleting audit logs (Manager & Kasir cannot delete audit logs)
        Gate::define('delete-audit-log', function (User $user): bool {
            return $user->isOwner();
        });
    }
}
