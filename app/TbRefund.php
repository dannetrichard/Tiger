<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TbRefund extends Model
{
    protected $guarded = [];
    protected $casts = [
        'express_list' => 'array',
    ];
}
