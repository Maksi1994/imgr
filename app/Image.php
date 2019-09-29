<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    public $timestamps = true;
    protected $guarded = [];


    public function posts() {
        return $this->morphedByMany(Post::class, 'imageable');
    }


    public static function boot ()
    {
        parent::boot();

        self::deleting(function (Image $image) {
            Storage::disk('DO_SPACE')->delete($image->path);
        });
    }


}
