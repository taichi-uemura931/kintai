<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'status_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function stampCorrectionRequests()
    {
        return $this->hasMany(StampCorrectionRequest::class);
    }
}
