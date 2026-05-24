<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable  implements JWTSubject 
{
    use  HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp',
        'otp_expires_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
          'otp',
    ];
 protected $casts = [
        'otp_expires_at'    => 'datetime',
        'email_verified_at' => 'datetime',
    ];
     public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
