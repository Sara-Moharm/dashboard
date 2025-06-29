<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles,HasApiTokens, HasFactory, Notifiable, CanResetPassword, LogsActivity, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'phone_number',
        'password',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isCustomer()
    {
        return $this->hasRole('customer');
    }

    public function isKitchenStaff()
    {
        return $this->hasRole('kitchen_staff');
    }

    public function isDelivery()
    {
        return $this->hasRole('delivery');
    }

    public function isOperationalStaff()
    {
        return $this->hasRole('delivery') || $this->hasRole('kitchen_staff');
    }

     public function isManager()
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin');
    }

    public function isStaff()
    {
        return $this->isManager() || $this->isOperationalStaff();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fname', 'lname', 'email', 'phone_number', 'role', 'is_active']) 
            ->useLogName('user')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // Delete related customer or staff records
            if ($user->isCustomer()) {
                $user->customer()->delete();
            } elseif ($user->isStaff()) {
                $user->staff()->delete();
            }
        });

        static::restoring(function ($user) {
            // Restore related customer or staff records
            if ($user->isCustomer()) {
                $user->customer()->restore();
            } elseif ($user->isStaff()) {
                $user->staff()->restore();
            }
        });
    }
}
