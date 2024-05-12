<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $fillable =[
        'name' ,
        'brith_date' ,
        'phone',
        'instructor_image' ,
        'address',
        'bio',
        'user_id',
    ];

    protected $dates=['deleted_at'];

    /**
     * Get the user that owns the Instructor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->where('type','1');
    }


    /**
     * Get all of the courses for the Instructor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }
}
