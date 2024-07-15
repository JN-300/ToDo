<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
use App\Models\Traits\AddOwnerTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

    public function scopeForUserIds(\Illuminate\Contracts\Database\Eloquent\Builder $query, int ...$userIds)
    {
        $query->whereIn('owner_id', $userIds ?? null);
    }


    public function scopeForProjectIds(\Illuminate\Contracts\Database\Eloquent\Builder $query, string ...$projectIds)
    {
        $projectIds = Collect($projectIds)
            ->filter(fn(string $projectId) => Str::isUuid($projectId))
            ->toArray()
        ;
        $query->whereIn('project_id', $projectIds ?? null);
    }
    public function scopeByStatus(\Illuminate\Contracts\Database\Eloquent\Builder $query, TaskStatusEnum ...$status){
        $query->whereIn('status', $status);
    }
    public function scopeNotByStatus(\Illuminate\Contracts\Database\Eloquent\Builder $query, TaskStatusEnum ...$status){
        $query->whereNotIn('status', $status);
    }

    public function scopeOverdue(\Illuminate\Contracts\Database\Eloquent\Builder $query, bool $overdue = true) {
        $query->where('deadline', '<=', Carbon::now());
    }



    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
