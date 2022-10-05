<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * Validate request parameter
         */

        $this->validate($request, [
            'title' => 'required|min:5',
            'image' => 'required|image|mimes:jpg,png',
            'content' => 'required|min:10',
        ]);

        /**
         * Hanlde upload image
         */

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        /**
         * Store request to database
         */

        Post::create([
            'title' => $request->title,
            'image' => $image->hashName(),
            'content' => $request->content,
        ]);
        
        return redirect()->route('posts.index')->with(['success' => 'Create data success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        /**
         * Validate request parameter
         */

        $this->validate($request, [
            'title' => 'required|min:5',
            'image' => 'image|mimes:jpg,png',
            'content' => 'required|min:10',
        ]);

        /**
         * Check if file is exist
         */

        $updateData = [];

        if ($request->hasFile('image')) {

            /**
             * Hanlde upload image
             */
    
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            /**
             * Remove old file
             */

            Storage::delete('public/posts/' . $post->image);

            $updateData = [
                'title' => $request->title,
                'image' => $request->image,
                'content' => $request->content
            ];
        } else {    
            $updateData = [
                'title' => $request->title,
                'content' => $request->content
            ];
        }
    
        /**
         * Update data to database
         */

        $post->update($updateData);
        
        return redirect()->route('posts.index')->with(['success' => 'Update data success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        /**
         * Remove old file
         */

        Storage::delete('public/posts/' . $post->image);

        /**
         * Delete data on database
         */

        $post->delete();

        return redirect()->route('posts.index')->with(['success' => 'Delete data success']);
    }
}
