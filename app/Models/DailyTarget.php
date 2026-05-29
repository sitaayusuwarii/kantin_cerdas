<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTarget extends Model
{
    protected $fillable = ['date', 'target_amount'];
}