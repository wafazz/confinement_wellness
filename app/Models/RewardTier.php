<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardTier extends Model
{
    protected $fillable = [
        'title', 'min_points', 'reward_description', 'month', 'status',
    ];
}
