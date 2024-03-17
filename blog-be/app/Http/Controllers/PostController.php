<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $post = Post::all();
        return response()->json(['status'=>200, 'data'=>$post]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|max:2048'
        ]);

        if($validator->fails()){
            return response()->json(['status'=>'error', 'message'=> $validator->errors()], 422);
        }

        $post = new Post();
        $post->title = $request->title;
        $post->content = $request->content;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if ($image->isValid()) {
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $path = public_path('images/');
                $image->move($path, $imageName);
                $post->image = $imageName;
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid image file.'], 422);
            }
        }

        $post->save();
        return response()->json(['status'=>200, 'message'=>'Data Saved Successfully', 'data'=>$post]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findorFail($id);
        return response()->json(['status'=>200, 'data'=>$post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::firstOrFail($id);
        return response()->json(['status'=>200, 'data'=>$post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|max:2048'
        ]);

        if($validator->fails()){
            return response()->json(['status'=>'error', 'message'=> $validator->errors()], 422);
        }
        $post = Post::findOrFail($id);
        $post->title = $request->title;
        $post->content = $request->content;

        if($request->hasFile('image')){
            $imageName = time() . '.' . $request->image->extension();
            $path = public_path('images/');
            $request->image->move($path, $imageName);
            $post->image = $imageName;
        }

        $post->save();
        return response()->json(['status'=>200, 'message'=>'Data Updated Successfully', 'data'=>$post]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if ($post->image) {
            Storage::delete($post->image);
        }

        $post->delete();

        return response()->json(['status'=>204, 'message'=>'Data Deleted Successfully', 'data'=>$post]);
    }
}