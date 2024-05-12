<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;


class Admin extends Model
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $fillable = [
        'name',
        'brith_date',
        'phone',
        'user_id',
    ];

    protected $dates=['deleted_at'];

    /**
     * Get the user that owns the Admin
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->where('type','2');
    }
}
