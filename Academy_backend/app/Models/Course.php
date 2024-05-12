<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;


class Course extends Model
{
    use HasApiTokens, HasFactory,SoftDeletes;

    protected $fillable = [
        'title' ,
        'description' ,
        'course_image',
        'instructor_id',
        'start_at',
        'end_at',
        'status',
    ];

    protected $dates=['deleted_at'];


    /**
     * The roles that belong to the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Studente::class, 'course_student', 'course_id', 'student_id');
    }


    /**
     * Get the instructor that owns the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
}
