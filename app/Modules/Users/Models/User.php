<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Users\Models;

use FI\Events\UserCreated;
use FI\Events\UserDeleted;
use FI\Traits\Sortable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, Sortable;

    protected $table = 'users';

    protected $guarded = ['id', 'password', 'password_confirmation'];

    protected $hidden = ['password', 'remember_token', 'api_public_key', 'api_secret_key'];

    protected $sortable = ['name', 'email'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($user)
        {
            event(new UserCreated($user));
        });

        static::deleted(function ($user)
        {
            event(new UserDeleted($user));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\UserCustom');
    }

    public function expenses()
    {
        return $this->hasMany('FI\Modules\Expenses\Models\Expense');
    }

    public function invoices()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\Invoice');
    }

    public function quotes()
    {
        return $this->hasMany('FI\Modules\Quotes\Models\Quote');
    }

    public function access_levels()
    {
        return $this->belongsTo('FI\Modules\AccessLevel\Models\AccessLevel', 'access_level');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getUserTypeAttribute()
    {
        return ($this->client_id) ? 'client' : 'admin';
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUserType($query, $userType)
    {
        if ($userType == 'client')
        {
            $query->where('client_id', '<>', 0);
        }
        elseif ($userType == 'admin')
        {
            $query->where('client_id', 0);
        }

        return $query;
    }
}