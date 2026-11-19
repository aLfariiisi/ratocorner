<?php

namespace App\Models;

use Database\Factories\TableFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable([
    'table_number',
    'name',
    'capacity',
    'location',
    'status',
    'qr_code_token',
    'qr_code_path',
    'is_active',
])]
class Table extends Model
{
    /** @use HasFactory<TableFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Table $table) {
            if (empty($table->qr_code_token)) {
                $table->qr_code_token = (string) Str::uuid();
            }
        });
    }

    /**
     * Orders placed at this table.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Current active/pending order at this table.
     */
    public function activeOrder(): HasOne
    {
        return $this->hasOne(Order::class)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latestOfMany();
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function available(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }
}
