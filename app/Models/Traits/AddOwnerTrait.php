<?php

namespace App\Models\Traits;

use App\Exceptions\OwnerException;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait AddOwnerTrait
{

    public static function bootAddOwnerTrait()
    {
        static::creating(function ($model) {
           $owner_id = Auth::user()->id ?? $model->owner_id ?? null;
           throw_if(!$owner_id, new OwnerException('must be logged in to create this model'));
           $model->owner_id = $owner_id;
        });
    }

    public function owner():?BelongsTo
    {
        if ($this instanceof Model) {
            return $this->belongsTo(User::class);
        }
        return null;
    }

}
