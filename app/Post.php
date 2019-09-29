<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{

    protected $guarded = [];
    public $timestamps = true;

    public function owner() {
        return $this->belongsTo(User::class);
    }

    public function images() {
        return $this->morphToMany(Image::class, 'imageable');
    }

    public  function scopeGetProfileImages($query, Request $request) {
        $query = $query->with('owner');

        $query->when($request->orderType === 'new', function ($q) use ($request) {
            $q->orderBy('created_at', $request->order ?? 'desc');
        });

        $query->when($request->orderType === 'popular', function ($q) {
            $q->orderBy('views', $request->order ?? 'desc');
        });

        return $query;
    }

    public static function saveOne(Request $request) {
        $files = [];

        foreach ($request->allFiles() as $file) {

            $name = Storage::disk('DO_SPACE')->putFile('images', $file, 'public');

            $files[] = [
                'path' => $name,
            ];
        }

        $postModel = self::updateOrCreate([
            'id' => $request->id,
        ], [
            'owner_id' => $request->user()->id,
            'comment' => $request->comment
        ]);

        $postModel->images()->createMany($files);

        if (!empty($request->deleted_images)){

            foreach (Image::whereIn('id', json_decode($request->deleted_images, true))->get() as $imageModel) {
                $imageModel->delete();
            }
        }

    }

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (Post $post) {
            foreach ($post->images as $imageModel) {
                $imageModel->delete();
            }
        });
    }

}
