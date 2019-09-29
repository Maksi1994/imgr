<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = true;
    protected $guarded = [];

    public function tabable()
    {
        return $this->morphedByMany('');
    }
}
