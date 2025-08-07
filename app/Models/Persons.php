<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Persons extends Model
{
    protected $fillable = [
        'title',
        'first_name',
        'initial',
        'last_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Title accessor/mutator (basic passthrough)
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => $value,
        );
    }

    /**
     * First name accessor/mutator
     * - Stores in lowercase
     * - Returns with first letter capitalized
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? ucfirst($value) : null,
            set: fn (?string $value) => $value ? strtolower($value) : null,
        );
    }

    /**
     * Initial accessor/mutator
     * - Stores in uppercase
     * - Returns as uppercase
     */
    protected function initial(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? strtoupper($value) : null,
            set: fn (?string $value) => $value ? strtoupper($value) : null,
        );
    }

    /**
     * Last name accessor/mutator
     * - Stores in lowercase
     * - Returns with first letter capitalized
     */
    protected function lastName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }
}
