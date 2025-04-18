<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'start',
        'end',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

}
