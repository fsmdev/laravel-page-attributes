<?php

namespace Fsmdev\LaravelPageAttributes\Models;

use Illuminate\Database\Eloquent\Model;

class PageAttribute extends Model
{
    protected $fillable = [
        'context',
        'language',
        'name',
        'value',
    ];
}
