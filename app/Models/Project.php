<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, HasUuids;

    const SCOPE_ORDER = 'order_by_title';

    protected $fillable = [
        'title',
    ];




    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(self::SCOPE_ORDER, function (Builder $builder) {
            $builder->orderBy('title', 'ASC');
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks():HasMany
    {
        return $this->hasMany(Task::class);
    }



}
