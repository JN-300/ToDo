<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasUuids, HasFactory;

    const SCOPE_ORDER = 'baseorder';
    protected $fillable  = [
        'title',
        'description',
        'status',
        'deadline'
    ];





    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(self::SCOPE_ORDER, function (Builder $builder) {
            $builder->orderBy('created_at', 'DESC');
        });
    }
}
