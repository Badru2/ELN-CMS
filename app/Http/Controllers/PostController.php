<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::paginate(10);

        return view('livewire.pages.posts.index', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::find($id);

        return view("livewire.pages.posts.show", compact("post"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        // save image on public folder
        $image = $request->file('thumbnail');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $filename);

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'thumbnail' => $filename,
        ]);

        return redirect()->route('posts.index');
    }

    public function edit($id)
    {
        $post = Post::find($id);

        return view("livewire.pages.posts.edit", compact("post"));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post = Post::find($id);

        $filename = $post->thumbnail;

        if ($request->hasFile('thumbnail')) {
            // save image on public folder
            $image = $request->file('thumbnail');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $filename);
        }

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'thumbnail' => $filename,
        ]);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();
    }

    public function restore($id)
    {
        $post = Post::withTrashed()->find($id);
        $post->restore();
    }

    public function publish($id)
    {
        $post = Post::find($id);
        $post->update(['is_published' => true]);

        return redirect()->back();
    }
}
