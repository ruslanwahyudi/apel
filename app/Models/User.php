<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'firebase_uid',
        'password',
        'role',
        'role_id',
        'status',
        'email_verified_at',
        'fcm_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'firebase_uid'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Roles_type::class, 'role_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notifications::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function dusun()
    {
        return $this->belongsTo(Dusun::class);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }

    /**
     * Override notification reset password default
     */
    public function sendPasswordResetNotification($token)
    {
        $url = config('app.url') . '/reset-password?token=' . $token . '&email=' . $this->email;
        
        $this->notify(new \App\Notifications\ResetPasswordNotification($url));
    }

    // Tambahkan mutator jika diperlukan
    public function setFcmTokenAttribute($value)
    {
        \Log::info('Setting FCM token', [
            'user_id' => $this->id ?? 'new user',
            'old_value' => $this->attributes['fcm_token'] ?? null,
            'new_value' => $value
        ]);
        
        // Jangan set jika value null atau empty string
        if (!is_null($value) && $value !== '') {
            $this->attributes['fcm_token'] = $value;
        }
    }

    public function getFcmTokenAttribute($value)
    {
        return $value ?? null;
    }

    // Tambahkan observer events untuk debugging
    protected static function booted()
    {
        static::creating(function ($user) {
            \Log::info('Creating user:', $user->getAttributes());
        });

        static::created(function ($user) {
            \Log::info('User created:', $user->getAttributes());
        });
    }
}
