<?php

namespace App\Models;

use App\Models\Traits\AddOwnerTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasUuids, HasFactory, AddOwnerTrait;

    const SCOPE_ORDER = 'baseorder';
    const SCOPE_AUTH = 'only_my_tasks';
    protected $fillable  = [
        'title',
        'description',
        'status',
        'deadline',
        'project_id'
    ];





    protected static function boot()
    {
        parent::boot();
//        static::addGlobalScope(self::SCOPE_AUTH, function(Builder $builder) {
//            $builder->where('owner_id', Auth::user()->id ?? null);
//        });
        static::addGlobalScope(self::SCOPE_ORDER, function (Builder $builder) {
            $builder->orderBy('created_at', 'DESC');
        });
    }


    public function scopeForUser(\Illuminate\Contracts\Database\Eloquent\Builder $query, User $user)
    {
        $query->where('owner_id', $user->id ?? null);
    }



    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
