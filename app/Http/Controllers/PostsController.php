<?php

namespace App\Http\Controllers;

use App\Http\Resources\Posts\PostResource;
use App\Http\Resources\Posts\PostsCollection;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{

    public function save(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'exists:posts',
            'comment' => 'max:300',
        ]);
        $success = false;

        if (!$validation->fails()) {
            $success = true;
            Post::saveOne($request);
        }

        return $this->success($success);
    }

    public function getList(Request $request)
    {
        $images = Post::getProfileImages($request)->paginate(20, '*', null, $request->page ?? 1);

        return new PostsCollection($images);
    }

    public function getOne(Request $request)
    {
        $image = Post::with('owner')->find($request->id);

        return new PostResource($image);
    }

    public function delete(Request $request)
    {
        $success = (boolean)Post::destroy($request->id);

        return $this->success($success);
    }


}
