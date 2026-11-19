<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::Owner;
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function isKasir(): bool
    {
        return $this->role === UserRole::Kasir;
    }

    /**
     * Orders processed by this user (as cashier).
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'cashier_id');
    }

    /**
     * Order deletion logs recorded by this user.
     */
    public function orderDeletionLogs(): HasMany
    {
        return $this->hasMany(OrderDeletionLog::class, 'deleted_by');
    }

    /**
     * Stock opnames conducted by this user.
     */
    public function stockOpnames(): HasMany
    {
        return $this->hasMany(StockOpname::class, 'user_id');
    }
}
